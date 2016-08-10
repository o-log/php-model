<?php

namespace OLOG\Model;

trait ProtectPropertiesTrait {
    public function __set($name, $value){
        throw new \Exception('Accessing invalid property ' . $name);
    }

    public function __get($name){
        throw new \Exception('Accessing invalid property ' . $name);
    }
} 