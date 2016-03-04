<?php

namespace OLOG;

class Redirects
{
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

    static public function redirectToSelfNoGetForm()
    {
        header('Location: ' . Url::getCurrentUrlNoGetForm());
        exit;
    }

    static public function redirectToSelf()
    {
        header('Location: ' . Url::getCurrentUrl());
        exit;
    }
}