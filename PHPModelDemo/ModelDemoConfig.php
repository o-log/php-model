<?php

namespace PHPModelDemo;

use OLOG\Cache\CacheConfig;
use OLOG\Cache\MemcacheServerSettings;
use OLOG\DB\DBConfig;
use OLOG\DB\DBSettings;

class ModelDemoConfig
{
    const DB_NAME_PHPMODELDEMO = 'phpmodel';

    public static function init()
    {
        DBConfig::setDBSettingsObj(
            self::DB_NAME_PHPMODELDEMO,
            new DBSettings('localhost', 'phpmodel', 'root', '1')
        );

        CacheConfig::addServerSettingsObj(
            new MemcacheServerSettings('localhost', 11211)
        );
    }
}