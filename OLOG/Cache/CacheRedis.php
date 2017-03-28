<?php

namespace OLOG\Cache;

class CacheRedis implements CacheEngineInterface
{
    static public function set($key, $value, $ttl_secs)
    {
        if ($ttl_secs == -1) {
            $ttl_secs = 60;
        }

        if ($ttl_secs < 0) {
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

    static public function increment($key)
    {
        throw new \Exception('redis increment not implemented');
        // инкремент сейчас не поддерживается
        // что надо сделать:
        // 1. если такого ключа еще нет - редис создает новый со значением 1, при этом у нас все значения должны быть сериализованные, а это будет не сериализованное. нужно запретить создавать ключ если если его при инкременте?
        // 2. перед инкрементом десериализовать, а потом сериализовать обратно

        /*
        $mc = self::getRedisConnectionObj();
        if (!$mc){
            return false;
        }

        $full_key = self::cache_key($key);
        $mc->incr($full_key);
        return true;
        */
    }

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
        /** @var Redis $mc */
        $mc = self::getRedisConnectionObj();
        if (!$mc){
            return false;
        }

        $full_key = self::cache_key($key);
        return $mc->delete($full_key);
    }

    /**
     * @return null|\Redis
     * @throws \Exception
     */
    static public function getRedisConnectionObj()
    {
        static $redis = NULL;

        if (isset($redis)) {
            return $redis;
        }

        $memcache_servers = CacheConfig::getServersObjArr();
        if (!$memcache_servers){
            return null;
        }

        if (count($memcache_servers) > 1){
            throw new \Exception('CacheRedis doesnt support multiple servers');
        }

        $redis = new \Redis();

        /** @var MemcacheServerSettings $server_settings_obj */
        foreach ($memcache_servers as $server_settings_obj) {
            \OLOG\Assert::assert(
                $redis->connect($server_settings_obj->getHost(), $server_settings_obj->getPort())
            );
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