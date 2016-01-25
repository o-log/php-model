<?php

namespace OLOG\Model;

trait ProtectProperties {
    public function __set($name, $value){
        throw new \Exception('Accessing invalid property ' . $name);
    }

    public function __get($name){
        throw new \Exception('Accessing invalid property ' . $name);
    }
} 