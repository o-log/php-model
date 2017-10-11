<?php

namespace OLOG\Model;

use OLOG\Model\Factory;

class FactoryHelper
{
    public static function factory($class_name, $id_to_load, $exception_if_not_loaded = true)
    {
        $obj = Factory::createAndLoadObject($class_name, $id_to_load);

        if ($exception_if_not_loaded) {
            if (!$obj){
                throw new \Exception();
            }
        }

        return $obj;
    }

    public static function removeObjFromCacheById($class_name, $id_to_remove)
    {
        Factory::removeObjectFromCache($class_name, $id_to_remove);
    }

    public static function removeObjectFromFactoryCache($class_name, $id)
    {
        self::removeObjFromCacheById($class_name, $id);
    }
}
