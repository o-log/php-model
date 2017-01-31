<?php

namespace OLOG\Model;

Interface ModelAfterSaveCallbackInterface {

    /**
     * @param $id
     */
    public static function afterSave($id);
}