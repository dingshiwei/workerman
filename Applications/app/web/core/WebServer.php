<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace app\web\core;

use Workerman\Protocols\Http;
use Workerman\Worker;
use Workerman\Connection;

/**
 *  WebServer.
 */
class WebServer extends Worker
{
    /**
     * Virtual host to path mapping.
     *
     * @var array ['websktshop.ci123.com'=>'\Prints\web\apps']
     */
    protected $serverRoot = array();

    /**
     * Mime mapping.
     *
     * @var array
     */
    protected static $mimeTypeMap = array();


    /**
     * Used to save user OnWorkerStart callback settings.
     *
     * @var callback
     */
    protected $_onWorkerStart = null;

    /**
     * Add virtual host.
     *
     * @param string $domain
     * @param string $namespace
     * @return void
     */
    public function addRoot($domain, $namespace)
    {
        $this->serverRoot[$domain] = $namespace;
    }

    /**
     * Construct.
     *
     * @param string $socket_name
     * @param array  $context_option
     */
    public function __construct($socket_name, $context_option = array())
    {
        list(, $address) = explode(':', $socket_name, 2);
        parent::__construct('http:' . $address, $context_option);
        $this->name = 'WebServer';
    }

    /**
     * Run webserver instance.
     *
     * @see Workerman.Worker::run()
     */
    public function run()
    {
        $this->_onWorkerStart = $this->onWorkerStart;
        $this->onWorkerStart  = array($this, 'onWorkerStart');
        $this->onMessage      = array($this, 'onMessage');
        parent::run();
    }

    /**
     * Emit when process start.
     *
     * @throws \Exception
     */
    public function onWorkerStart()
    {
        if (empty($this->serverRoot)) {
            echo new \Exception('server root not set, please use WebServer::addRoot($domain, $root_path) to set server root path');
            exit(250);
        }

        // Init mimeMap.
        $this->initMimeTypeMap();

        // Try to emit onWorkerStart callback.
        if ($this->_onWorkerStart) {
            try {
                call_user_func($this->_onWorkerStart, $this);
            } catch (\Exception $e) {
                self::log($e);
                exit(250);
            } catch (\Error $e) {
                self::log($e);
                exit(250);
            }
        }
    }

    /**
     * Init mime map.
     *
     * @return void
     */
    public function initMimeTypeMap()
    {
        $mime_file = Http::getMimeTypesFile();
        if (!is_file($mime_file)) {
            $this->log("$mime_file mime.type file not fond");
            return;
        }
        $items = file($mime_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!is_array($items)) {
            $this->log("get $mime_file mime.type content fail");
            return;
        }
        foreach ($items as $content) {
            if (preg_match("/\s*(\S+)\s+(\S.+)/", $content, $match)) {
                $mime_type                      = $match[1];
                $workerman_file_extension_var   = $match[2];
                $workerman_file_extension_array = explode(' ', substr($workerman_file_extension_var, 0, -1));
                foreach ($workerman_file_extension_array as $workerman_file_extension) {
                    self::$mimeTypeMap[$workerman_file_extension] = $mime_type;
                }
            }
        }
    }

    /**
     * Emit when http message coming.
     *
     * @param Connection\TcpConnection $connection
     * @return void
     */
    public function onMessage($connection)
    {
        Output::ready();
        try {
            $server_name = URI::_get_server_name();
            if (!$server_name || !isset($this->serverRoot[$server_name])) {
                Output::close($connection, Output::_404);
                return;
            }
            $namespace = $this->serverRoot[$server_name];
            list($class, $method, $params) = URI::_set_request();
            Security::_sanitize_globals(); // 过滤 $_GET $_POST $_COOKIT
            if (empty($class) || (strtolower($class) === 'controller')) {
                Output::close($connection, Output::_404);
                return;
            }
            $class = $namespace . $class; // camel-case
            var_dump($class);
            if (!class_exists($class, TRUE) || $method[0] === '_') {
                Output::close($connection, Output::_404);
                return;
            }
            if (!in_array($method, get_class_methods($class), TRUE)) {
                Output::close($connection, Output::_404);
                return;
            }
            set_error_display();
            ob_start();
            $_SERVER['REMOTE_ADDR'] = $connection->getRemoteIp();
            $_SERVER['REMOTE_PORT'] = $connection->getRemotePort();
            try {
                $c = new $class();
                call_user_func_array(array(&$c, $method), $params);
            } catch (\Exception $e) {
                if ($e->getMessage() != 'jump_exit') {
                    echo $e;
                }
            }
            $buffer = ob_get_contents();
            @ob_end_clean();
            Output::close($connection, Output::_200, $buffer);
        } catch (\Exception $e) {
            $rf = new \ReflectionClass(Output::class);
            $constants = $rf->getConstants();
            if (isset($constants['_'.$e->getCode()])) {
                Output::close($connection, $constants['_' . $e->getCode()], $e->getMessage());
            } else {
                Output::close($connection, Output::_500);
            }
            return;
        } finally {
            unset($server_name, $namespace, $class, $method, $params, $buffers);
            unset($e, $c, $rf, $constants);
        }
    }

}
