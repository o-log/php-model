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

    public static function beforeSaveSubscribers($model_class_name) {
        return array_key_exists($model_class_name, self::$before_save_subscribers) ? self::$before_save_subscribers[$model_class_name] : [];
    }

    public static function addBeforeSaveSubscriber($model_class_name, $before_save_callback_class_name) {
        if (!is_a($before_save_callback_class_name, ModelBeforeSaveCallbackInterface::class, true)){
            throw new \Exception();
        }

        if (!isset(self::$before_save_subscribers[$model_class_name])) {
            self::$before_save_subscribers[$model_class_name] = [];
        }

        self::$before_save_subscribers[$model_class_name][] = $before_save_callback_class_name;
    }

    public static function addAfterSaveSubscriber($model_class_name, $after_save_callback_class_name) {
        if (!is_a($after_save_callback_class_name, ModelAfterSaveCallbackInterface::class, true)){
            throw new \Exception();
        }

        if (!isset(self::$after_save_subscribers[$model_class_name])) {
            self::$after_save_subscribers[$model_class_name] = [];
        }

        self::$after_save_subscribers[$model_class_name][] = $after_save_callback_class_name;
    }

    public static function afterSaveSubscribers($model_class_name) {
        return array_key_exists($model_class_name, self::$after_save_subscribers) ? self::$after_save_subscribers[$model_class_name] : [];
    }
}
