<?php

namespace OLOG\Model\CLI;

class FieldDataType
{
    public $sql_type_name;
    public $can_be_null;
    public $can_have_default_value;

    public function __construct($sql_type_name, $can_be_null, $can_have_default_value){
        $this->sql_type_name = $sql_type_name;
        $this->can_be_null = $can_be_null;
        $this->can_have_default_value = $can_have_default_value;
    }
}