<?php

namespace OLOG\Cache;

class CacheWrapper
{

    static protected $storage_arr = array();

    /**
     * @param $key
     * @return array|string|false Возвращает false если значения нет в кэше. Нужно использовать типизированную проверку, чтобы отличить например от значения 0, полученного из кэша.
     */
    static public function get($key)
    {
        if (isset(self::$storage_arr[$key])) {
            return self::$storage_arr[$key];
        }

        $value = \OLOG\Cache\Dmemcache::dmemcache_get($key);

        if ($value !== false) {
            self::$storage_arr[$key] = $value;
        }

        return $value;
    }

    static public function delete($key)
    {
        unset(self::$storage_arr[$key]);

        $cache_obj = \OLOG\Cache\CacheFactory::getCacheObj();
        if (!$cache_obj->connected) {
            return false;
        }

        return $cache_obj->delete($key);
    }

    static public function set($key, $value, $expire = -1)
    {
        self::$storage_arr[$key] = $value;

        return \OLOG\Cache\Dmemcache::dmemcache_set($key, $value, $expire);
    }

    static public function increment($key)
    {
        // мы не можем корректно обновить значение в статическом кэше - он обновится только на одной машине
        // поэтому удаляем неактуальное значение с тем, чтобы оно если что перечиталось из мемкеша
        unset(self::$storage_arr[$key]);

        return \OLOG\Cache\Dmemcache::dmemcache_increment($key);
    }
}