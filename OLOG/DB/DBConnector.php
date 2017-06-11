<?php

namespace OLOG\DB;

class DBConnector
{
    protected $dsn;
    protected $db_name;
    protected $user;
    protected $password;
    protected $pdo_obj = null;
    protected $pdo_is_connected = false;

    public function __construct($dsn, $db_name, $user, $password)
    {
        $this->dsn = $dsn;
        $this->db_name = $db_name;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @return bool
     */
    public function isPdoIsConnected()
    {
        return $this->pdo_is_connected;
    }

    /**
     * @param bool $pdo_is_connected
     */
    public function setPdoIsConnected($pdo_is_connected)
    {
        $this->pdo_is_connected = $pdo_is_connected;
    }

    /**
     * Подключается к серверу при первом обращении за объектом PDO.
     * @return null
     */
    public function getPdoObj()
    {
        if ($this->isPdoIsConnected()) {
            return $this->pdo_obj;
        }

        $pdo_obj = new \PDO('mysql:host=' . $this->getServerHost() . ';dbname=' . $this->getDbName() . ';charset=utf8', $this->getUser(), $this->getPassword());
        $pdo_obj->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->setPdoObj($pdo_obj);
        $this->setPdoIsConnected(true);

        return $this->pdo_obj;
    }

    /**
     * @param null $pdo_obj
     */
    public function setPdoObj($pdo_obj)
    {
        $this->pdo_obj = $pdo_obj;
    }

    /**
     * @deprecated replaced with getDsn
     * @return mixed
     */
    public function getServerHost()
    {
        return $this->dsn;
    }

    /**
     * @deprecated replaced with setDsn
     * @param mixed $dsn
     */
    public function setServerHost($dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * @return mixed
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /**
     * @param mixed $dsn
     */
    public function setDsn($dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * @return mixed
     */
    public function getDbName()
    {
        return $this->db_name;
    }

    /**
     * @param mixed $db_name
     */
    public function setDbName($db_name)
    {
        $this->db_name = $db_name;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
}