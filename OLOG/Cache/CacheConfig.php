<?php

namespace OLOG\Cache;

class CacheConfig
{
    static protected $servers_obj_arr = [];
    static protected $cache_key_prefix = '';

    /**
     * @return string
     */
    public static function getCacheKeyPrefix()
    {
        return self::$cache_key_prefix;
    }

    /**
     * @param string $cache_key_prefix
     */
    public static function setCacheKeyPrefix($cache_key_prefix)
    {
        self::$cache_key_prefix = $cache_key_prefix;
    }

    static public function addServerSettingsObj(MemcacheServerSettings $server_settings_obj){
        self::$servers_obj_arr[] = $server_settings_obj;
    }

    static public function getServersObjArr(){
        return self::$servers_obj_arr;
    }
}