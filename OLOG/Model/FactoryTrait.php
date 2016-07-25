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
        //$class_name = \OLOG\Model\Helper::globalizeClassName($class_name);
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
     * Базовая реализация beforeSave - ничего не делает.
     * При необходимости можно переопределить этот метод и сделать в нем дополнительную обработку или проверки перед сохранением.
     */
    public function beforeSave(){
    }

    /**
     * Базовая обработка изменения.
     * Если на это событие есть подписчики - нужно переопределить обработчик в самом классе и там уже подписать остальных подписчиков.
     */
    public function afterUpdate()
    {
        $this->removeFromFactoryCache();
    }

    /**
     * Сбрасывает кэш фабрики для объекта.
     */
    public function removeFromFactoryCache()
    {
        $class_name = self::getMyClassName();
        \OLOG\Model\FactoryHelper::afterUpdate($class_name, $this->getId()); // TODO: check interfaceLoad
    }

    /**
     * Метод проверки возможности удаления объекта.
     * Если объект удалять нельзя - нужно вернуть false.
     * В переменную, переданную по ссылке, можно записать текст сообщения для вывода пользователю.
     * @param $message
     * @return bool
     */
    public function canDelete(&$message)
    {
        return true;
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
        $class_name = self::getMyClassName();
        \OLOG\Model\FactoryHelper::afterDelete($class_name, $this->getId()); // TODO: check interfaceLoad
    }
}