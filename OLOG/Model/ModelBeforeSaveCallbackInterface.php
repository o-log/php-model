<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Model;

Interface ModelBeforeSaveCallbackInterface {

    /**
     * @param $id
     */
    public static function beforeSave($id);
}
