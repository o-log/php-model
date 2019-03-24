<?php
declare(strict_types=1);


namespace OLOG\Model\CLI\Templates;


class CounterTemplate
{
    static public function counterTemplate(){
        return <<<'EOT'

    static public function countFor#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#($#FIELDTEMPLATE_FIELD_NAME#): int
    {
        if (is_null($#FIELDTEMPLATE_FIELD_NAME#)){
            throw new \Exception('NULL values not supported in counter.');
        }

        return \OLOG\DB\DB::readField(
            self::DB_ID,
            'select count(*) from ' . self::DB_TABLE_NAME .
            ' where ' . self::#FIELDTEMPLATE_FIELD_CONSTANT# . '=?',
            [$#FIELDTEMPLATE_FIELD_NAME#]
        );
    }

EOT;
    }
}
