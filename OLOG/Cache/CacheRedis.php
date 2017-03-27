<?php

namespace OLOG\Cache;

class CacheRedis
{

    static public function set($key, $value, $ttl_secs = 0)
    {

        if ($ttl_secs == -1) {
            // TODO: придумать, как глобально правлять временем кэширования
            $ttl_secs = 60;
        }

        if ($ttl_secs > 0) {
            /*
            if ($exp > 2592000) { // не добавляем тайм для мелких значений, чтобы не добавлять сложностей с разными часами на серверах и т.п.
                $exp += time();
            }
            */
        } else {
            $ttl_secs = 0;
        }

        if($ttl_secs == 0) {
            return true;
        }

        $mc = self::getRedisConnectionObj(); // do not check result - already checked
        if (!$mc){
            return false;
        }

        $full_key = self::cache_key($key);

        $value_ser = serialize($value);
        $mcs_result = $mc->set($full_key, $value_ser, $ttl_secs);

        if (!$mcs_result) {
            return false;
        }

        return true;
    }

    /*
    static public function increment($key)
    {
        $mc = self::getRedisConnectionObj();
        if (!$mc){
            return false;
        }

        $full_key = self::dmemcache_key($key);
        if (!$mc->increment($full_key)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    */

    /**
     * returns false if key not found
     * @param $key
     * @return array|bool|string
     */
    static public function get($key)
    {
        $mc = self::getRedisConnectionObj();
        if (!$mc){
            return false;
        }

        $full_key = self::cache_key($key);
        $result = $mc->get($full_key);

        if ($result === false){
            return false;
        }

        $result = unserialize($result);

        return $result;
    }

    static public function delete($key)
    {
        $mc = self::getRedisConnectionObj();
        if (!$mc){
            return false;
        }

        $full_key = self::cache_key($key);
        return $mc->delete($full_key);
    }

    static public function getRedisConnectionObj()
    {
        static $redis = NULL;

        if (isset($redis)) {
            return $redis;
        }

        //$memcache_servers = \OLOG\ConfWrapper::value(\OLOG\Model\ModelConstants::MODULE_CONFIG_ROOT_KEY . '.memcache_servers');
        /*
        $memcache_servers = CacheConfig::getServersObjArr();

        if (!$memcache_servers){
            return null;
        }
        */

        // Memcached php extension not supported - slower, rare, extra features not needed
        /** @var \Memcache $redis */
        $redis = new \Redis();

        /** @var MemcacheServerSettings $server_settings_obj */
        /*
        foreach ($memcache_servers as $server_settings_obj) {
            \OLOG\Assert::assert($redis->addServer($server_settings_obj->getHost(), $server_settings_obj->getPort()));
            $redis->setCompressThreshold(5000, 0.2);
        }
        */

        // TODO: finish
        if (!$redis->connect('127.0.0.1', 6379)){
            throw new \Exception('redis connect failed');
        }

        return $redis;
    }

    static public function cache_key($key)
    {
        $prefix = CacheConfig::getCacheKeyPrefix();
        if ($prefix == ''){
            $prefix = 'default';
        }

        $full_key = $prefix . '-' . $key;

        return md5($full_key);
    }

}