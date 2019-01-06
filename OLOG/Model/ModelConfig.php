<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Model;

class ModelConfig {
    static protected $after_save_subscribers = [];
    static protected $before_save_subscribers = [];
    static protected $ignore_missing_properties_on_load = false;
    static protected $ignore_missing_properties_on_save = false;

    public static function ignoreMissingPropertiesOnSave()
    {
        return self::$ignore_missing_properties_on_save;
    }

    public static function setIgnoreMissingPropertiesOnSave($ignore_missing_properties_on_save)
    {
        self::$ignore_missing_properties_on_save = $ignore_missing_properties_on_save;
    }

    public static function ignoreMissingPropertiesOnLoad()
    {
        return self::$ignore_missing_properties_on_load;
    }

    public static function setIgnoreMissingPropertiesOnLoad($ignore_missing_properties_on_load)
    {
        self::$ignore_missing_properties_on_load = $ignore_missing_properties_on_load;
    }
}
