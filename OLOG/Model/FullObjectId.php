<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Model;

class FullObjectId
{
    static public function getFullObjectId($obj)
    {
        if (is_null($obj)){
            return null;
        }

        if (!is_object($obj)) {
            return 'not_object';
        }
        $obj_id_parts = array();
        $obj_id_parts[] = get_class($obj);
        if (method_exists($obj, 'getId')) {
            $obj_id_parts[] = $obj->getId();
        }
        return implode('.', $obj_id_parts);
    }

}
