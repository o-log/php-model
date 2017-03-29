<?php

namespace OLOG\Cache;

class CacheServerSettings
{
    protected $host;
    protected $port;

    public function __construct($host, $port)
    {
        $this->setHost($host);
        $this->setPort($port);
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }


}