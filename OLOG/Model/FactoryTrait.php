<?php

namespace OLOG\Model;

trait FactoryTrait {
    /**
     * Возвращает глобализованное имя класса модели.
     * @return string
     */
    static public function getMyGlobalizedClassName()
    {
        $class_name = get_called_class(); // "Gets the name of the class the static method is called in."
        $class_name = \OLOG\Model\Helper::globalizeClassName($class_name);
        return $class_name;
    }
    
    /**
     * @return $this
     */
    static public function factory($id_to_load, $exception_if_not_loaded = true)
    {
        $class_name = self::getMyGlobalizedClassName();
        return \OLOG\Model\FactoryHelper::factory($class_name, $id_to_load, $exception_if_not_loaded);
    }

    static public function removeObjFromCacheById($id_to_remove)
    {
        $class_name = self::getMyGlobalizedClassName();
        \OLOG\Model\FactoryHelper::removeObjFromCacheById($class_name, $id_to_remove);
    }

    /**
     * Базовая обработка изменения.
     * Если на это событие есть подписчики - нужно переопределить обработчик в самом классе и там eventmanager::invoke, где уже подписать остальных подписчиков.
     * сделано статиками чтобы можно было вызывать для других объектов не создавая, только по id.
     */
    static public function afterUpdate($id)
    {
        $class_name = self::getMyGlobalizedClassName();
        \OLOG\Model\FactoryHelper::afterUpdate($class_name, $id);
    }

    /**
     * Метод чистки после удаления объекта.
     * Поскольку модели уже нет в базе, этот метод должен использовать только данные объекта в памяти:
     * - не вызывать фабрику для этого объекта
     * - не использовать геттеры (они могут обращаться к базе)
     * - не быть статическим: работает в контексте конкретного объекта
     */
    public function afterDelete()
    {
        $class_name = self::getMyGlobalizedClassName();
        \OLOG\Model\FactoryHelper::afterDelete($class_name, $this->id);
    }
}