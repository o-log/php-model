<?php

namespace OLOG\Cache;

/**
 * Основной класс для работы с кэшом. Устанавливать соединение с сервером мемкэша перед работой не надо - оно будет установлено автоматически.
 * Class CacheWrapper
 * @package OLOG\Cache
 */
class CacheWrapper
{

    static protected $storage_arr = array();

    /**
     * Получение значения из кэша. Если настроек мемкэша в конфиге нет - используется только статический кэш.
     * @param $key
     * @return array|string|false Возвращает false если значения нет в кэше. Нужно использовать типизированную проверку, чтобы отличить например от значения 0, полученного из кэша.
     */
    static public function get($key)
    {
        if (isset(self::$storage_arr[$key])) {
            return self::$storage_arr[$key];
        }

        $value = \OLOG\Cache\CacheMemcache::get($key);

        if ($value !== false) {
            self::$storage_arr[$key] = $value;
        }

        return $value;
    }

    /**
     * Удаление значения из кэша.
     * @param $key
     * @return bool
     */
    static public function delete($key)
    {
        unset(self::$storage_arr[$key]);

        return CacheMemcache::delete($key);
    }

    /**
     * Запись значения в кэш.
     * @param $key
     * @param $value
     * @param int $expire
     * @return bool
     */
    static public function set($key, $value, $expire = -1)
    {
        self::$storage_arr[$key] = $value;

        return \OLOG\Cache\CacheMemcache::set($key, $value, $expire);
    }

    static public function increment($key)
    {
        // мы не можем корректно обновить значение в статическом кэше - он обновится только на одной машине
        // поэтому удаляем неактуальное значение с тем, чтобы оно если что перечиталось из мемкеша
        unset(self::$storage_arr[$key]);

        return \OLOG\Cache\CacheMemcache::increment($key);
    }
}