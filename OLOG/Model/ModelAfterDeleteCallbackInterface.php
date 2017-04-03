<?php

namespace OLOG\Model;

Interface ModelAfterDeleteCallbackInterface {

    /**
     * @param $id
     */
    public static function afterDelete($id);
}