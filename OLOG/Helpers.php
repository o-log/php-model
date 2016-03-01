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

    /**
     * @deprecated Replaced with Assert::assert()
     * @param $value
     * @param string $message
     * @throws \Exception
     */
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
            if (array_key_exists('REQUEST_URI', $_SERVER)) {
                $message_str .= ' [' . $_SERVER['REQUEST_URI'] . ']';
            }

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

    static public function check_plain($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * @deprecated Moved to Exits
     *
     */
    static public function exit404()
    {
        header("HTTP/1.0 404 Not Found");
        exit();
    }

    /**
     * @deprecated Moved to Exits
     *
     */
    static public function exit429()
    {
        header("HTTP/1.0 429 Too Many Requests");
        exit();
    }

    /**
     * @deprecated Moved to Exits
     *
     */
    static public function exit408()
    {
        header("HTTP/1.0 408 Request Timeout");
        exit();
    }

    /**
     * @deprecated Moved to Exits
     * @param $exit_condition
     */
    static public function exit404If($exit_condition)
    {
        if (!$exit_condition) {
            return;
        }

        header("HTTP/1.0 404 Not Found");
        exit();
    }

    /**
     * @deprecated Moved to Exits
     * @param $exit_condition
     */
    static public function exit403If($exit_condition)
    {
        if (!$exit_condition) {
            return;
        }

        header("HTTP/1.0 403 Forbidden");

        exit();
    }

    /**
     * @deprecated Moved to Exits
     *
     */
    static public function exit403()
    {
        header("HTTP/1.0 403 Forbidden");
        exit();
    }

    /**
     * @deprecated Moved to Exits
     * @param $exit_condition
     */
    static public function exit400If($exit_condition)
    {
        if (!$exit_condition) {
            return;
        }

        header("HTTP/1.0 400 Bad Request");
        exit();
    }

    /**
     * @deprecated Moved to Exits
     * @param $exit_condition
     * @param string $message
     */
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

    /**
     * @deprecated Moved to Exits
     * @param array $allowed_methods_arr
     */
    public static function exit405($allowed_methods_arr = array())
    {
        header("HTTP/1.1 405 Method Not Allowed");
        if(!empty($allowed_methods_arr)) {
            self::setAllowedMethodsHeaders($allowed_methods_arr);
        }

        exit();
    }

    /**
     * @deprecated Moved to Redirects
     * @param $url
     */
    static public function redirect301($url)
    {
        header("HTTP/1.0 301 Moved Permanently");
        header('Location: ' . $url);
        exit;
    }

    /**
     * @deprecated Moved to Urls
     * @return mixed
     */
    static public function getCurrentUrlNoGetForm(){
      $url = $_SERVER['REQUEST_URI'];
      $no_form = $url;
      
      if (strpos($url, '?')){
	list($no_form, $form) = explode('?', $url);
      }
      
      return $no_form;
    }

    /**
     * @deprecated Moved to Redirects
     * @param $url
     */
    static public function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * @deprecated Moved to Redirects
     *
     */
    static public function redirectToSelfNoGetForm()
    {
      header('Location: ' . self::getCurrentUrlNoGetForm());
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
