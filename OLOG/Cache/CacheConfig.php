<?php

namespace OLOG\Cache;

use OLOG\CheckClassInterfaces;

class CacheConfig
{
    static protected $servers_obj_arr = [];
    static protected $cache_key_prefix = '';
    static protected $engine_class_name = CacheMemcache::class;
    static protected $cache_engine_params_arr = [];

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
        CheckClassInterfaces::exceptionIfClassNotImplementsInterface($engine_class_name, CacheEngineInterface::class);

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

    /**
     * @param CacheServerSettings $server_settings_obj
     */
    static public function addServerSettingsObj(CacheServerSettings $server_settings_obj)
    {
        self::$servers_obj_arr[] = $server_settings_obj;
    }

    /**
     * @return CacheServerSettings[]
     */
    static public function getServersObjArr()
    {
        return self::$servers_obj_arr;
    }

    /**
     * @return array
     */
    public static function getCacheEngineParamsArr()
    {
        return self::$cache_engine_params_arr;
    }

    /**
     * @param array $cache_engine_params_arr
     */
    public static function setCacheEngineParamsArr($cache_engine_params_arr)
    {
        self::$cache_engine_params_arr = $cache_engine_params_arr;
    }
}