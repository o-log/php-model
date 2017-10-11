<?php

namespace PHPModelDemo;

use OLOG\DB\DBConfig;
use OLOG\DB\ConnectorMySQL;
use OLOG\DB\Space;
use OLOG\Model\ModelConfig;

class ModelDemoConfig
{
    const DB_NAME_PHPMODELDEMO = 'phpmodel';
    const DB_CONNECTOR_PHPMODELDEMO = 'phpmodel';

    public static function init()
    {
        DBConfig::setConnector(
            self::DB_CONNECTOR_PHPMODELDEMO,
            new ConnectorMySQL('127.0.0.1', 'phpmodel', 'root', '1234')
        );

        DBConfig::setSpace(
            self::DB_NAME_PHPMODELDEMO,
            new Space(self::DB_CONNECTOR_PHPMODELDEMO, 'phpmodel.sql')
        );

        /*
        CacheConfig::addServerSettingsObj(
            //new MemcacheServerSettings('localhost', 11211)
            new CacheServerSettings('localhost', 6379)
        );

        CacheConfig::setEngineClassname(CacheRedis::class);
         */

        ModelConfig::addAfterSaveSubscriber(SomeModel::class, DemoAfterSaveSubscriber::class);
        ModelConfig::addBeforeSaveSubscriber(SomeModel::class, DemoBeforeSaveSubscriber::class);

        //ModelConfig::addCLIMenuClass(DemoCLIMenu::class);
    }
}