<?php

namespace OLOG;

class Url
{
    static public function getCurrentUrlNoGetForm(){
        $url = $_SERVER['REQUEST_URI'];
        $no_form = $url;

        if (strpos($url, '?')){
            list($no_form, $form) = explode('?', $url);
        }

        return $no_form;
    }
}