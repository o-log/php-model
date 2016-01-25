<?php

namespace Cebera\DB;

/**
 * Class DBFactory
 * @package DB
 * A connection pool.
 */
class DBFactory
{
    /**
     * @param $db_id
     * @return null|\Cebera\DB\DB
     */
    static public function getDB($db_id)
    {
        static $pdo_arr = array();

        // check static cache
        if (isset($pdo_arr[$db_id])) {
            return $pdo_arr[$db_id];
        }

        // find config
        $databases_conf_arr = \Cebera\ConfWrapper::value('db');

        if (!is_array($databases_conf_arr)) {
            return null;
        }

        if (!array_key_exists($db_id, $databases_conf_arr)) {
            return null;
        }

        $db_conf_arr = $databases_conf_arr[$db_id];

        // connect
        $pdo_arr[$db_id] = new \Cebera\DB\DB($db_conf_arr);
        return $pdo_arr[$db_id];
    }
}