<?php

namespace OLOG\Model;

Interface ModelOnAfterDeleteCallbackInterface {

    /**
     * @param $obj
     * @return mixed
     */
    public static function onAfterDelete($obj);
}