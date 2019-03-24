<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Model\CLI;

class FieldDataType
{
    public $title;
    public $sql_type_name;
    public $php_type_name;
//    public $can_be_not_null;
    public $can_have_default_value;
    public $can_have_collate;

    public function __construct(
        $title,
        $php_type_name,
        $sql_type_name, /*$can_be_not_null, */
        $can_have_default_value,
        $can_have_collate
    ){
        $this->title = $title;
        $this->sql_type_name = $sql_type_name;
        $this->php_type_name = $php_type_name;
//        $this->can_be_not_null = $can_be_not_null;
        $this->can_have_default_value = $can_have_default_value;
        $this->can_have_collate = $can_have_collate;
    }

    public function render(){
        return $this->title
            . ': SQL '
            . $this->sql_type_name
            . ', '/* . ($this->can_be_not_null ? 'can be null' : 'can not be null') . ', '*/
            . ($this->can_have_default_value ? 'can have default value' : 'can not have default value')
            . ', '
            . ($this->can_have_collate ? 'can have collate' : 'can not have collate');
    }
}
