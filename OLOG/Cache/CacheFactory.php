<?php

namespace OLOG\Cache;

class CacheFactory {
    /**
     * @return \OLOG\Cache\Cache
     */
    static public function getCacheObj()
    {
        static $cache_obj;

        if (isset($cache_obj)) {
            return $cache_obj;
        }

        $cache_obj = new \OLOG\Cache\Cache();
        return $cache_obj;
    }


}