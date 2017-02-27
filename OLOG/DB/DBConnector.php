<?php

namespace OLOG\DB;

class DBConnector
{
    protected $server_host;
    protected $db_name;
    protected $user;
    protected $password;
    protected $pdo_obj = null;
    protected $pdo_is_connected = false;

    public function __construct($server_host, $db_name, $user, $password)
    {
        $this->server_host = $server_host;
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
     * @return mixed
     */
    public function getServerHost()
    {
        return $this->server_host;
    }

    /**
     * @param mixed $server_host
     */
    public function setServerHost($server_host)
    {
        $this->server_host = $server_host;
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