<?php

namespace OLOG\Cache;

class Dmemcache
{

    static public function dmemcache_set($key, $value, $exp = 0, $bin = 'cache')
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

        $mc = self::dmemcache_object($bin); // do not check result - already checked
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

    static public function dmemcache_increment($key, $bin = 'cache')
    {
        if ($mc = self::dmemcache_object($bin)) {
            $full_key = self::dmemcache_key($key, $bin);
            if (!$mc->increment($full_key)) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
        return FALSE;
    }

    static public function dmemcache_get($key, $bin = 'cache')
    {
        $mc = self::dmemcache_object($bin); // do not check result - already checked
        if (!$mc){
            return false;
        }

        $full_key = self::dmemcache_key($key, $bin);
        $result = $mc->get($full_key);

        return $result;
    }

    static public function dmemcache_delete($key, $bin = 'cache')
    {
        $mc = self::dmemcache_object($bin);  // do not check result - already checked

        $full_key = self::dmemcache_key($key, $bin);
        return $mc->delete($full_key);
    }

    static public function dmemcache_object($bin = NULL)
    {
        static $memcache = NULL;

        if (isset($memcache)) {
            return $memcache;
        }

        $memcache_servers = \OLOG\ConfWrapper::value('memcache_servers');
        if (!$memcache_servers){
            return null;
        }

        $memcache = new \Memcache;

        foreach ($memcache_servers as $s => $c) {
            list($host, $port) = explode(':', $s);

            \OLOG\Helpers::assert($memcache->addServer($host, $port));
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