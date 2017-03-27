<?php

namespace OLOG\Cache;

class CacheConfig
{
    static protected $servers_obj_arr = [];
    static protected $cache_key_prefix = '';
    static protected $engine_class_name = CacheMemcache::class;

    /**
     * @return string
     */
    public static function getEngineClassname()
    {
        return self::$engine_class_name;
    }

    /**
     * @param string $engine_class_name
     */
    public static function setEngineClassname($engine_class_name)
    {
        // TODO: check cache engine interface

        self::$engine_class_name = $engine_class_name;
    }

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