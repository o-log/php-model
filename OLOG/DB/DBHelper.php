<?php


namespace Cebera\DB;

/**
 * Class DBHelper
 * @package DB
 */
class DBHelper
{
    public static function prepareOrderBy($order_by)
    {
        return preg_replace('@[^a-z\s0-9,\.()]@i', '', $order_by);
    }
}