<?php

namespace PHPModelDemo;

class DemoNode implements
    \OLOG\Model\InterfaceFactory,
    \OLOG\Model\InterfaceLoad,
    \OLOG\Model\InterfaceSave,
    \OLOG\Model\InterfaceDelete
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecord;
    use \OLOG\Model\ProtectProperties;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'modeldemonode';

    protected $id;
    protected $title = "";
    protected $body = "";
    protected $created_at_ts = '';

    public function __construct(){
        $this->created_at_ts = time();
    }

    /**
     * overrides factoryTrait method
     */
    public function afterUpdate()
    {
        $term_to_node_ids_arr = DemoTermToNode::getIdsArrForNodeIdByCreatedAtDesc($this->getId());
        foreach ($term_to_node_ids_arr as $term_to_node_id){
            $term_to_node_obj = DemoTermToNode::factory($term_to_node_id);
            $term_to_node_obj->setCreatedAtTs($this->getCreatedAtTs());
            $term_to_node_obj->save();
        }
        
        $this->removeFromFactoryCache();
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