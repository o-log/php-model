<?php

namespace OLOG\Model;
use OLOG\DB\DBWrapper;
use OLOG\FullObjectId;

/**
 * Для работы с ActiveRecord необходимо:
 *
 * 1. создаем таблицу в БД с полем "id" (auto increment) и прочими нужными полями
 * 2. создаем класс для модели:
 *      - для каждого поля в таблице у класса должно быть свое свойство
 *      - значения по-умолчанию должны соответствовать полям таблицы
 *      - указываемм две константы:
 *          - const DB_ID           - идентификатор БД (news, stats, etc.)
 *          - const DB_TABLE_NAME   - имя таблицы в которой хранятся данные модели
 *      - подключаем трейты:
 *          - ProtectPropertiesTrait
 *          - ActiveRecordTrait
 *      - пишем необходимые геттеры и сеттеры
 *
 * Сделано трейтом, чтобы:
 * - был нормальный доступ к данным объекта (в т.ч. защищенным)
 * - идешка видела методы ActiveRecordTrait
 */
trait ActiveRecordTrait
{
    public function load($id)
    {
        return \OLOG\Model\ActiveRecordHelper::loadModelObj($this, $id);
    }

    /**
     * Базовая реализация beforeSave - ничего не делает.
     * При необходимости можно переопределить этот метод и сделать в нем дополнительную обработку или проверки перед
     * сохранением.
     */
    public function beforeSave(){
    }

    /**
     * все сохранение делается внутри транзакции (включая beforeSave и afterSave), если будет исключение - транзакция
     * будет откачена PDO
     */
    public function save()
    {
        ActiveRecordHelper::exceptionIfObjectIsIncompatibleWithActiveRecord($this);

        $obj_class_name = get_class($this);
        $obj_db_id = $obj_class_name::DB_ID;

        $transaction_is_my = false;
        if (!DBWrapper::inTransaction($obj_db_id)) {
            DBWrapper::beginTransaction($obj_db_id);
            $transaction_is_my = true;
        }

        $this->beforeSave();
        $before_save_subscribers_arr = ModelConfig::getBeforeSaveSubscribersArr(self::class);

        foreach ($before_save_subscribers_arr as $before_save_subscriber) {
            /**
             * реализация интерфейса проверена на этапе добавления подписчиков
             * @var ModelBeforeSaveCallbackInterface $before_save_subscriber
             */
            $before_save_subscriber::beforeSave($this);
        }

        \OLOG\Model\ActiveRecordHelper::saveModelObj($this);

        // не вызываем afterSave если это вызов save для этого объекта изнутри aftersave этого же объекта (для предотвращения бесконечного рекурсивного вызова afterSave)
        static $__inprogress = [];
        $inprogress_key = FullObjectId::getFullObjectId($this);
        if (!array_key_exists($inprogress_key, $__inprogress)) {
            $__inprogress[$inprogress_key] = 1;

            $this->afterSave();

            $after_save_subscribers_arr = ModelConfig::getAfterSaveSubscribersArr(self::class);

            foreach ($after_save_subscribers_arr as $after_save_subscriber) {
                /**
                 * реализация интерфейса проверена на этапе добавления подписчиков
                 * @var ModelAfterSaveCallbackInterface $after_save_subscriber
                 */
                $after_save_subscriber::afterSave($this->getId());
            }
            
            unset($__inprogress[$inprogress_key]);
        }

        // комитим только если мы же и стартовали транзакцию (на случай вложенных вызовов)
        if ($transaction_is_my) {
            DBWrapper::commitTransaction($obj_db_id);
        }
    }

    /**
     * Базовая обработка изменения.
     * Если на это событие есть подписчики - нужно переопределить обработчик в самом классе и там уже подписать
     * остальных подписчиков.
     * Не забыть в переопределенном методе сбросить кэш фабрики!
     */
    public function afterSave()
    {
        if ($this instanceof InterfaceFactory) {
            $this->removeFromFactoryCache();
        }
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

    public function delete()
    {
        ActiveRecordHelper::deleteModel($this);
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
        /*
        if ($this instanceof InterfaceFactory) {
            $class_name = self::getMyClassName();
            FactoryHelper::removeObjFromCacheById($class_name, $this->getId());
        }*/
        if ($this instanceof InterfaceFactory) {
            $this->removeFromFactoryCache();
        }
    }

    /**
     * пока работаем с полями объекта напрямую, без сеттеров/геттеров
     * этот метод позволяет писать в защищенные свойства (используется, например, в CRUD)
     * @param $fields_arr
     */
    public function ar_setFields($fields_arr)
    {
        foreach ($fields_arr as $field_name => $field_value) {
            $this->$field_name = $field_value;
        }
    }

    public function getFieldValueByName($field_name)
    {
        return $this->$field_name;
    }

}
