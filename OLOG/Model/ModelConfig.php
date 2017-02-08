<?php

namespace OLOG\Model;

use OLOG\CheckClassInterfaces;

class ModelConfig {

    static protected $after_save_subscribers_arr = [];
    static protected $before_save_subscribers_arr = [];

    public static function getBeforeSavesubscribers(string $model_class_name) {
        return self::$before_save_subscribers_arr[$model_class_name] ?? [];
    }

    public static function addBeforeSaveSubscriber(string $model_class_name, string $before_save_callback_class_name) {
        CheckClassInterfaces::exceptionIfClassNotImplementsInterface($before_save_callback_class_name, ModelBeforeSaveCallbackInterface::class);
        if (!isset(self::$before_save_subscribers_arr[$model_class_name])) {
            self::$before_save_subscribers_arr[$model_class_name] = [];
        }

        self::$before_save_subscribers_arr[$model_class_name][] = $before_save_callback_class_name;
    }

    public static function addAfterSaveSubscriber(string $model_class_name, string $after_save_callback_class_name) {
        CheckClassInterfaces::exceptionIfClassNotImplementsInterface($after_save_callback_class_name, ModelAfterSaveCallbackInterface::class);
        if (!isset(self::$after_save_subscribers_arr[$model_class_name])) {
            self::$after_save_subscribers_arr[$model_class_name] = [];
        }

        self::$after_save_subscribers_arr[$model_class_name][] = $after_save_callback_class_name;
    }

    public static function getAfterSaveSubscribers(string $model_class_name) {
        return self::$after_save_subscribers_arr[$model_class_name] ?? [];
    }
}