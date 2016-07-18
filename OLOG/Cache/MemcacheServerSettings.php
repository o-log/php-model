<?php

namespace OLOG\Cache;

class MemcacheServerSettings
{
    protected $host;
    protected $port;

    public function __construct($host, $port)
    {
        $this->setHost($host);
        $this->setPort($port);
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }


}