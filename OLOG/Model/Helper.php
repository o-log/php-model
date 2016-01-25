<?php

namespace OLOG\Model;

class Helper {
    /**
     * Глобализация имен классов не является абсолютно необходимой, но в большом проекте проще и безопаснее всегда использовать глобальные имена классов.
     * Пых всегда возвращает имена классов полные (со всеми неймспейсами), но не глобальные (без \ в начале).
     * @param $class_name
     * @return string
     */
    static public function globalizeClassName($class_name){
        if (!preg_match("@^\\\\@", $class_name)){ // если в начале имени класса нет слэша - добавляем
            $class_name = '\\' . $class_name;
        }

        return $class_name;
    }

    /**
     * @param $class_name string Принимает как глобальное, так и неглобальное имя класса.
     * @param $interface_class_name string Имя интерфейса, обязательно не глобальное!
     * @throws \Exception
     */
    static public function exceptionIfClassNotImplementsInterface($class_name, $interface_class_name)
    {
        $global_class_name = self::globalizeClassName($class_name);

        $model_class_interfaces_arr = class_implements($global_class_name);

        if (!array_key_exists($interface_class_name, $model_class_interfaces_arr)) {
            throw new \Exception('model class ' . $class_name . ' does not implement ' . $interface_class_name);
        }
    }

}