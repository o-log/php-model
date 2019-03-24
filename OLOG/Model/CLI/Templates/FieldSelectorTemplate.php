<?php
declare(strict_types=1);


namespace OLOG\Model\CLI\Templates;


class FieldSelectorTemplate
{
    static public function selectorTemplate(){
        return <<<'EOT'

    /**
     * @return #CLASS_NAME#[]
     */
    static public function for#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#($#FIELDTEMPLATE_FIELD_NAME#, int $limit = #SELECTOR_PAGE_SIZE#, int $offset = 0): array
    {
        return self::idsToObjs(self::idsFor#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#($#FIELDTEMPLATE_FIELD_NAME#, $limit, $offset));
    }

    static public function idsFor#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#($#FIELDTEMPLATE_FIELD_NAME#, $limit = #SELECTOR_PAGE_SIZE#, $offset = 0): array
    {
        if (is_null($#FIELDTEMPLATE_FIELD_NAME#)){
            throw new \Exception('NULL values not supported in selector.');
        }

        return \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME .
            ' where ' . self::#FIELDTEMPLATE_FIELD_CONSTANT# . '=?' .
            ' order by ' . self::_CREATED_AT_TS . ' desc limit ? offset ?',
            [$#FIELDTEMPLATE_FIELD_NAME#, $limit, $offset]
        );
    }

EOT;
    }
}
