<?php

namespace OLOG\Cache;

class CacheMemcache
{

    static public function set($key, $value, $exp = 0, $bin = 'cache')
    {

        if ($exp == -1) {
            // TODO: придумать, как глобально правлять временем кэширования
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

        $full_key = self::dmemcache_key($key, $bin);

        $mcs_result = $mc->set($full_key, $value, MEMCACHE_COMPRESSED, $exp);

        if (!$mcs_result) {
            return FALSE;
        }

        return TRUE;
    }

    static public function increment($key, $bin = 'cache')
    {
        $mc = self::getMcConnectionObj();
        if (!$mc){
            return false;
        }
        
        $full_key = self::dmemcache_key($key, $bin);
        if (!$mc->increment($full_key)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    static public function get($key, $bin = 'cache')
    {
        $mc = self::getMcConnectionObj();
        if (!$mc){
            return false;
        }

        $full_key = self::dmemcache_key($key, $bin);
        $result = $mc->get($full_key);

        return $result;
    }

    static public function delete($key, $bin = 'cache')
    {
        $mc = self::getMcConnectionObj();
        if (!$mc){
            return false;
        }

        $full_key = self::dmemcache_key($key, $bin);
        return $mc->delete($full_key);
    }

    static public function getMcConnectionObj()
    {
        static $memcache = NULL;

        if (isset($memcache)) {
            return $memcache;
        }

        $memcache_servers = \OLOG\ConfWrapper::value(\OLOG\Model\ModelConstants::MODULE_CONFIG_ROOT_KEY . '.memcache_servers');
        if (!$memcache_servers){
            return null;
        }

        // Memcached php extension not supported - slower, rare, extra features not needed
        $memcache = new \Memcache;

        foreach ($memcache_servers as $server) {
            list($host, $port) = explode(':', $server);

            \OLOG\Assert::assert($memcache->addServer($host, $port));
            $memcache->setCompressThreshold(5000, 0.2);
        }

        return $memcache;
    }

    static public function dmemcache_key($key, $bin = 'cache')
    {
        $prefix = '123';
        $full_key = ($prefix ? $prefix . '-' : '') . $bin . '-' . $key;

        return md5($full_key);
    }

} 