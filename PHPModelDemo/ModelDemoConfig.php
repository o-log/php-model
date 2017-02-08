<?php

namespace PHPModelDemo;

use OLOG\Cache\CacheConfig;
use OLOG\Cache\MemcacheServerSettings;
use OLOG\DB\DBConfig;
use OLOG\DB\DBSettings;
use OLOG\Model\ModelConfig;

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


        ModelConfig::addAfterSaveSubscriber(SomeModel::class, DemoAfterSaveSubscriber::class);
        ModelConfig::addBeforeSaveSubscribers(SomeModel::class, DemoBeforeSaveSubscriber::class);
    }
}