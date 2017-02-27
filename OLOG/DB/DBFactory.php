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
}