<?php

namespace Tests;

class TestNode implements
    \OLOG\Model\ActiveRecordInterface
{
    use \OLOG\Model\ActiveRecordTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'modeltestnode';

    public $title = "";
    public $body = "";
    public $created_at_ts = '';
    protected $id;

    public function __construct()
    {
        $this->created_at_ts = time();
    }

    public function beforeSave(): void
    {
        $this->body = $this->title . $this->title;
    }

    public function afterSave(): void
    {
        $term_to_node_ids_arr = TestTermToNode::getIdsArrForNodeIdByCreatedAtDesc($this->getId());
        foreach ($term_to_node_ids_arr as $term_to_node_id){
            $term_to_node_obj = TestTermToNode::factory($term_to_node_id);
            $term_to_node_obj->created_at_ts = $this->created_at_ts;
            $term_to_node_obj->save();
        }

        $this->removeFromFactoryCache();
    }

    public function getId()
    {
        return $this->id;
    }
}
