<?php
/**
 *
 * User: Tricolor
 * DateTime: 2017/10/18 19:13
 */
if ( ! function_exists('is_php'))
{
    /**
     * Determines if the current version of PHP is equal to or greater than the supplied value
     *
     * @param	string
     * @return	bool	TRUE if the current version is $version or higher
     */
    function is_php($version)
    {
        static $_is_php;
        $version = (string) $version;

        if ( ! isset($_is_php[$version]))
        {
            $_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
        }

        return $_is_php[$version];
    }
}
if ( ! function_exists('remove_invisible_characters')) {
    /**
     * Remove Invisible Characters
     *
     * This prevents sandwiching null characters
     * between ascii characters, like Java\0script.
     *
     * @param    string
     * @param    bool
     * @return    string
     */
    function remove_invisible_characters($str, $url_encoded = TRUE)
    {
        $non_displayables = array();

        // every control character except newline (dec 10),
        // carriage return (dec 13) and horizontal tab (dec 09)
        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/';    // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/';    // url encoded 16-31
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';    // 00-08, 11, 12, 14-31, 127

        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);

        return $str;
    }
}
if (!function_exists('normalize')) {
    function normalize($data)
    {
        if (null === $data || is_scalar($data)) {
            if (is_float($data)) {
                if (is_infinite($data)) {
                    return ($data > 0 ? '' : '-') . 'INF';
                }
                if (is_nan($data)) {
                    return 'NaN';
                }
            }
            return $data;
        }

        if (is_array($data)) {
            $normalized = array();

            $count = 1;
            foreach ($data as $key => $value) {
                if ($count++ >= 1000) {
                    $normalized['...'] = 'Over 1000 items (' . count($data) . ' total), aborting normalization';
                    break;
                }
                $normalized[$key] = normalize($value);
            }

            return $normalized;
        }

        if ($data instanceof \DateTime) {
            return $data->format($this->dateFormat);
        }

        if (is_object($data)) {
            // TODO 2.0 only check for Throwable
            if ($data instanceof Exception || (PHP_VERSION_ID > 70000 && $data instanceof \Throwable)) {
                return $this->normalizeException($data);
            }

            // non-serializable objects that implement __toString stringified
            if (method_exists($data, '__toString') && !$data instanceof \JsonSerializable) {
                $value = $data->__toString();
            } else {
                // the rest is json-serialized in some way
                $value = $this->toJson($data, true);
            }

            return sprintf("[object] (%s: %s)", get_class($data), $value);
        }

        if (is_resource($data)) {
            return sprintf('[resource] (%s)', get_resource_type($data));
        }

        return '[unknown(' . gettype($data) . ')]';
    }
}
if (!function_exists('toJson')) {
    function toJson($data, $ignoreErrors = false)
    {
        // the rest is json-serialized in some way
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return $ignoreErrors
                ? @json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                : json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        return $ignoreErrors
            ? @json_encode($data)
            : json_encode($data);
    }
}
if (!function_exists('convertToString')) {
    function convertToString($data)
    {
        if (null === $data || is_bool($data)) {
            return var_export($data, true);
        }

        if (is_scalar($data)) {
            return (string)$data;
        }

        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return toJson($data, true);
        }

        return str_replace('\\/', '/', @json_encode($data));
    }
}
if (!function_exists('replaceNewlines')) {
    function replaceNewlines($str, $allowInlineLineBreaks = false)
    {
        if ($allowInlineLineBreaks) {
            if (0 === strpos($str, '{')) {
                return str_replace(array('\r', '\n'), array("\r", "\n"), $str);
            }

            return $str;
        }

        return str_replace(array("\r\n", "\r", "\n"), ' ', $str);
    }
}

if (!function_exists('my_log')) {
    function my_log($short, $_ = null) {
        $path = '/tmp/' . trim(trim($short), "/");
        is_dir($path) OR @mkdir($path, 0774, true);
        if (is_writable($path)) {
            $args = func_get_args();
            array_shift($args);
            $content = "[".date("Y-m-d H:i:s")."] {$short}.DEBUG:  " . replaceNewlines(convertToString(normalize($args))) . "\n";
            file_put_contents("/tmp/{$short}/{$short}-".date('Y-m-d').'.log', $content, FILE_APPEND);
        }
    }
}