<?php

namespace OLOG;

class CliUtil
{
    static public function delimiter(){
        return str_pad('', 60, '_') . "\n\n";
    }

    static public function readStdinAnswer(){
        echo "> ";

        $answer = trim(fgets(STDIN));

        return $answer;
    }
}