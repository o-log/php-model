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
    const SPACE_PHPMODELDEMO = 'phpmodel';
    const CONNECTOR_PHPMODELDEMO = 'phpmodel';

    public static function init()
    {
        DBConfig::setConnector(self::CONNECTOR_PHPMODELDEMO, new ConnectorMySQL('127.0.0.1', 'phpmodel', 'root', '1234'));
        DBConfig::setSpace(self::SPACE_PHPMODELDEMO, new Space(self::CONNECTOR_PHPMODELDEMO, 'phpmodel.sql'));

        CacheConfig::setBucket('', new BucketMemcache([new MemcacheServer('localhost', 11211)]));

        ModelConfig::addAfterSaveSubscriber(CallbacksDemoModel::class, DemoAfterSaveSubscriber::class);
        ModelConfig::addBeforeSaveSubscriber(CallbacksDemoModel::class, DemoBeforeSaveSubscriber::class);
    }
}