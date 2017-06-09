<?php

namespace OLOG\DB;

use OLOG\Assert;

class DBConfig
{
    static protected $dbsettings_obj_arr = [];
    static protected $readonlydb_settings_obj_arr = [];
    static protected $dbconnector_obj_arr = [];

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

    static public function setDBSettingsObj($db_id, DBSettings $settings_obj, DBSettings $readonlydb_settings_obj = null){
        self::$dbsettings_obj_arr[$db_id] = $settings_obj;

        if (!is_null($readonlydb_settings_obj)) {
            self::$readonlydb_settings_obj_arr[$db_id] = $readonlydb_settings_obj;
        }
    }

    /**
     * @param $db_id
     * @return DBSettings
     */
    static public function getDBSettingsObj($db_id){
        Assert::assert(array_key_exists($db_id, self::$dbsettings_obj_arr));

        return self::$dbsettings_obj_arr[$db_id];
    }

    static public function getReadOnlyDBSettingsObj($db_id){
        Assert::assert(array_key_exists($db_id, self::$readonlydb_settings_obj_arr));

        return self::$readonlydb_settings_obj_arr[$db_id];
    }

    static public function getDBSettingsObjArr(){
        return self::$dbsettings_obj_arr;
    }
}