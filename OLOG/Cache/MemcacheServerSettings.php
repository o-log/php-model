<?php

namespace OLOG\Cache;

/**
 * Class MemcacheServerSettings
 * @deprecated Use CacheServerSettings instead
 * @see CacheServerSettings
 * @package OLOG\Cache
 */
class MemcacheServerSettings extends CacheServerSettings
{
    public function __construct($host, $port)
    {
        parent::__construct($host, $port);
    }
}