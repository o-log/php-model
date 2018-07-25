<?php

namespace Config;

use OLOG\Cache\BucketMemcache;
use OLOG\Cache\BucketRedis;
use OLOG\Cache\CacheConfig;
use OLOG\Cache\MemcacheServer;
use OLOG\Cache\RedisServer;
use OLOG\DB\ConnectorMySQL;
use OLOG\DB\DBConfig;
use OLOG\DB\Space;
use OLOG\Model\ModelConfig;
use PHPModelDemo\CallbacksDemoModel;
use PHPModelDemo\DemoAfterSaveSubscriber;
use PHPModelDemo\DemoBeforeSaveSubscriber;

class Config
{
    const SPACE_PHPMODELDEMO = 'phpmodel';
    const CONNECTOR_PHPMODELDEMO = 'phpmodel';

    public static function init()
    {
        DBConfig::setConnector(self::CONNECTOR_PHPMODELDEMO, new ConnectorMySQL('127.0.0.1', 'phpmodel', 'root', '1'));
        DBConfig::setSpace(self::SPACE_PHPMODELDEMO, new Space(self::CONNECTOR_PHPMODELDEMO, 'phpmodel.sql'));

        CacheConfig::setBucket('', new BucketRedis([new RedisServer('localhost', 6379)]));

        ModelConfig::addAfterSaveSubscriber(CallbacksDemoModel::class, DemoAfterSaveSubscriber::class);
        ModelConfig::addBeforeSaveSubscriber(CallbacksDemoModel::class, DemoBeforeSaveSubscriber::class);
    }
}