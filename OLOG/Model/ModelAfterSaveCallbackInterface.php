<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Model;

Interface ModelAfterSaveCallbackInterface {

    /**
     * @param $id
     */
    public static function afterSave($id);
}
