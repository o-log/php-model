<?php

namespace Config;

use OLOG\Cache\BucketMemcache;
use OLOG\Cache\CacheConfig;
use OLOG\Cache\MemcacheServer;
use OLOG\DB\ConnectorMySQL;
use OLOG\DB\DBConfig;
use OLOG\DB\Space;
use OLOG\Model\ModelConfig;
use PHPModelDemo\CallbacksDemoModel;
use PHPModelDemo\DemoAfterSaveSubscriber;
use PHPModelDemo\DemoBeforeSaveSubscriber;

class Config
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

        CacheConfig::setBucket('', new BucketMemcache([new MemcacheServer('localhost', 11211)]));

        ModelConfig::addAfterSaveSubscriber(CallbacksDemoModel::class, DemoAfterSaveSubscriber::class);
        ModelConfig::addBeforeSaveSubscriber(CallbacksDemoModel::class, DemoBeforeSaveSubscriber::class);
    }
}