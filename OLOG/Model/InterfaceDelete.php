<?php

namespace OLOG\Model;

/**
 * Если класс реализует этот интерфейс, то он должен иметь:
 * - Метод delete(), который удаляет данные объекта в базе. Поведение метода при наличии зависимых объектов пока не регламентировано.
 */
interface InterfaceDelete {
    public function canDelete(&$message);
    public function delete();
    public function afterDelete();
}