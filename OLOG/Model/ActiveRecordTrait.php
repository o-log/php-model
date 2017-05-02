<?php

namespace OLOG\Model;
use OLOG\DB\DBWrapper;
use OLOG\FullObjectId;
use OLOG\Sanitize;

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
        return $this->loadModelObj($id);
    }

    /**
     * Метод собственно загрузки из БД сделан отдельно от load чтобы можно было использовать его в переопределенных
     * версиях load.
     * Сделан в трейте, а не в отдельном вспомогательном классе - чтобы был доступ к защищенным свойствам ообъекта без
     * использования reflection (для производительности).
     * @param $model_obj
     * @param $id
     * @return bool
     */
    protected function loadModelObj($id)
    {
        ActiveRecordHelper::exceptionIfObjectIsIncompatibleWithActiveRecord($this);

        $model_class_name = get_class($this);
        $db_id = $model_class_name::DB_ID;
        $db_table_name = $model_class_name::DB_TABLE_NAME;
        $db_id_field_name = ActiveRecordHelper::getIdFieldName($this);

        $data_obj = \OLOG\DB\DBWrapper::readObject(
            $db_id,
            'select /* LMO */ * from ' . Sanitize::sanitizeSqlColumnName($db_table_name) . ' where ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) . ' = ?',
            array($id)
        );

        if (!$data_obj) {
            return false;
        }

        foreach ($data_obj as $field_name => $field_value) {
            if (property_exists($model_class_name, $field_name)){
                $this->$field_name = $field_value;
            } else {
                if (ModelConfig::isIgnoreMissingPropertiesOnLoad()){
                    // ignore missing property
                } else {
                    throw new \Exception('Missing "' . $field_name . '" property in class "' . $model_class_name . '" while property is present in DB table "' . $db_table_name . '"');
                }
            }
        }

        return true;
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

        try {
            $this->saveModelObj();
        } catch (\Exception $e) {
            // if any exception while saving - rollback transaction and rethrow exception
            if ($transaction_is_my) {
                DBWrapper::rollbackTransaction($obj_db_id);
            }

            throw $e;
        }

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
     * Метод собственно записи в БД сделан отдельно от load чтобы можно было использовать его в переопределенных
     * версиях load.
     * Сделан в трейте, а не в отдельном вспомогательном классе - чтобы был доступ к защищенным свойствам ообъекта без
     * использования reflection (для производительности).
     * @param $model_obj
     */
    protected function saveModelObj()
    {
        ActiveRecordHelper::exceptionIfObjectIsIncompatibleWithActiveRecord($this);

        $model_class_name = get_class($this);
        $db_id = $model_class_name::DB_ID;
        $db_table_name = $model_class_name::DB_TABLE_NAME;

        $db_table_fields_arr = DBWrapper::readObjects(
            $db_id,
            'explain ' . Sanitize::sanitizeSqlColumnName($db_table_name)
        );

        $id_field_name = ActiveRecordHelper::getIdFieldName($this);

        $fields_to_save_arr = array();

        //$reflect = new \ReflectionClass($model_obj);

        /*
	    $ignore_properties_names_arr = array();
	    if ($reflect->hasProperty(self::INGORE_LIST_FIELD_NAME))
	    {
		    $ignore_fields_arr_field = $reflect->getProperty(self::INGORE_LIST_FIELD_NAME);
            $ignore_fields_arr_field->setAccessible(true); // на случай если поле будет protected
            $ignore_properties_names_arr = $ignore_fields_arr_field->getValue($model_obj);
	    }
        */

        /*
        foreach ($reflect->getProperties() as $property_obj) {
            // игнорируем статические свойства класса
            // также игнорируем свойства класса перечисленные в игнор листе $active_record_ignore_fields_arr
            //if ($property_obj->isStatic() || in_array($property_obj->getName(), $ignore_properties_names_arr)) {
            //    continue;
            //}
            if ($property_obj->isStatic()) {
                continue;
            }

            $property_obj->setAccessible(true);
            $fields_to_save_arr[$property_obj->getName()] = $property_obj->getValue($model_obj);
        }
        */
        foreach ($db_table_fields_arr as $field_index => $field_obj){
            $field_name = $field_obj->Field;

            if (property_exists($model_class_name, $field_name)) {
                $property_value = $this->$field_name;
                $fields_to_save_arr[$field_name] = $property_value;
            } else {
                if (ModelConfig::isIgnoreMissingPropertiesOnSave()) {
                    // ignore
                } else {
                    throw new \Exception('missing property');
                }
            }
        }

        unset($fields_to_save_arr[$id_field_name]);

        /*
        $property_obj = $reflect->getProperty($db_id_field_name);
        $property_obj->setAccessible(true);
        $model_id_value = $property_obj->getValue($model_obj);
        */
        $model_id_value = $this->$id_field_name;

        if ($model_id_value == '') {
            $last_insert_id = ActiveRecordHelper::insertRecord($db_id, $db_table_name, $fields_to_save_arr, $id_field_name);
            //$property_obj->setValue($model_obj, $last_insert_id);
            $this->$id_field_name = $last_insert_id;

            //\OLOG\Logger\Logger::logObjectEvent($model_obj, 'CREATE');
        } else {
            ActiveRecordHelper::updateRecord($db_id, $db_table_name, $fields_to_save_arr, $id_field_name, $model_id_value);

            //\OLOG\Logger\Logger::logObjectEvent($model_obj, 'UPDATE');
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
