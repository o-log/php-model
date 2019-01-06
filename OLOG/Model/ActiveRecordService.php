<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Model;

use OLOG\DB\DB;

class ActiveRecordService
{
    const INGORE_LIST_FIELD_NAME = 'active_record_ignore_fields_arr';

    public static function getIdFieldName($model_obj)
    {
        $obj_class_name = get_class($model_obj);

        if (defined($obj_class_name . '::DB_ID_FIELD_NAME')) {
            return $obj_class_name::DB_ID_FIELD_NAME;
        }

        return 'id';
    }

    public static function updateRecord($db_id, $db_table_name, $fields_to_save_arr, $id_field_name, $model_id_value){
        $placeholders_arr = array();

        foreach ($fields_to_save_arr as $field_name => $field_value) {
            $field_name = preg_replace("/[^a-zA-Z0-9_]+/", "", $field_name);
            $placeholders_arr[] = $field_name . '=?';
        }

        $values_arr = array_values($fields_to_save_arr);
        array_push($values_arr, $model_id_value);

        $query = 'update ' . $db_table_name . ' set ' . implode(',', $placeholders_arr) . ' where ' . $id_field_name . ' = ?';
        DB::query($db_id, $query, $values_arr);
    }

    public static function insertRecord($db_id, $db_table_name, $fields_to_save_arr, $id_field_name){
        $placeholders_arr = array_fill(0, count($fields_to_save_arr), '?');

        $quoted_fields_to_save_arr = array();
        foreach (array_keys($fields_to_save_arr) as $field_name_to_save) {
            $quoted_fields_to_save_arr[] = preg_replace("/[^a-zA-Z0-9_]+/", "", $field_name_to_save);
        }

        DB::query(
            $db_id,
            'insert into ' . $db_table_name . ' (' . implode(',', $quoted_fields_to_save_arr) . ') values (' . implode(',', $placeholders_arr) . ')',
            array_values($fields_to_save_arr)
        );

        $db_sequence_name = $db_table_name . '_' . $id_field_name . '_seq';
        $last_insert_id = DB::lastInsertId($db_id, $db_sequence_name);
        return $last_insert_id;
    }

    /**
     * все удаление делается внутри транзакции (включая canDelete и afterDelete), если будет исключение - транзакция будет откачена PDO
     */
    public static function deleteModel(ActiveRecordInterface $obj){
        $obj_class_name = get_class($obj);
        $obj_db_id = $obj_class_name::DB_ID;

        $transaction_is_my = false;
        if (!DB::inTransaction($obj_db_id)) {
            DB::beginTransaction($obj_db_id);
            $transaction_is_my = true;
        }

        $can_delete_message = '';
        if (!$obj->canDelete($can_delete_message)) {
            if ($transaction_is_my) {
                DB::rollBack($obj_db_id);
            }
            throw new \Exception($can_delete_message);
        }

        ActiveRecordService::deleteModelObj($obj);

        try {
            $obj->afterDelete();
        } catch (\Exception $e){
            // in the case of any exception - rollback transaction and rethrow exception
            // thus actual db record will not be deleted
            if ($transaction_is_my) {
                DB::rollback($obj_db_id);
            }

            throw $e;
        }

        if ($transaction_is_my) {
            DB::commit($obj_db_id);
        }
    }

    /**
     * Удаление записи
     * @param $model_obj
     * @return \PDOStatement
     */
    public static function deleteModelObj($model_obj)
    {
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

        $result = DB::query(
            $db_id,
            'DELETE FROM ' . $db_table_name . ' where ' . $db_id_field_name . ' = ?',
            array($model_id_value)
        );

        return $result;
    }

}
