<?php

namespace OLOG\DB;

use OLOG\Assert;

class DBConfig
{
    static protected $dbsettings_obj_arr = [];

    static public function setDBSettingsObj($db_id, DBSettings $settings_obj){
        self::$dbsettings_obj_arr[$db_id] = $settings_obj;
    }

    /**
     * @param $db_id
     * @return DBSettings
     */
    static public function getDBSettingsObj($db_id){
        Assert::assert(array_key_exists($db_id, self::$dbsettings_obj_arr));

        return self::$dbsettings_obj_arr[$db_id];
    }

    static public function getDBSettingsObjArr(){
        return self::$dbsettings_obj_arr;
    }
}