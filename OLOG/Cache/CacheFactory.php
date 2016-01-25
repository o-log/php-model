<?php

namespace Cebera\Cache;

class CacheFactory {
    /**
     * @return \Cebera\Cache\Cache
     */
    static public function getCacheObj()
    {
        static $cache_obj;

        if (isset($cache_obj)) {
            return $cache_obj;
        }

        $cache_obj = new \Cebera\Cache\Cache();
        return $cache_obj;
    }


}