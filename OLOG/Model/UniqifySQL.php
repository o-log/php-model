<?php
declare(strict_types=1);

namespace OLOG\Model;

class UniqifySQL
{
    static public function addDatetimeComment(string $sql): string
    {
        return $sql . ' /* ' . date('Y.m.d H:i:s'). ' */';
    }
}
