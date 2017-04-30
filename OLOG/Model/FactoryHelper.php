<?php

namespace OLOG\Model;

class FactoryHelper
{
    public static function factory($class_name, $id_to_load, $exception_if_not_loaded = true)
    {
        $obj = \OLOG\Model\Factory::createAndLoadObject($class_name, $id_to_load);

        if ($exception_if_not_loaded) {
            \OLOG\Assert::assert($obj);
        }

        return $obj;
    }

    public static function removeObjFromCacheById($class_name, $id_to_remove)
    {
        \OLOG\Model\Factory::removeObjectFromCache($class_name, $id_to_remove);
    }

    public static function removeObjectFromFactoryCache($class_name, $id)
    {
        self::removeObjFromCacheById($class_name, $id);
    }
}
