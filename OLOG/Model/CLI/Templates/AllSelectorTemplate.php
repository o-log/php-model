<?php
declare(strict_types=1);


namespace OLOG\Model\CLI\Templates;


class AllSelectorTemplate
{
    static public function selectorTemplate(){
        return <<<'EOT'

    /**
     * @return #CLASS_NAME#[]
     */
    static public function all(int $limit = #SELECTOR_PAGE_SIZE#, int $offset = 0): array {
        return self::idsToObjs(self::ids($limit, $offset));
    }

    static public function ids($limit = #SELECTOR_PAGE_SIZE#, $offset = 0): array {
        return \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME .
            ' order by ' . self::_CREATED_AT_TS . ' desc limit ? offset ?',
            [$limit, $offset]
        );
    }

EOT;
    }
}
