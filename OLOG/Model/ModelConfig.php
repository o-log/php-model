<?php

namespace OLOG\Model;

use OLOG\CheckClassInterfaces;

class ModelConfig {
    static protected $after_save_subscribers_arr = [];
    static protected $before_save_subscribers_arr = [];
    static protected $ignore_missing_properties_on_load = false;
    static protected $ignore_missing_properties_on_save = false;
    static protected $cli_menu_classes_arr = [];

    public static function addCLIMenuClass($class_name){
        CheckClassInterfaces::exceptionIfClassNotImplementsInterface($class_name, InterfaceCLIMenu::class);

        self::$cli_menu_classes_arr[] = $class_name;
    }

    public static function getCLIMenuClassesArr(){
        return self::$cli_menu_classes_arr;
    }

    /**
     * @return bool
     */
    public static function isIgnoreMissingPropertiesOnSave()
    {
        return self::$ignore_missing_properties_on_save;
    }

    /**
     * @param bool $ignore_missing_properties_on_save
     */
    public static function setIgnoreMissingPropertiesOnSave($ignore_missing_properties_on_save)
    {
        self::$ignore_missing_properties_on_save = $ignore_missing_properties_on_save;
    }

    /**
     * @return bool
     */
    public static function isIgnoreMissingPropertiesOnLoad()
    {
        return self::$ignore_missing_properties_on_load;
    }

    /**
     * @param bool $ignore_missing_properties_on_load
     */
    public static function setIgnoreMissingPropertiesOnLoad($ignore_missing_properties_on_load)
    {
        self::$ignore_missing_properties_on_load = $ignore_missing_properties_on_load;
    }

    public static function getBeforeSaveSubscribersArr($model_class_name) {
        return array_key_exists($model_class_name, self::$before_save_subscribers_arr) ? self::$before_save_subscribers_arr[$model_class_name] : [];
    }

    public static function addBeforeSaveSubscriber($model_class_name, $before_save_callback_class_name) {
        CheckClassInterfaces::exceptionIfClassNotImplementsInterface($before_save_callback_class_name, ModelBeforeSaveCallbackInterface::class);
        if (!isset(self::$before_save_subscribers_arr[$model_class_name])) {
            self::$before_save_subscribers_arr[$model_class_name] = [];
        }

        self::$before_save_subscribers_arr[$model_class_name][] = $before_save_callback_class_name;
    }

    public static function addAfterSaveSubscriber($model_class_name, $after_save_callback_class_name) {
        CheckClassInterfaces::exceptionIfClassNotImplementsInterface($after_save_callback_class_name, ModelAfterSaveCallbackInterface::class);
        if (!isset(self::$after_save_subscribers_arr[$model_class_name])) {
            self::$after_save_subscribers_arr[$model_class_name] = [];
        }

        self::$after_save_subscribers_arr[$model_class_name][] = $after_save_callback_class_name;
    }

    public static function getAfterSaveSubscribersArr($model_class_name) {
        return array_key_exists($model_class_name, self::$after_save_subscribers_arr) ? self::$after_save_subscribers_arr[$model_class_name] : [];
    }
}