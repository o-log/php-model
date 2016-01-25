<?php

namespace OLOG\Cache;

class Cache
{
    public $keyPrefix = 'prefix';

    /**
     * @var boolean whether to use memcached or memcache as the underlying caching extension.
     * If true {@link http://pecl.php.net/package/memcached memcached} will be used.
     * If false {@link http://pecl.php.net/package/memcache memcache}. will be used.
     * Defaults to false.
     */
    public $useMemcached = false;
    /**
     * @var \Memcache the Memcache instance
     */
    public $_cache = null;
    public $connected = false;

    /**
     * @var array list of memcache server configurations
     */
    //private $_servers = array();

    public function __construct()
    {
        $this->connected = false;

        $this->init();
    }

    /**
     * Initializes this application component.
     * This method is required by the {@link IApplicationComponent} interface.
     * It creates the memcache instance and adds memcache servers.
     * @throws CException if memcache extension is not loaded
     */
    public function init()
    {
        $conf = \OLOG\ConfWrapper::value('cache');

        if (!$conf){
            return;
        }

        // TODO: rewrite with cache wrapper
            if (array_key_exists('use_memcached', $conf)) {
                $this->useMemcached = $conf['use_memcached'];
            }

        $servers = array();

        // TODO: rewrite with cache wrapper
        if (!array_key_exists('servers', $conf)){
            return;
        }
        $servers = $conf['servers'];

        $this->createCache();

        // TODO: validate _cache

        if (count($servers)) {
            foreach ($servers as $server) {
                $server['weight'] = 1;

                if ($this->useMemcached) {
                    if (!$this->_cache->addServer($server['host'], $server['port'], $server['weight'])){
                        throw new \Exception('Server add failed');
                    }
                } else {
                    if (!$this->_cache->addServer($server['host'], $server['port'], true)){
                        throw new \Exception('Server add failed');
                    }

                    $this->_cache->setCompressThreshold(5000, 0.2);
                }

                $this->connected = true;
            }
        }
    }

    /**
     * @return mixed the memcache instance (or memcached if {@link useMemcached} is true) used by this component.
     */
    public function createCache()
    {
        $this->_cache = $this->useMemcached ? new \Memcached : new \Memcache;
    }

    /**
     * @return \Memcached|\Memcache|null the memcache instance (or memcached if {@link useMemcached} is true) used by this component.
     */
    public function getConnectionObj()
    {
        return $this->_cache;
    }

    public function get($key)
    {
        return \OLOG\Cache\Dmemcache::dmemcache_get($key);
    }

    public function delete($key)
    {

        //if (isset($_GET['use_dmemcache'])){
            return \OLOG\Cache\Dmemcache::dmemcache_delete($key);
        //}

        $unique_key = $this->generateUniqueKey($key);
        //$unique_key = $key;

        // удаляем и сериализованное, и несериализ. значение. потом оставить только одно удаление

        return $this->deleteValue('__NOSER__' . $unique_key);
        //return $this->deleteValue($unique_key);
    }

    /**
     * Retrieves a value from cache with a specified key.
     * This is the implementation of the method declared in the parent class.
     * @param string $key a unique key identifying the cached value
     * @return string the value stored in cache, false if the value is not in the cache or expired.
     */
    /*
    protected function getValue($key)
    {
        return $this->_cache->get($key);
    }
    */

    /**
     * Retrieves multiple values from cache with the specified keys.
     * @param array $keys a list of keys identifying the cached values
     * @return array a list of cached values indexed by the keys
     */
    /*
    protected function getValues($keys)
    {
        return $this->useMemcached ? $this->_cache->getMulti($keys) : $this->_cache->get($keys);
    }
    */


    protected function generateUniqueKey($key)
    {
        return md5($this->keyPrefix . $key);
    }


    public function set($key, $value, $expire = -1)
    {
        // memcache debug
        /*
        $_value_size = strlen(serialize($value));
        if ($_value_size > 30000){
            error_log("C\t" . $_value_size . "\t" . $key . "\n", 3, "/tmp/mmc.log");
        }
        */

        //if (isset($_GET['use_dmemcache'])){
            return \OLOG\Cache\Dmemcache::dmemcache_set($key, $value, $expire);
        //}



        $unique_key = $this->generateUniqueKey($key);
        //$unique_key = $key;



        //return apc_store($key, $value, 60);




        // extra non-serialized set
        return $this->setValue('__NOSER__' . $unique_key, $value, $expire);
        //return $this->setValue($unique_key, serialize($value), $expire);
    }

    public function increment($key)
    {
        return \OLOG\Cache\Dmemcache::dmemcache_increment($key);
    }

    /**
     * Stores a value identified by a key in cache.
     * This is the implementation of the method declared in the parent class.
     *
     * @param string $key the key identifying the value to be cached
     * @param string $value the value to be cached
     * @param integer $expire the number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean true if the value is successfully stored into cache, false otherwise
     */
    protected function setValue($key, $value, $expire = -1)
    {
        // TODO: debug

        if ($expire == -1) {
            // TODO: придумать, как глобально правлять временем кэширования
            $expire = 60;
        }

        if ($expire > 0)
            $expire += time();
        else
            $expire = 0;

        $result = false;

        if ($this->useMemcached){
            $result = $this->_cache->set($key, $value, $expire);
            //$error = $this->_cache->getResultCode() . ": " . $this->_cache->getResultMessage();
        } else {
            $result = $this->_cache->set($key, $value, 0, $expire);
        }

        return $result;
    }

    protected function incrementValue($key)
    {
        if ($this->useMemcached){
            $result = $this->_cache->increment($key);
            //$error = $this->_cache->getResultCode() . ": " . $this->_cache->getResultMessage();
        } else {
            $result = $this->_cache->increment($key);
        }

        return $result;
    }

    /**
     * Deletes a value with the specified key from cache
     * This is the implementation of the method declared in the parent class.
     * @param string $key the key of the value to be deleted
     * @return boolean if no error happens during deletion
     */
    protected function deleteValue($key)
    {
        $result = $this->_cache->delete($key, 0);
        //$error = $this->_cache->getResultCode();

        return $result;
    }
}