<?php

namespace OLOG\DB;

use OLOG\Assert;

class DBConfig
{
    static protected $dbsettings_obj_arr = [];
    static protected $readonlydb_settings_obj_arr = [];
    static protected $dbconnector_obj_arr = [];
    static protected $dbtableset_obj_arr = [];

    /**
     * @param $dbconnector_id
     * @param DBConnector $dbconnector_obj
     */
    static public function setDBConnectorObj($dbconnector_id, DBConnector $dbconnector_obj){
        self::$dbconnector_obj_arr[$dbconnector_id] = $dbconnector_obj;
    }

    /**
     * @param $db_id
     * @return DBConnector
     */
    static public function getDBConnectorObj($dbconnector_id){
        Assert::assert(array_key_exists($dbconnector_id, self::$dbconnector_obj_arr));

        return self::$dbconnector_obj_arr[$dbconnector_id];
    }

    /**
     * @deprecated use Tableset instead
     * @param $db_id
     * @param DBSettings $settings_obj
     * @param DBSettings|null $readonlydb_settings_obj
     */
    static public function setDBSettingsObj($db_id, DBSettings $settings_obj, DBSettings $readonlydb_settings_obj = null){
        self::$dbsettings_obj_arr[$db_id] = $settings_obj;

        if (!is_null($readonlydb_settings_obj)) {
            self::$readonlydb_settings_obj_arr[$db_id] = $readonlydb_settings_obj;
        }
    }

    /**
     * @deprecated use Tableset instead
     * @param $db_id
     * @return DBSettings
     */
    static public function getDBSettingsObj($db_id, $exception_if_none = true){
        if (!array_key_exists($db_id, self::$dbsettings_obj_arr)) {
            if ($exception_if_none) {
                Assert::assert(array_key_exists($db_id, self::$dbsettings_obj_arr));
            }

            return null;
        }

        return self::$dbsettings_obj_arr[$db_id];
    }

    /**
     * @deprecated use Tableset instead
     * @param $db_id
     * @return mixed
     */
    static public function getReadOnlyDBSettingsObj($db_id){
        Assert::assert(array_key_exists($db_id, self::$readonlydb_settings_obj_arr));

        return self::$readonlydb_settings_obj_arr[$db_id];
    }

    /**
     * @deprecated use Tableset instead
     * @return array
     */
    static public function getDBSettingsObjArr(){
        return self::$dbsettings_obj_arr;
    }

    /**
     * @param $dbtableset_id
     * @param DBTablesetObj $tableset_obj
     */
    static public function setDBTablesetObj($dbtableset_id, DBTablesetObj $tableset_obj){
        self::$dbtableset_obj_arr[$dbtableset_id] = $tableset_obj;
    }

    /**
     * @param $dbtableset_id
     * @return mixed
     */
    static public function getDBTablesetObj($dbtableset_id){
        Assert::assert(array_key_exists($dbtableset_id, self::$dbtableset_obj_arr));

        return self::$dbtableset_obj_arr[$dbtableset_id];
    }

    /**
     * @return array
     */
    static public function getDBTablesetObjArr(){
        return self::$dbtableset_obj_arr;
    }
}