<?php

namespace OLOG\Model;

use OLOG\CheckClassInterfaces;

class ModelConfig
{
    static protected $after_save_subscribers_arr = [];
    static protected $before_save_subscribers_arr = [];
    static protected $on_after_delete_subscribers_arr = [];

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

    public static function addOnAfterDeleteSubscriber(string $model_class_name, string $on_after_delete_callback_class_name)
    {
        CheckClassInterfaces::exceptionIfClassNotImplementsInterface($on_after_delete_callback_class_name, ModelOnAfterDeleteCallbackInterface::class);
        if (!isset(self::$on_after_delete_subscribers_arr[$model_class_name])) {
            self::$on_after_delete_subscribers_arr[$model_class_name] = [];
        }

        self::$on_after_delete_subscribers_arr[$model_class_name][] = $on_after_delete_callback_class_name;
    }

    /**
     * @param string $model_class_name
     * @return array|ModelOnAfterDeleteCallbackInterface[]
     */
    public static function getOnAfterDeleteSubscribersArr(string $model_class_name)
    {
        return self::$on_after_delete_subscribers_arr[$model_class_name] ?? [];
    }
}