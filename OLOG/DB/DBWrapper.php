<?php

namespace OLOG\DB;

class DBWrapper
{
    /**
     *
     * @param string $db_name
     * @param string $query
     * @param array $params_arr
     * @return \PDOStatement
     * @throws \Exception
     */
    static public function query($db_name, $query, $params_arr = array())
    {
        $db_obj = \OLOG\DB\DBFactory::getDB($db_name);
        if (!$db_obj) {
            throw new \Exception('getDB failed');
        }

        try {
            return $db_obj->query($query, $params_arr);
        }
        catch(\PDOException $e) {
            $uri = '';

            // may be not present in command line scripts
            if (array_key_exists('REQUEST_URI', $_SERVER)){
                $uri = "\r\nUrl: " . $_SERVER['REQUEST_URI'];
            }

            throw new \PDOException($uri . "\r\n".$e->getMessage());
        }
    }

    static public function readObjects($db_name, $query, $params_arr = array(), $field_name_for_keys = '')
    {
        $statement_obj = self::query($db_name, $query, $params_arr);

        $output_arr = array();

        while (($row_obj = $statement_obj->fetchObject()) !== false) {
            if ($field_name_for_keys != '') {
                $key = $row_obj->$field_name_for_keys;
                $output_arr[$key] = $row_obj;
            }
            else {
                $output_arr[] = $row_obj;
            }
        }

        return $output_arr;
    }

    static public function readObject($db_name, $query, $params_arr = array()) {
        $statement_obj = self::query($db_name, $query, $params_arr);

        return $statement_obj->fetch(\PDO::FETCH_OBJ);
    }


    /**
     * Предназначен для миграции старого кода, использовать только при крайней необходимости.
     * @deprecated
     * @param $db_name
     * @param $query
     * @param array $params_arr
     * @return array
     */
    static public function readAssoc($db_name, $query, $params_arr = array())
    {
        $statement_obj = self::query($db_name, $query, $params_arr);

        $output_arr = array();

        while (($row_arr = $statement_obj->fetch(\PDO::FETCH_ASSOC)) !== false) {
            $output_arr[] = $row_arr;
        }

        return $output_arr;
    }

    static public function readColumn($db_id, $query, $params_arr = array())
    {
        $statement_obj = self::query($db_id, $query, $params_arr);

        $output_arr = array();

        while (($field = $statement_obj->fetch(\PDO::FETCH_COLUMN)) !== false) {
            $output_arr[] = $field;
        }

        return $output_arr;
    }

    static public function readAssocRow($db_name, $query, $params_arr = array())
    {
        $statement_obj = self::query($db_name, $query, $params_arr);

        return $statement_obj->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Возвращает false при ошибке или если нет записей.
     * @param $db_name
     * @param $query
     * @param array $params_arr
     * @return mixed
     */
    static public function readField($db_name, $query, $params_arr = array())
    {
        $statement_obj = self::query($db_name, $query, $params_arr);
        return $statement_obj->fetch(\PDO::FETCH_COLUMN);
    }

    static public function lastInsertId($db_name, $db_sequence_name)
    {
        $db_obj = \OLOG\DB\DBFactory::getDB($db_name);
        if (!$db_obj) {
            throw new \Exception('getDB failed');
        }

        return $db_obj->lastInsertId($db_sequence_name);
   }

    static public function beginTransaction($db_name)
    {
        $db_obj = \OLOG\DB\DBFactory::getDB($db_name);
        if (!$db_obj) {
            throw new \Exception('getDB failed');
        }

        $db_obj->beginTransaction();
    }

    static public function commitTransaction($db_name)
    {
        $db_obj = \OLOG\DB\DBFactory::getDB($db_name);
        if (!$db_obj) {
            throw new \Exception('getDB failed');
        }

        $db_obj->commit();
    }

    static public function rollBackTransaction($db_name)
    {
        $db_obj = \OLOG\DB\DBFactory::getDB($db_name);
        if (!$db_obj) {
            throw new \Exception('getDB failed');
        }

        $db_obj->rollBack();
    }
}