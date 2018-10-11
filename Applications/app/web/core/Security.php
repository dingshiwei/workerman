<?php
namespace app\web\core;
/**
 *
 * User: Tricolor
 * DateTime: 2017/10/23 13:22
 */
class Security
{
    public static function _sanitize_globals()
    {
        try {
            if (is_array($_GET)) {
                foreach ($_GET as $key => $val) {
                    $_GET[self::_clean_input_keys($key)] = self::_clean_input_data($val);
                }
            }
            if (is_array($_POST)) {
                foreach ($_POST as $key => $val) {
                    $_POST[self::_clean_input_keys($key)] = self::_clean_input_data($val);
                }
            }
            // Clean $_COOKIE Data
            if (is_array($_COOKIE)) {
                // Also get rid of specially treated cookies that might be set by a server
                // or silly application, that are of no use to a CI application anyway
                // but that when present will trip our 'Disallowed Key Characters' alarm
                // http://www.ietf.org/rfc/rfc2109.txt
                // note that the key names below are single quoted strings, and are not PHP variables
                unset(
                    $_COOKIE['$Version'],
                    $_COOKIE['$Path'],
                    $_COOKIE['$Domain']
                );

                foreach ($_COOKIE as $key => $val) {
                    if (($cookie_key = self::_clean_input_keys($key)) !== FALSE) {
                        $_COOKIE[$cookie_key] = self::_clean_input_data($val);
                    } else {
                        unset($_COOKIE[$key]);
                    }
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected static function _clean_input_keys($str, $fatal = TRUE)
    {
        if (!preg_match('/^[a-z0-9:_\/|-]+$/i', $str)) {
            if ($fatal === TRUE) {
                return FALSE;
            } else {
                throw new \Exception('Disallowed Key Characters.', 503);
            }
        }

        // Clean UTF-8 if supported
        if (UTF8_ENABLED === TRUE) {
            return self::clean_string($str);
        }

        return $str;
    }

    protected static function clean_string($str)
    {
        if (self::is_ascii($str) === FALSE) {
            if (MB_ENABLED) {
                $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
            } elseif (ICONV_ENABLED) {
                $str = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
            }
        }

        return $str;
    }

    protected static function is_ascii($str)
    {
        return (preg_match('/[^\x00-\x7F]/S', $str) === 0);
    }

    protected static function _clean_input_data($str)
    {
        try {
            if (is_array($str)) {
                $new_array = array();
                foreach (array_keys($str) as $key) {
                    $new_array[self::_clean_input_keys($key)] = self::_clean_input_data($str[$key]);
                }
                return $new_array;
            }

            /* We strip slashes if magic quotes is on to keep things consistent

               NOTE: In PHP 5.4 get_magic_quotes_gpc() will always return 0 and
                     it will probably not exist in future versions at all.
            */
            if (!is_php('5.4') && get_magic_quotes_gpc()) {
                $str = stripslashes($str);
            }

            // Clean UTF-8 if supported
            if (UTF8_ENABLED === TRUE) {
                $str = self::clean_string($str);
            }

            // Remove control characters
            $str = remove_invisible_characters($str, FALSE);

            return $str;
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
