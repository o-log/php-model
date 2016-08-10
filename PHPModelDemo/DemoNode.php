<?php

namespace PHPModelDemo;

class DemoNode implements
    \OLOG\Model\InterfaceFactory,
    \OLOG\Model\InterfaceLoad,
    \OLOG\Model\InterfaceSave,
    \OLOG\Model\InterfaceDelete
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecordTrait;
    use \OLOG\Model\ProtectPropertiesTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'modeldemonode';

    protected $term_id;
    protected $title = "";
    protected $body = "";
    protected $created_at_ts = '';
    protected $id;

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTermId(){
        return $this->term_id;
    }

    public function setTermId($value){
        $this->term_id = $value;
    }


    public function __construct(){
        $this->created_at_ts = time();
    }

    public function beforeSave(){
        $this->setBody($this->getTitle() . $this->getTitle());
    }

    /**
     * overrides factoryTrait method
     */
    public function afterSave()
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