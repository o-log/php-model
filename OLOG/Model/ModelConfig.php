<?php

namespace OLOG\Model;

use OLOG\CheckClassInterfaces;

class ModelConfig {

    static protected $after_save_subscribers_arr = [];
    static protected $before_save_subscribers_arr = [];

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