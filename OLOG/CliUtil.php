<?php

namespace OLOG;

class CliUtil
{
    static public function readStdinAnswer(){
        echo "> ";

        $answer = trim(fgets(STDIN));

        return $answer;
    }
}