<?php

namespace OLOG\Model;

/**
 * Реализация классом этого интерфейса означает, что класс имеет метод load, который:
 * - принимает один параметр: идентификатор объекта
 * - заполняет поля объекта
 * - возвращает true если все нормально, false - если не получилось загрузить объект (нет в БД и т.п.)
 * - Также класс должен иметь метод getId, который возвращает идентификатор объекта в виде строки.
 * - Метод save(), который сохраняет данные объекта. Если объекта нет в базе - он должен создавать и его id должен заполняться
 * правильным значением.
 * - Метод delete(), который удаляет данные объекта в базе. Поведение метода при наличии зависимых объектов пока не регламентировано.
 * - класс умеет создавать свои экземпляры, кэшировать их и сбрасывать кэш при изменениях.
*/
interface ActiveRecordInterface {
    public function load($id);
    public function getId();
    public function beforeSave();
    public function save();
    public function afterSave();
    public function afterLoad();
    public function canDelete(&$message);
    public function delete();
    public function afterDelete();
    public static function factory($id_to_load, $exception_if_not_loaded = true);
    //static public function removeObjFromCacheById($id_to_remove);
    public function removeFromFactoryCache();
}
