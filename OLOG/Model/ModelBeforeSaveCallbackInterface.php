<?php

namespace OLOG\Model;

Interface ModelBeforeSaveCallbackInterface {

    /**
     * @param $id
     */
    public static function beforeSave($id);
}