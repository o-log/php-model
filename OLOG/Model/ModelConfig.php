<?php

namespace OLOG\Model;

use OLOG\CheckClassInterfaces;

class ModelConfig
{
    static protected $after_save_subscribers_arr = [];
    static protected $before_save_subscribers_arr = [];
    static protected $after_delete_subscribers_arr = [];

    /**
     * @param string $model_class_name
     * @return array|ModelBeforeSaveCallbackInterface[]
     */
    public static function getBeforeSaveSubscribersArr(string $model_class_name)
    {
        return self::$before_save_subscribers_arr[$model_class_name] ?? [];
    }

    public static function addBeforeSaveSubscriber(string $model_class_name, string $before_save_callback_class_name)
    {
        CheckClassInterfaces::exceptionIfClassNotImplementsInterface($before_save_callback_class_name, ModelBeforeSaveCallbackInterface::class);
        if (!isset(self::$before_save_subscribers_arr[$model_class_name])) {
            self::$before_save_subscribers_arr[$model_class_name] = [];
        }

        self::$before_save_subscribers_arr[$model_class_name][] = $before_save_callback_class_name;
    }

    public static function addAfterSaveSubscriber(string $model_class_name, string $after_save_callback_class_name)
    {
        CheckClassInterfaces::exceptionIfClassNotImplementsInterface($after_save_callback_class_name, ModelAfterSaveCallbackInterface::class);
        if (!isset(self::$after_save_subscribers_arr[$model_class_name])) {
            self::$after_save_subscribers_arr[$model_class_name] = [];
        }

        self::$after_save_subscribers_arr[$model_class_name][] = $after_save_callback_class_name;
    }

    /**
     * @param string $model_class_name
     * @return array|ModelAfterSaveCallbackInterface[]
     */
    public static function getAfterSaveSubscribersArr(string $model_class_name)
    {
        return self::$after_save_subscribers_arr[$model_class_name] ?? [];
    }

    public static function addAfterDeleteSubscriber(string $model_class_name, string $after_save_callback_class_name)
    {
        CheckClassInterfaces::exceptionIfClassNotImplementsInterface($after_save_callback_class_name, ModelAfterDeleteCallbackInterface::class);
        if (!isset(self::$after_delete_subscribers_arr[$model_class_name])) {
            self::$after_delete_subscribers_arr[$model_class_name] = [];
        }

        self::$after_delete_subscribers_arr[$model_class_name][] = $after_save_callback_class_name;
    }

    /**
     * @param string $model_class_name
     * @return array|ModelAfterDeleteCallbackInterface[]
     */
    public static function getAfterDeleteSubscribersArr(string $model_class_name)
    {
        return self::$after_delete_subscribers_arr[$model_class_name] ?? [];
    }
}