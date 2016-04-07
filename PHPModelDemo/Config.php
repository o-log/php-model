<?php

namespace PHPModelDemo;

class Config
{
    const DB_NAME_PHPMODELDEMO = 'phpmodel';

    public static function get()
    {
        //$conf = \App\CommonConfig::get(); // uncomment this to inherit shared configuration part, stored to repository
        
        $conf['cache'] = [];
        
        $conf['cache_lifetime'] = 60;
        $conf['return_false_if_no_route'] = true; // for local php server
        $conf['db'] = array(
            self::DB_NAME_PHPMODELDEMO => array(
                'host' => 'localhost',
                'db_name' => 'phpmodel',
                'user' => 'root',
                'pass' => '1'
            ),
        );

        return $conf;
    }
}