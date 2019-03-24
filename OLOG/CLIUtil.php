<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG;

class CliUtil
{
    static public function error($str){
        echo("\033[31m" . $str . "\033[0m\n");
    }

    static public function delimiter()
    {
        return str_pad('', 60, '_') . "\n\n";
    }

    static public function readStdinAnswer()
    {
        echo "> ";

        $answer = trim(fgets(STDIN));

        return $answer;
    }

    static public function ARGVOptional($index, $default = ''){
        return isset($_SERVER['argv'][$index]) ? $_SERVER['argv'][$index] : $default;
    }
}
