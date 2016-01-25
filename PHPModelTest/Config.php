<?php

namespace PHPModelTest;

class Conf
{
    const DB_NAME_PHPMODELTEST = 'phpmodel';

    public static function get()
    {
        //$conf = \Guk\CommonConfig::get();
        $conf['cache_lifetime'] = 60;
        $conf['return_false_if_no_route'] = true; // for local php server
        $conf['db'] = array(
            self::DB_NAME_PHPMODELTEST => array(
                'host' => 'localhost',
                'db_name' => 'phpmodel',
                'user' => 'root',
                'pass' => '1'
            ),
        );

        return $conf;
    }
}