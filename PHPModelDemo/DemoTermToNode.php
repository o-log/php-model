<?php

namespace PHPModelDemo;

class DemoTermToNode implements
    \OLOG\Model\FactoryInterface,
    \OLOG\Model\ActiveRecordInterface
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecordTrait;
    use \OLOG\Model\ProtectPropertiesTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'demotermtonode';

    protected $created_at_ts = '';
    protected $term_id;
    protected $node_id;
    protected $id;

    static public function getIdsArrForNodeIdByCreatedAtDesc($value){
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' where node_id = ? order by created_at_ts desc',
            array($value)
        );
        return $ids_arr;
    }

    public function getNodeId(){
        return $this->getnode_id;
    }

    public function setNodeId($value){
        $this->node_id = $value;
    }


    static public function getIdsArrForTermIdByCreatedAtDesc($value){
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' where term_id = ? order by created_at_ts desc',
            array($value)
        );
        return $ids_arr;
    }

    public function getTermId(){
        return $this->getterm_id;
    }

    public function setTermId($value){
        $this->term_id = $value;
    }


    static public function getAllIdsArrByCreatedAtDesc(){
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' order by created_at_ts desc'
        );
        return $ids_arr;
    }

    public function __construct(){
        $this->created_at_ts = time();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCreatedAtTs()
    {
        return $this->created_at_ts;
    }

    /**
     * @param string $title
     */
    public function setCreatedAtTs($title)
    {
        $this->created_at_ts = $title;
    }
}