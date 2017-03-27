<?php

namespace OLOG\Cache;

interface CacheEngineInterface
{
    static public function set($key, $value, $exp);
    static public function increment($key);
    static public function get($key);
    static public function delete($key);
}