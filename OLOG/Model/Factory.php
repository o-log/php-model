<?php

namespace OLOG\Model;

/**
 * Базовая фабрика объектов - V2. Используется для объектов, у которых идентификатор не составной.
 * Умеет создавать объекты указанного класса, при необходимости загружая их из кэша.
 */
class Factory
{
    protected static function getObjectCacheId($class_name, $object_id)
    {
        return $class_name . '::' . $object_id;
    }

    public static function removeObjectFromCache($class_name, $object_id)
    {
        $cache_key = self::getObjectCacheId($class_name, $object_id);
        \OLOG\Cache\CacheWrapper::delete($cache_key);
    }

    /**
     * Создает новый объект указанного класса и вызывает для него load().
     * @param $class_name string Имя класса, объект которого создаем.
     * @param $object_id string Идентификатор объекта
     * @return null|object Если удалось создать и загрузить объект - возвращается этот объект. Иначе (например, не удалось загрузить) - возвращает null.
     * @throws \Exception
     */
    public static function createAndLoadObject($class_name, $object_id)
    {
        $cache_key = self::getObjectCacheId($class_name, $object_id);

        $cached_obj = \OLOG\Cache\CacheWrapper::get($cache_key);

        if ($cached_obj !== false) {
            return $cached_obj;
        }


        $obj = new $class_name;

        $object_is_loaded = call_user_func_array(array($obj, "load"), array($object_id));

        if (!$object_is_loaded) {
            return null;
        }

        // store to cache

        $cache_ttl_seconds = 60;

        if ($obj instanceof \OLOG\Model\InterfaceCacheTtlSeconds) {
            $cache_ttl_seconds = $obj->getCacheTtlSeconds();
        }

        \OLOG\Cache\CacheWrapper::set($cache_key, $obj, $cache_ttl_seconds);

        return $obj;
    }

}