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
    const _ID = 'id';
    protected $id;

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
