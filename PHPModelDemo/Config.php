<?php

namespace PHPModelDemo;

class Config
{
    const DB_NAME_PHPMODELDEMO = 'phpmodel';

    public static function get()
    {
        $conf = [];
        //$conf = \App\CommonConfig::get(); // uncomment this to inherit shared configuration part, stored to repository

        /*
        $conf['memcache_servers'] = [
            'localhost:11211'
        ];
        */
        
        $conf['cache_lifetime'] = 60;
        $conf['return_false_if_no_route'] = true; // for local php server
        $conf['db'] = array(
            self::DB_NAME_PHPMODELDEMO => array(
                'host' => '127.0.0.1',
                'db_name' => 'phpmodel',
                'user' => 'root',
                'pass' => '1'
            ),
        );

        return $conf;
    }
}