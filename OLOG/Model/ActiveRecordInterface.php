<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

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
    public function load($id): bool;
    public function getId();
    public function beforeSave(): void;
    public function save(); // не указываю возвращаемый тип в интерфейсе - непонятно как указать здесь вызывающий класс при реализации метода в trait
    public function afterSave(): void;
    public function afterLoad(): void;
    public function canDelete(&$message): bool;
    public function delete(); // не указываю возвращаемый тип в интерфейсе - непонятно как указать здесь вызывающий класс при реализации метода в trait
    public function afterDelete(): void;
    public static function factory($id_to_load, $exception_if_not_loaded = true); // не указываю возвращаемый тип в интерфейсе - непонятно как указать здесь вызывающий класс при реализации метода в trait
    //static public function removeObjFromCacheById($id_to_remove);
    public function removeFromFactoryCache(): void;
}
