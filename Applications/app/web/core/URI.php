<?php
namespace app\web\core;
/**
 *
 * User: Tricolor
 * DateTime: 2017/10/23 13:27
 */
class URI
{
    private static $_permitted_uri_chars = 'a-z 0-9~%.:_\-';

    public static function _get_server_name()
    {
        return isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
    }

    public static function _set_request()
    {
        $uri = self::_parse_request_uri();
        try {
            $segments = self::_set_uri_string($uri);
        } catch (\Exception $e) {
            throw $e;
        }
        var_dump($segments);
        $class = ucfirst($segments[0]);
        $method = isset($segments[1]) ? $segments[1] : 'index';
        $params = array_slice($segments, 2);

        return array($class, $method, $params);
    }

    /**
     * @param $str
     * @return array
     * @throws \Exception
     */
    protected static function _set_uri_string($str)
    {
        $segments = array();
        // Filter out control characters and trim slashes
        $uri_string = trim(remove_invisible_characters($str, FALSE), '/');

        if ($uri_string !== '') {
            // Populate the segments array
            foreach (explode('/', trim($uri_string, '/')) as $val) {
                $val = trim($val);
                // Filter segments for security
                try {
                    self::filter_uri($val);
                } catch (\Exception $e) {
                    throw $e;
                }
                if ($val !== '') {
                    $segments[] = $val;
                }
            }
        }
        return $segments;
    }

    protected static function _parse_request_uri()
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            return '';
        }

        // parse_url() returns false if no host is present, but the path or query string
        // contains a colon followed by a number
        $uri = parse_url('http://dummy' . $_SERVER['REQUEST_URI']);
        $query = isset($uri['query']) ? $uri['query'] : '';
        $uri = isset($uri['path']) ? $uri['path'] : '';

        // This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
        // URI is found, and also fixes the QUERY_STRING server var and $_GET array.
        if (trim($uri, '/') === '' && strncmp($query, '/', 1) === 0) {
            $query = explode('?', $query, 2);
            $uri = $query[0];
            $_SERVER['QUERY_STRING'] = isset($query[1]) ? $query[1] : '';
        } else {
            $_SERVER['QUERY_STRING'] = $query;
        }

        parse_str($_SERVER['QUERY_STRING'], $_GET);

        if ($uri === '/' OR $uri === '') {
            return '/';
        }

        // Do some final cleaning of the URI and return it
        return self::_remove_relative_directory($uri);
    }

    protected static function _remove_relative_directory($uri)
    {
        $uris = array();
        $tok = strtok($uri, '/');
        while ($tok !== FALSE) {
            if ((!empty($tok) OR $tok === '0') && $tok !== '..') {
                $uris[] = $tok;
            }
            $tok = strtok('/');
        }

        return implode('/', $uris);
    }

    protected static function filter_uri(&$str)
    {
        if (!empty($str) && !empty(self::$_permitted_uri_chars) && !preg_match('/^[' . self::$_permitted_uri_chars . ']+$/i' . (UTF8_ENABLED ? 'u' : ''), $str)) {
            throw new \Exception('The URI you submitted has disallowed characters.', 400);
        }
    }

}