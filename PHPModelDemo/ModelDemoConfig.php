<?php

namespace PHPModelDemo;

use OLOG\Cache\CacheConfig;
use OLOG\Cache\CacheRedis;
use OLOG\Cache\MemcacheServerSettings;
use OLOG\DB\DBConfig;
use OLOG\DB\DBConnector;
use OLOG\DB\DBSettings;
use OLOG\Model\ModelConfig;

class ModelDemoConfig
{
    const DB_NAME_PHPMODELDEMO = 'phpmodel';
    const DB_CONNECTOR_PHPMODELDEMO = 'phpmodel';

    public static function init()
    {
        DBConfig::setDBConnectorObj(self::DB_CONNECTOR_PHPMODELDEMO,
            new DBConnector('localhost', 'phpmodel', 'root', '1')
        );

        DBConfig::setDBSettingsObj(
            self::DB_NAME_PHPMODELDEMO,
            new DBSettings('', '', '', '', '', self::DB_CONNECTOR_PHPMODELDEMO)
        );

        CacheConfig::addServerSettingsObj(
            //new MemcacheServerSettings('localhost', 11211)
            new MemcacheServerSettings('localhost', 6379)
        );

        CacheConfig::setEngineClassname(CacheRedis::class);

        ModelConfig::addAfterSaveSubscriber(SomeModel::class, DemoAfterSaveSubscriber::class);
        ModelConfig::addBeforeSaveSubscriber(SomeModel::class, DemoBeforeSaveSubscriber::class);
    }
}