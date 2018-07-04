<?php
namespace app\web\core;
use Workerman\Protocols\Http;
use Workerman\Connection\TcpConnection;
/**
 *
 * User: Tricolor
 * DateTime: 2017/10/23 17:05
 */
class Output
{
    private static $time = null;

    const _200= 200;
    const _400 = 400;
    const _404 = 404;
    const _500 = 500;
    const _503 = 503;

    /**
     * @param $connection TcpConnection
     * @param $http_code
     * @param null $output
     */
    public static function close(&$connection, $http_code, $output = null)
    {
        try {
            switch ($http_code) {
                case self::_200:
                    $connection->close($output);
                    $header = 'HTTP/1.1 200 OK';
                    break;
                case self::_400:
                    $output = isset($output) ? $output : '<h1>400 Bad Request</h1>';
                    $header = 'HTTP/1.1 400 Bad Request';
                    break;
                case self::_404:
                    $output = isset($output) ? $output : '<html><head><title>404 File not found</title></head><body><center><h3>404 Not Found</h3></center></body></html>';
                    $header = 'HTTP/1.1 404 Not Found';
                    break;
                case self::_503:
                    $output = isset($output) ? $output : '<h1>503 Service Unavailable</h1>';
                    $header = 'HTTP/1.1 503 Service Unavailable';
                    break;
                case self::_500:
                default:
                    $output = isset($output) ? $output : '<h1>500 Internal Server Error</h1>';
                    $header = 'HTTP/1.1 500 Internal Server Error';
                    break;
            }
            Http::header($header);
            $connection->close($output);
            $buffer_size = strlen($output);
            self::log($buffer_size, $http_code);
        } catch (\Exception $e) {
        } finally {
            unset($http_code, $output, $header, $buffer_size);
        }
    }

    /**
     * @param int $buffer_size
     * @param int $http_code
     */
    protected static function log(&$buffer_size, &$http_code)
    {
        $logs = array();
        foreach (array('HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR', 'REQUEST_METHOD', 'REQUEST_URI', 'SERVER_PROTOCOL') as $i) {
            array_push($logs, isset($_SERVER[$i]) ? $_SERVER[$i] : '-');
        }
        array_push($logs, $http_code, floor((microtime(true) - self::$time) * 1000), $buffer_size);
        foreach (array('HTTP_REFERER', 'HTTP_USER_AGENT') as $i) {
            array_push($logs, isset($_SERVER[$i]) ? $_SERVER[$i] : '-');
        }
        my_log('logs_http', $logs);
    }

    public static function ready()
    {
        self::$time = microtime(true);
    }

}