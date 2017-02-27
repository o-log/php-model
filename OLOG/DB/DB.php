<?php

namespace OLOG\DB;
use OLOG\Assert;

/**
 * Class DB
 * @package DB
 * Represents a single database connection.
 */
class DB
{
    /**
     * Throws PDOException on failure.
     * @var \PDO|null
     */
    protected $pdo_obj = null;

    /**
     * @return null|\PDO
     */
    public function getPdoObj()
    {
        return $this->pdo_obj;
    }

    /**
     * @param null|\PDO $pdo_obj
     */
    public function setPdoObj($pdo_obj)
    {
        $this->pdo_obj = $pdo_obj;
    }

    /**
     * Умеет или сам подключаться к БД или использовать объект PDO из указанного DBConnector (при этом можно использовать одно подключение для нескольких объектов БД (если они смотрят на одну физическую базу) чтобы правильно работали транзакции)
     * @param DBSettings $db_settings_obj
     */
    public function __construct(DBSettings $db_settings_obj)
    {
        //$this->pdo = new \PDO('mysql:host=' . $db_conf_arr['host'] . ';dbname=' . $db_conf_arr['db_name'] . ';charset=utf8', $db_conf_arr['user'], $db_conf_arr['pass']);
        //$this->pdo = new \PDO('pgsql:dbname='. $db_conf_arr['db_name'] . ';host=' . $db_conf_arr['host'] . ';user='.$db_conf_arr['user'].';password='.$db_conf_arr['pass']);

        if ($db_settings_obj->getDbConnectorId() != ''){
            $dbconnector_obj = DBConfig::getDBConnectorObj($db_settings_obj->getDbConnectorId());
            Assert::assert($dbconnector_obj);
            $this->setPdoObj($dbconnector_obj->getPdoObj());
        } else {
            $pdo_obj = new \PDO('mysql:host=' . $db_settings_obj->getServerHost() . ';dbname=' . $db_settings_obj->getDbName() . ';charset=utf8', $db_settings_obj->getUser(), $db_settings_obj->getPassword());
            $pdo_obj->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->setPdoObj($pdo_obj);
        }
    }

    /**
     * Throws PDOException on failure.
     * @param $query
     * @param array $params_arr
     * @return \PDOStatement
     * @throws \Exception
     */
    public function query($query, $params_arr = array())
    {
        $statement_obj = $this->getPdoObj()->prepare($query);

        $params_prepared_arr = array();
        foreach($params_arr as $key => $param_value) {
            if(is_object($param_value)){
                throw new \Exception($key . ' passed object');
            }
            /**
             * Хак для БД Postgres:
             * PDO кастит false в пустую строку и postgres не позволяет в поле типа boolean записать её.
             */
            /*
            if($param_value === false) {
                $params_prepared_arr[$key] = 'f';
            } elseif ($param_value === true) {
                $params_prepared_arr[$key] = 't';
            } else {
            */
                $params_prepared_arr[$key] = $param_value;
            //}
        }

        if (!$statement_obj->execute($params_prepared_arr)) {
            throw new \Exception('query execute failed');
        }

        return $statement_obj;
    }

    /**
     * @param $db_sequence_name
     * @return string
     */
    public function lastInsertId($db_sequence_name)
    {
        return $this->getPdoObj()->lastInsertId($db_sequence_name);
    }

    public function inTransaction()
    {
        return $this->getPdoObj()->inTransaction();
    }

    public function beginTransaction()
    {
        return $this->getPdoObj()->beginTransaction();
    }

    public function commit()
    {
        $this->getPdoObj()->commit();
    }

    public function rollBack()
    {
        $this->getPdoObj()->rollBack();
    }
}