<?php

namespace OLOG\Model;


use OLOG\DB\DBWrapper;
use OLOG\Sanitize;

class ActiveRecordHelper
{
    const INGORE_LIST_FIELD_NAME = 'active_record_ignore_fields_arr';

    public static function getIdFieldName($model_obj)
    {
        $obj_class_name = get_class($model_obj);

        if (defined($obj_class_name . '::DB_ID_FIELD_NAME')) {
            $id_field_name = $obj_class_name::DB_ID_FIELD_NAME;
        } else {
            $id_field_name = 'id';
        }
        return $id_field_name;
    }

    public static function updateRecord($db_id, $db_table_name, $fields_to_save_arr, $id_field_name, $model_id_value){
        $placeholders_arr = array();

        foreach ($fields_to_save_arr as $field_name => $field_value) {
            $placeholders_arr[] = $field_name . '=?';
        }

        $values_arr = array_values($fields_to_save_arr);
        array_push($values_arr, $model_id_value);

        $query = 'update ' . Sanitize::sanitizeSqlColumnName($db_table_name) . ' set ' . implode(',', $placeholders_arr) . ' where ' . $id_field_name . ' = ?';
        \OLOG\DB\DBWrapper::query($db_id, $query, $values_arr);
    }

    public static function insertRecord($db_id, $db_table_name, $fields_to_save_arr, $id_field_name){
        $placeholders_arr = array_fill(0, count($fields_to_save_arr), '?');

        $quoted_fields_to_save_arr = array();
        foreach (array_keys($fields_to_save_arr) as $field_name_to_save) {
            $quoted_fields_to_save_arr[] = Sanitize::sanitizeSqlColumnName($field_name_to_save);
        }

        \OLOG\DB\DBWrapper::query(
            $db_id,
            'insert into ' . $db_table_name . ' (' . implode(',', $quoted_fields_to_save_arr) . ') values (' . implode(',', $placeholders_arr) . ')',
            array_values($fields_to_save_arr)
        );

        $db_sequence_name = $db_table_name . '_' . $id_field_name . '_seq';
        $last_insert_id = \OLOG\DB\DBWrapper::lastInsertId($db_id, $db_sequence_name);
        return $last_insert_id;
    }

    /**
     * Сохранение записи
     * @param $model_obj
     */
    /*
    public static function saveModelObj($model_obj)
    {
        self::exceptionIfObjectIsIncompatibleWithActiveRecord($model_obj);

        $model_class_name = get_class($model_obj);
        $db_id = $model_class_name::DB_ID;
        $db_table_name = $model_class_name::DB_TABLE_NAME;

        $db_table_fields_arr = DBWrapper::readObjects(
            $db_id,
            'explain ' . Sanitize::sanitizeSqlColumnName($db_table_name)
        );

        $db_id_field_name = self::getIdFieldName($model_obj);

        $fields_to_save_arr = array();

        $reflect = new \ReflectionClass($model_obj);

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

        unset($fields_to_save_arr[$db_id_field_name]);

        $property_obj = $reflect->getProperty($db_id_field_name);
        $property_obj->setAccessible(true);
        $model_id_value = $property_obj->getValue($model_obj);

        if ($model_id_value == '') {
            $placeholders_arr = array_fill(0, count($fields_to_save_arr), '?');

            $quoted_fields_to_save_arr = array();
            foreach (array_keys($fields_to_save_arr) as $field_name_to_save) {
                $quoted_fields_to_save_arr[] = $field_name_to_save;
            }

            \OLOG\DB\DBWrapper::query(
                $db_id,
                'insert into ' . $db_table_name . ' (' . implode(',', $quoted_fields_to_save_arr) . ') values (' . implode(',', $placeholders_arr) . ')',
                array_values($fields_to_save_arr)
            );

            $db_sequence_name = $db_table_name . '_' . $db_id_field_name . '_seq';
            $last_insert_id = \OLOG\DB\DBWrapper::lastInsertId($db_id, $db_sequence_name);
            $property_obj->setValue($model_obj, $last_insert_id);

            //\OLOG\Logger\Logger::logObjectEvent($model_obj, 'CREATE');
        } else {
            $placeholders_arr = array();

            foreach ($fields_to_save_arr as $field_name => $field_value) {
                $placeholders_arr[] = $field_name . '=?';
            }

            $values_arr = array_values($fields_to_save_arr);
            array_push($values_arr, $model_id_value);

            $query = 'update ' . $db_table_name . ' set ' . implode(',', $placeholders_arr) . ' where ' . $db_id_field_name . ' = ?';
            \OLOG\DB\DBWrapper::query($db_id, $query, $values_arr);

            //\OLOG\Logger\Logger::logObjectEvent($model_obj, 'UPDATE');
        }
    }
    */

    /**
     * Загружаем запись
     * @param $model_obj
     * @param $id
     * @return bool
     */
    /*
    public static function loadModelObj($model_obj, $id)
    {
        self::exceptionIfObjectIsIncompatibleWithActiveRecord($model_obj);

        $model_class_name = get_class($model_obj);
        $db_id = $model_class_name::DB_ID;
        $db_table_name = $model_class_name::DB_TABLE_NAME;
        $db_id_field_name = self::getIdFieldName($model_obj);

        $data_obj = \OLOG\DB\DBWrapper::readObject(
            $db_id,
            'select * from ' . $db_table_name . ' where ' . $db_id_field_name . ' = ?',
            array($id)
        );

        if (!$data_obj) {
            return false;
        }

        $reflect = new \ReflectionClass($model_class_name);
        foreach ($data_obj as $field_name => $field_value) {
            $property = $reflect->getProperty($field_name);
            $property->setAccessible(true);
            $property->setValue($model_obj, $field_value);
        }

        return true;
    }
    */

    /**
     * все удаление делается внутри транзакции (включая canDelete и afterDelete), если будет исключение - транзакция будет откачена PDO
     */
    public static function deleteModel($obj){
        self::exceptionIfObjectIsIncompatibleWithActiveRecord($obj);

        $obj_class_name = get_class($obj);
        $obj_db_id = $obj_class_name::DB_ID;

        $transaction_is_my = false;
        if (!DBWrapper::inTransaction($obj_db_id)) {
            DBWrapper::beginTransaction($obj_db_id);
            $transaction_is_my = true;
        }

        $can_delete_message = '';
        if ($obj instanceof \OLOG\Model\InterfaceDelete) {
            if (!$obj->canDelete($can_delete_message)) {
                if ($transaction_is_my) {
                    DBWrapper::rollBackTransaction($obj_db_id);
                }
                throw new \Exception($can_delete_message);
            }
        }

        \OLOG\Model\ActiveRecordHelper::deleteModelObj($obj);

        if ($obj instanceof \OLOG\Model\InterfaceDelete) {
            try {
                $obj->afterDelete();
            } catch (\Exception $e){
                // in the case of any exception - rollback transaction and rethrow exception
                // thus actual db record will not be deleted
                if ($transaction_is_my) {
                    DBWrapper::rollbackTransaction($obj_db_id);
                }

                throw $e;
            }
        }

        if ($transaction_is_my) {
            DBWrapper::commitTransaction($obj_db_id);
        }
    }

    /**
     * Удаление записи
     * @param $model_obj
     * @return \PDOStatement
     */
    public static function deleteModelObj($model_obj)
    {
        self::exceptionIfObjectIsIncompatibleWithActiveRecord($model_obj);

        $model_class_name = get_class($model_obj);
        $db_id = $model_class_name::DB_ID;
        $db_table_name = $model_class_name::DB_TABLE_NAME;
        $db_id_field_name = self::getIdFieldName($model_obj);

        $reflect = new \ReflectionClass($model_obj);
        $property_obj = $reflect->getProperty($db_id_field_name);
        $property_obj->setAccessible(true);
        $model_id_value = $property_obj->getValue($model_obj);

        if ($model_id_value == ''){
            throw new \Exception('Deleting not saved object');
        }

        $result = \OLOG\DB\DBWrapper::query(
            $db_id,
            'DELETE FROM ' . $db_table_name . ' where ' . $db_id_field_name . ' = ?',
            array($model_id_value)
        );

        //\OLOG\Logger\Logger::logObjectEvent($model_obj, 'DELETE');

        return $result;
    }

    /**
     * Проверяет, что объект (класс его) предоставляет нужные константы и т.п.
     * Если что-то не так - выбрасывает исключение. По исключениям разработчик класса может понять чего не хватает.
     * @param $obj
     * @throws \Exception
     */
    static public function exceptionIfObjectIsIncompatibleWithActiveRecord($obj)
    {
        if (!is_object($obj)) {
            throw new \Exception('must be object');
        }

        $obj_class_name = get_class($obj);

        self::exceptionIfClassIsIncompatibleWithActiveRecord($obj_class_name);
    }

    static public function exceptionIfClassIsIncompatibleWithActiveRecord($class_name)
    {
        if (!defined($class_name . '::DB_ID')) {
            throw new \Exception('class must provide DB_ID constant to use ActiveRecordTrait');
        }

        if (!defined($class_name . '::DB_TABLE_NAME')) {
            throw new \Exception('class must provide DB_TABLE_NAME constant to use ActiveRecordTrait');
        }
    }
}