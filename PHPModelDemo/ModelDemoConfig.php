<?php

namespace PHPModelDemo;

use OLOG\Cache\CacheConfig;
use OLOG\Cache\CacheRedis;
use OLOG\Cache\CacheServerSettings;
use OLOG\Cache\CacheWrapper;
use OLOG\Cache\MemcacheServerSettings;
use OLOG\DB\DBConfig;
use OLOG\DB\DBConnector;
use OLOG\DB\DBSettings;
use OLOG\DB\DBTablesetObj;
use OLOG\Model\ModelConfig;

class ModelDemoConfig
{
    const DBTABLESET_PHPMODELDEMO = 'phpmodel';
    const DBCONNECTOR_PHPMODELDEMO_READWRITE = 'phpmodel';

    public static function init()
    {
        DBConfig::setDBConnectorObj(
            self::DBCONNECTOR_PHPMODELDEMO_READWRITE,
            new DBConnector('localhost', 'phpmodel', 'root', '1')
        );

        /*
        DBConfig::setDBSettingsObj(
            self::DB_NAME_PHPMODELDEMO,
            new DBSettings('', '', '', '', '', self::DB_CONNECTOR_PHPMODELDEMO),
            null
        );
        */

        DBConfig::setDBTablesetObj(
            self::DBTABLESET_PHPMODELDEMO,
            new DBTablesetObj(
                self::DBCONNECTOR_PHPMODELDEMO_READWRITE
            )
        );

        CacheConfig::addServerSettingsObj(
            //new MemcacheServerSettings('localhost', 11211)
            new CacheServerSettings('localhost', 6379)
        );

        CacheConfig::setEngineClassname(CacheRedis::class);

        ModelConfig::addAfterSaveSubscriber(SomeModel::class, DemoAfterSaveSubscriber::class);
        ModelConfig::addBeforeSaveSubscriber(SomeModel::class, DemoBeforeSaveSubscriber::class);

        ModelConfig::addCLIMenuClass(DemoCLIMenu::class);
    }
}