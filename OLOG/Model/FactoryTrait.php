<?php

namespace OLOG\Model;

trait FactoryTrait {
    /**
     * Возвращает имя класса модели.
     * @return string
     */
    static public function getMyClassName()
    {
        $class_name = get_called_class(); // "Gets the name of the class the static method is called in."
        return $class_name;
    }
    
    /**
     * @return $this
     */
    static public function factory($id_to_load, $exception_if_not_loaded = true)
    {
        $class_name = self::getMyClassName();
        return \OLOG\Model\FactoryHelper::factory($class_name, $id_to_load, $exception_if_not_loaded);
    }

    static public function removeObjFromCacheById($id_to_remove)
    {
        $class_name = self::getMyClassName();
        \OLOG\Model\FactoryHelper::removeObjFromCacheById($class_name, $id_to_remove);
    }

    /**
     * Сбрасывает кэш объекта, созданный фабрикой при его загрузке.
     * Нужно вызывать после изменения или удаления объекта.
     */
    public function removeFromFactoryCache()
    {
        $class_name = self::getMyClassName();
        \OLOG\Model\FactoryHelper::removeObjectFromFactoryCache($class_name, $this->getId()); // TODO: check interfaceLoad
    }
}