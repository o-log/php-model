<?php
declare(strict_types=1);

namespace OLOG\Model\CLI\Templates;

use Stringy\Stringy;

class GetterSetterTemplate
{
    static public function getterSetterTemplate($field_name, $php_data_type, $is_nullable, $class_name_no_namespace)
    {
        $camelized_field_name = Stringy::create($field_name)->upperCamelize();

        $type_str = $php_data_type;
        if ($is_nullable) {
            $type_str = '?' . $type_str;
        }

        ob_start();

        // code below contains some extra empty lines because php eats new line after closing php tag
        ?>

    public function get<?= $camelized_field_name ?>(): <?= $type_str ?>

    {
        return $this-><?= $field_name ?>;
    }

    public function set<?= $camelized_field_name ?>(<?= $type_str ?> $value): <?= $class_name_no_namespace ?>

    {
        $this-><?= $field_name ?> = $value;
        return $this;
    }
<?php
        return ob_get_clean();
    }
}
