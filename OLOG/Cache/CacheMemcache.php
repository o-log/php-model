<?php

namespace OLOG\Cache;

class CacheMemcache implements CacheEngineInterface
{
    static public function set($key, $value, $exp)
    {
        if ($exp == -1) {
            $exp = 60;
        }

        if ($exp > 0) {
            if ($exp > 2592000) { // не добавляем тайм для мелких значений, чтобы не добавлять сложностей с разными часами на серверах и т.п.
                $exp += time();
            }
        } else {
            $exp = 0;
        }

        if($exp == 0) {
            return true;
        }

        $mc = self::getMcConnectionObj(); // do not check result - already checked
        if (!$mc){
            return false;
        }

        $full_key = self::dmemcache_key($key);

        $mcs_result = $mc->set($full_key, $value, MEMCACHE_COMPRESSED, $exp);

        if (!$mcs_result) {
            return FALSE;
        }

        return TRUE;
    }

    static public function increment($key)
    {
        $mc = self::getMcConnectionObj();
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

    static public function get($key)
    {
        $mc = self::getMcConnectionObj();
        if (!$mc){
            return false;
        }

        $full_key = self::dmemcache_key($key);
        $result = $mc->get($full_key);

        return $result;
    }

    static public function delete($key)
    {
        $mc = self::getMcConnectionObj();
        if (!$mc){
            return false;
        }

        $full_key = self::dmemcache_key($key);
        return $mc->delete($full_key);
    }

    static public function getMcConnectionObj()
    {
        static $memcache = NULL;

        if (isset($memcache)) {
            return $memcache;
        }

        //$memcache_servers = \OLOG\ConfWrapper::value(\OLOG\Model\ModelConstants::MODULE_CONFIG_ROOT_KEY . '.memcache_servers');
        $memcache_servers = CacheConfig::getServersObjArr();

        if (!$memcache_servers){
            return null;
        }

        // Memcached php extension not supported - slower, rare, extra features not needed
        /** @var \Memcache $memcache */
        $memcache = new \Memcache;

        /** @var MemcacheServerSettings $server_settings_obj */
        foreach ($memcache_servers as $server_settings_obj) {
            \OLOG\Assert::assert($memcache->addServer($server_settings_obj->getHost(), $server_settings_obj->getPort()));
            $memcache->setCompressThreshold(5000, 0.2);
        }

        return $memcache;
    }

    static public function dmemcache_key($key)
    {
        $prefix = CacheConfig::getCacheKeyPrefix();
        if ($prefix == ''){
            $prefix = 'default';
        }

        $full_key = $prefix . '-' . $key;

        return md5($full_key);
    }

} 