<?php

namespace OLOG;

class Assert
{
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
}