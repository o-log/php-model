<?php

namespace OLOG\Cache;

class CacheConfig
{
    static protected $servers_obj_arr = [];

    static public function addServerSettingsObj(MemcacheServerSettings $server_settings_obj){
        $servers_obj_arr[] = $server_settings_obj;
    }

    static public function getServersObjArr(){
        return self::$servers_obj_arr;
    }
}