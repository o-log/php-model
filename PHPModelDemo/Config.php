<?php

namespace PHPModelDemo;

class Config
{
    const DB_NAME_PHPMODELDEMO = 'phpmodel';

    public static function get()
    {
        $conf = [];
        //$conf = \App\CommonConfig::get(); // uncomment this to inherit shared configuration part, stored to repository

        //$conf['cache_lifetime'] = 60; // not used
        $conf['return_false_if_no_route'] = true; // for local php server

        $conf[\OLOG\Model\Constants::MODULE_CONFIG_ROOT_KEY] = [
            'db' => [
                self::DB_NAME_PHPMODELDEMO => [
                    'host' => '127.0.0.1',
                    'db_name' => 'phpmodel',
                    'user' => 'root',
                    'pass' => '1',
                    //'sql_file' => 'sql_file/phpmodel.sql'
                 ]   
            ],

            'memcache_servers' => [
                'localhost:11211'
            ]
        ];


        return $conf;
    }
}