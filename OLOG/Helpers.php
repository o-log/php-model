<?php

namespace OLOG;

class Helpers
{
    static public function implode_intval($glue, $pieces)
    {
        $intval_arr = array();

        foreach ($pieces as $val) {
            $intval_arr[] = intval($val);
        }

        return implode($glue, $intval_arr);
    }

    static public function assert($value, $message = "")
    {
        if ($value == false) {
            $backtrace_arr = debug_backtrace();

            if (is_array($backtrace_arr)) {
                if (count($backtrace_arr) > 0) {
                    $last_function_call_trace = $backtrace_arr[0];
                    $message = " [" . $last_function_call_trace['file'] . ":" . $last_function_call_trace['line'] . "] " . $message;
                }
            }

            $message_str = 'Assertion failed ' . $message;
            $message_str .= ' [' . $_SERVER['REQUEST_URI'] . ']';

            throw new \Exception($message_str);
        }
    }

    static public function getFullObjectId($obj)
    {
        if (!is_object($obj)) {
            return 'not_object';
        }

        $obj_id_parts = array();
        $obj_id_parts[] = get_class($obj);

        if (method_exists($obj, 'getId')) { // TODO: заменить на проверку интерфеса?
            $obj_id_parts[] = $obj->getId();
        }

        return implode('.', $obj_id_parts);
    }

    static public function cacheHeaders($seconds = 0)
    {
        if ($seconds) {
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $seconds) . ' GMT');
            header('Cache-Control: max-age=' . $seconds . ', public');
        } else {
            header('Expires: ' . gmdate('D, d M Y H:i:s', date('U') - 86400) . ' GMT');
            header('Cache-Control: no-cache');
        }

    }

    static public function uri_no_getform()
    {
        $request_uri = array_key_exists('REQUEST_URI', $_SERVER) ? $_SERVER['REQUEST_URI'] : '';
        $parts = explode('?', $request_uri);
        $uri_no_getform = $parts[0];
        return $uri_no_getform;
    }

    /**
     *
     * @param int $n Count of items
     * @param array $pluralForms 3 forms of word for plural
     * @return string Plural form of word for provided count
     */
    static function formatPlural($n, array $pluralForms)
    {
        if ($n % 10 == 1 && $n % 100 != 11) {
            return $pluralForms[0];
        }
        if ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20)) {
            return $pluralForms[1];
        }
        return $pluralForms[2];
    }


    /**
     * Returns array of slash separated url parts.
     * @return array Array of url parts.
     */
    static public function url_args()
    {
        $uri_no_getform = Helpers::uri_no_getform();

        // remove "/" at the beginning to avoid empty first arg and protect from uri without leading "/"

        if (substr($uri_no_getform, 0, 1) == '/')
            $uri_no_getform = substr($uri_no_getform, 1);

        $args = explode('/', $uri_no_getform);
        return $args;
    }

    /**
     * Returns requested url part.
     * @param int $index Index of requested url part.
     * @param string $default Default value - returned when requested url part missed.
     * @return string Requested url part or default value.
     */
    static public function url_arg($index, $default = '')
    {
        $args = self::url_args();

        if (isset($args[$index]))
            return $args[$index];

        return $default;
    }

    static public function check_plain($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
    }

    static public function exit404()
    {
        header("HTTP/1.0 404 Not Found");
        exit();
    }

    static public function exit429()
    {
        header("HTTP/1.0 429 Too Many Requests");
        exit();
    }

    static public function exit408()
    {
        header("HTTP/1.0 408 Request Timeout");
        exit();
    }

    static public function exit404If($exit_condition)
    {
        if (!$exit_condition) {
            return;
        }

        header("HTTP/1.0 404 Not Found");
        exit();
    }

    static public function exit403If($exit_condition)
    {
        if (!$exit_condition) {
            return;
        }

        header("HTTP/1.0 403 Forbidden");

        exit();
    }

    static public function exit403()
    {
        header("HTTP/1.0 403 Forbidden");
        exit();
    }

    static public function exit400If($exit_condition)
    {
        if (!$exit_condition) {
            return;
        }

        header("HTTP/1.0 400 Bad Request");
        exit();
    }

    static public function exit403IfWithMessage($exit_condition, $message = '')
    {
        if (!$exit_condition) {
            return;
        }

        header("HTTP/1.0 403 Forbidden");
        if ($message != '') {
            echo $message;
        }

        exit();
    }

    public static function exit405($allowed_methods_arr = array())
    {
        header("HTTP/1.1 405 Method Not Allowed");
        if(!empty($allowed_methods_arr)) {
            self::setAllowedMethodsHeaders($allowed_methods_arr);
        }

        exit();
    }

    static public function redirect301($url)
    {
        header("HTTP/1.0 301 Moved Permanently");
        header('Location: ' . $url);
        exit;
    }

    static public function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    public static function appendMessageInErrorLog($message)
    {
        error_log('Fatal error:' . $message);
    }

    public static function doJsonResponse($response_obj)
    {
        self::setAccessControlAllowOriginHeader();
        header('Content-Type: application/json; charset=utf-8');
        $json = json_encode($response_obj);
        if(json_last_error()) {
            $err = json_last_error_msg();
            self::appendMessageInErrorLog($err);
        }

        echo $json;
    }

    public static function setAllowedMethodsHeaders($allowed_methods_arr)
    {
        self::assert(is_array($allowed_methods_arr));
        self::setAccessControlAllowOriginHeader();

        header('Access-Control-Allow-Methods: ' . implode(', ', $allowed_methods_arr));
        header('Allow: ' . implode(', ', $allowed_methods_arr));
    }

    public static function setAccessControlAllowOriginHeader()
    {
        $allowed_hosts = \OLOG\ConfWrapper::value('access_control_allow_origin_header', '');
        if($allowed_hosts != '') {
            header('Access-Control-Allow-Origin: ' . $allowed_hosts);
        }
    }
}
