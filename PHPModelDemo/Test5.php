<?php
declare(strict_types=1);

namespace PHPModelDemo;

use OLOG\Model\ActiveRecordInterface;
use OLOG\Model\ActiveRecordTrait;

class Test5 implements
    ActiveRecordInterface
{
    use ActiveRecordTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'phpmodeldemo_test5';

    const _CREATED_AT_TS = 'created_at_ts';
    protected $created_at_ts;
    const _COMMENT_TEXT = 'comment_text';
    protected $comment_text;
    const _RANDINT = 'randint';
    protected $randint;
    const _ID = 'id';
    protected $id;

    /**
     * @return Test5[]
     */
    static public function forRandint($randint, int $limit = 30, int $offset = 0): array
    {
        return self::idsToObjs(self::idsForRandint($randint, $limit, $offset));
    }

    static public function idsForRandint($randint, $limit = 30, $offset = 0): array
    {
        if (is_null($randint)){
            throw new \Exception('NULL values not supported in selector.');
        }

        return \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME .
            ' where ' . self::_RANDINT . '=?' .
            ' order by ' . self::_CREATED_AT_TS . ' desc limit ? offset ?',
            [$randint, $limit, $offset]
        );
    }


    public function getRandint(): int    {
        return $this->randint;
    }

    public function setRandint(int $value): Test5    {
        $this->randint = $value;
        return $this;
    }



    /**
     * @return Test5[]
     */
    static public function all(int $limit = 30, int $offset = 0): array {
        return self::idsToObjs(self::ids($limit, $offset));
    }

    static public function ids($limit = 30, $offset = 0): array {
        return \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME .
            ' order by ' . self::_CREATED_AT_TS . ' desc limit ? offset ?',
            [$limit, $offset]
        );
    }


    public function getCommentText(): ?string
    {
        return $this->comment_text;
    }

    public function setCommentText(?string $value): Test5
    {
        $this->comment_text = $value;
        return $this;
    }



    public function __construct(){
        $this->created_at_ts = time();
        $this->randint = rand(0, 10);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAtTs(): int
    {
        return $this->created_at_ts;
    }
}
