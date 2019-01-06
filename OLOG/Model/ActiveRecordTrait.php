<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Model;
use OLOG\DB\DB;

/**
 * Для работы с ActiveRecord необходимо:
 *
 * 1. создаем таблицу в БД с полем "id" (auto increment) и прочими нужными полями
 * 2. создаем класс для модели:
 *      - для каждого поля в таблице у класса должно быть свое свойство
 *      - значения по умолчанию должны соответствовать полям таблицы
 *      - указываем в классе две константы:
 *          - const DB_ID           - идентификатор БД (news, stats, etc.)
 *          - const DB_TABLE_NAME   - имя таблицы в которой хранятся данные модели
 *      - подключаем трейты:
 *          - ActiveRecordTrait
 *      - пишем необходимые геттеры и сеттеры
 *
 * Сделано трейтом, чтобы:
 * - был нормальный доступ к данным объекта (в т.ч. защищенным)
 * - идешка видела методы ActiveRecordTrait
 */
trait ActiveRecordTrait
{
    public function load($id): bool
    {
        return $this->loadModelObj($id);
    }

    /**
     * Метод собственно загрузки из БД сделан отдельно от load чтобы можно было использовать его в переопределенных
     * версиях load.
     * Сделан в трейте, а не в отдельном вспомогательном классе - чтобы был доступ к защищенным свойствам ообъекта без
     * использования reflection (для производительности).
     * Возвращает false если объект не найден.
     */
    protected function loadModelObj($id): bool
    {
        $model_class_name = get_class($this);
        $db_id = $model_class_name::DB_ID;
        $db_table_name = $model_class_name::DB_TABLE_NAME;
        $id_field_name = ActiveRecordService::getIdFieldName($this);

        $data_obj = DB::readObject(
            $db_id,
            'select /* LMO */ * from ' . $db_table_name . ' where ' . $id_field_name . ' = ?',
            array($id)
        );

        if (!$data_obj) {
            return false;
        }

        foreach ($data_obj as $field_name => $field_value) {
            if (property_exists($model_class_name, $field_name)){
                $this->$field_name = $field_value;
            } else {
                if (!ModelConfig::ignoreMissingPropertiesOnLoad()){
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
    public function beforeSave(): void
    {
        // may be implemented in model class
    }

    /**
     * все сохранение делается внутри транзакции (включая beforeSave и afterSave), если будет исключение - транзакция
     * будет откачена PDO
     */
    public function save(): self
    {
        $obj_class_name = get_class($this);
        $obj_db_id = $obj_class_name::DB_ID;

        $transaction_is_my = false;
        if (!DB::inTransaction($obj_db_id)) {
            DB::beginTransaction($obj_db_id);
            $transaction_is_my = true;
        }

        $this->beforeSave();

        try {
            $this->saveModelObj();
        } catch (\Exception $e) {
            // if any exception while saving - rollback transaction and rethrow exception
            if ($transaction_is_my) {
                DB::rollback($obj_db_id);
            }

            throw $e;
        }

        // не вызываем afterSave если это вызов save для этого объекта изнутри aftersave этого же объекта (для предотвращения бесконечного рекурсивного вызова afterSave)
        static $__inprogress = [];
        $inprogress_key = FullObjectId::getFullObjectId($this);
        if (!array_key_exists($inprogress_key, $__inprogress)) {
            $__inprogress[$inprogress_key] = 1;

            $this->afterSave();

            unset($__inprogress[$inprogress_key]);
        }

        // комитим только если мы же и стартовали транзакцию (на случай вложенных вызовов)
        if ($transaction_is_my) {
            DB::commit($obj_db_id);
        }

        return $this;
    }

    /**
     * Метод собственно записи в БД сделан отдельно от load чтобы можно было использовать его в переопределенных
     * версиях load.
     * Сделан в трейте, а не в отдельном вспомогательном классе - чтобы был доступ к защищенным свойствам ообъекта без
     * использования reflection (для производительности).
     */
    protected function saveModelObj(): void
    {
        $model_class_name = get_class($this);
        $db_id = $model_class_name::DB_ID;
        $db_table_name = $model_class_name::DB_TABLE_NAME;
        $id_field_name = ActiveRecordService::getIdFieldName($this);

        $field_names = [];

        /*
         * Сейчас за основу берется структура таблицы БД - это позволяет сначала деплоить код, а затем мигрировать
         * изменения в БД без ошибок: новые поля в модели не будут писаться в БД, пока они не появятся в таблицах.
         * Но explain при сохранениях может создавать дополнительную нагрузку и при необходимости можно использовать
         * другую схему: брать за основу структуру модели, как было раньше: $this->get_object_vars().
         * Также можно кэшировать результат explain если производительность станет проблемой.
         */
        $db_table_fields_arr = DB::readObjects($db_id, 'explain ' . $db_table_name);
        foreach ($db_table_fields_arr as $field_index => $field_obj) {
            $field_names[] = $field_obj->Field;
        }

        $fields_to_save_arr = array();
        foreach ($field_names as $field_name){
            if (property_exists($model_class_name, $field_name)) {
                $property_value = $this->$field_name;
                if (is_bool($property_value)){ // PDO converts boolean value to strings, so false becomes ''
                    $property_value = (int) $property_value;
                }
                $fields_to_save_arr[$field_name] = $property_value;
            } else {
                if (!ModelConfig::ignoreMissingPropertiesOnSave()) {
                    throw new \Exception('Missing property when saving model: field "' . $field_name . '" exists in DB table and not present in model class. You can disable this check using ModelConfig::setIgnoreMissingPropertiesOnSave()');
                }
            }
        }

        unset($fields_to_save_arr[$id_field_name]);

        $model_id_value = $this->$id_field_name;

        if ($model_id_value == '') {
            $last_insert_id = ActiveRecordService::insertRecord($db_id, $db_table_name, $fields_to_save_arr, $id_field_name);
            $this->$id_field_name = $last_insert_id;
        } else {
            ActiveRecordService::updateRecord($db_id, $db_table_name, $fields_to_save_arr, $id_field_name, $model_id_value);
        }
    }

    /**
     * Базовая обработка изменения.
     * Если на это событие есть подписчики - нужно переопределить обработчик в самом классе и там уже подписать
     * остальных подписчиков.
     * Не забыть в переопределенном методе сбросить кэш фабрики!
     */
    public function afterSave(): void
    {
        $this->removeFromFactoryCache();
    }

    /**
     * Метод проверки возможности удаления объекта.
     * Если объект удалять нельзя - нужно вернуть false.
     * В переменную, переданную по ссылке, можно записать текст сообщения для вывода пользователю.
     * @param $message
     * @return bool
     */
    public function canDelete(&$message): bool
    {
        return true;
    }

    public function delete(): self
    {
        ActiveRecordService::deleteModel($this);
        return $this;
    }

    /**
     * Метод чистки после удаления объекта.
     * Поскольку модели уже нет в базе, этот метод должен использовать только данные объекта в памяти:
     * - не вызывать фабрику для этого объекта
     * - не использовать геттеры (они могут обращаться к базе)
     * - не быть статическим: работает в контексте конкретного объекта
     */
    public function afterDelete(): void
    {
        $this->removeFromFactoryCache();
    }

    static public function factory($id_to_load, $exception_if_not_loaded = true): ?self
    {
        $class_name = get_called_class(); // "Gets the name of the class the static method is called in."
        $obj = Factory::createAndLoadObject($class_name, $id_to_load);

        if ($exception_if_not_loaded) {
            if (!$obj){
                throw new \Exception();
            }
        }

        if ($obj){
            $obj->afterLoad();
        }

        return $obj;
    }

    public function afterLoad(): void
    {
        // may be implemented in model class
    }

    /**
     * Сбрасывает кэш объекта, созданный фабрикой при его загрузке.
     * Нужно вызывать после изменения или удаления объекта.
     */
    public function removeFromFactoryCache(): void
    {
        $class_name = get_class($this);
        Factory::removeObjectFromCache($class_name, $this->getId());
    }

    /*
    public function getFieldValueByName($field_name)
    {
        return $this->$field_name;
    }
    */

    /**
     * пока работаем с полями объекта напрямую, без сеттеров/геттеров
     * этот метод позволяет писать в защищенные свойства (используется, например, в CRUD)
     * @param $fields_arr
     */
    /*
    public function ar_setFields($fields_arr)
    {
        foreach ($fields_arr as $field_name => $field_value) {
            $this->$field_name = $field_value;
        }
    }
    */

    /**
     * @param array $ids
     * @return self[]
     */
    static public function idsToObjs(array $ids): array
    {
        return array_map(
            function ($id) {
                return self::factory($id);
            },
            $ids
        );
    }

    static public function first($objs, $exception_if_none = true): ?self {
        if (empty($objs)){
            if ($exception_if_none){
                throw new \Exception('Empty array passed to first()');
            }

            return null;
        }

        return $objs[0];
    }

    static public function single($objs, $exception_if_none = true): ?self {
        if (count($objs) > 1){
            throw new \Exception('Single object required.');
        }

        if (empty($objs)){
            if ($exception_if_none){
                throw new \Exception('Empty array passed to single()');
            }

            return null;
        }

        return $objs[0];
    }
}
