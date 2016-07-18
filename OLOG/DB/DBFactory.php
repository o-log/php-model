<?php

namespace OLOG\DB;

/**
 * Class DBFactory
 * @package DB
 * A connection pool.
 */
class DBFactory
{
    /**
     * @param $db_id
     * @return null|\OLOG\DB\DB
     */
    static public function getDB($db_id)
    {
        static $pdo_arr = array();

        // check static cache
        if (isset($pdo_arr[$db_id])) {
            return $pdo_arr[$db_id];
        }

        $db_settings_obj = DBConfig::getDBSettingsObj($db_id);

        $pdo_arr[$db_id] = new \OLOG\DB\DB($db_settings_obj);
        return $pdo_arr[$db_id];
    }

    /*
    static public function getConfigArr($db_id){
        // find config
        $databases_conf_arr = \OLOG\ConfWrapper::value(\OLOG\Model\ModelConstants::MODULE_CONFIG_ROOT_KEY . '.db');

        if (!is_array($databases_conf_arr)) {
            return null;
        }

        if (!array_key_exists($db_id, $databases_conf_arr)) {
            return null;
        }

        $db_conf_arr = $databases_conf_arr[$db_id];
        return $db_conf_arr;
    }
    */
}