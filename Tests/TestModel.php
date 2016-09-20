<?php

namespace Tests;

class TestModel implements
    \OLOG\Model\InterfaceFactory,
    \OLOG\Model\InterfaceLoad,
    \OLOG\Model\InterfaceSave,
    \OLOG\Model\InterfaceDelete
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecordTrait;
    use \OLOG\Model\ProtectPropertiesTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'tests_testmodel';

    protected $created_at_ts; // initialized by constructor
    protected $title = "";
    protected $disable_delete = 0;
    protected $throw_exception_after_delete = 0;
    protected $after_save_counter = 0;
    protected $id;

    public function getAfterSaveCounter(){
        return $this->after_save_counter;
    }

    public function setAfterSaveCounter($value){
        $this->after_save_counter = $value;
    }

    public function getThrowExceptionAfterDelete(){
        return $this->throw_exception_after_delete;
    }

    public function setThrowExceptionAfterDelete($value){
        $this->throw_exception_after_delete = $value;
    }

    public function afterDelete(){
        if ($this->getThrowExceptionAfterDelete()){
            throw new \Exception('After delete');
        }
    }

    public function getDisableDelete(){
        return $this->disable_delete;
    }

    public function setDisableDelete($value){
        $this->disable_delete = $value;
    }

    public function getTitle(){
        return $this->title;
    }

    public function setTitle($value){
        $this->title = $value;
    }

    public function afterSave(){
        $this->removeFromFactoryCache();

        $this->setAfterSaveCounter($this->getAfterSaveCounter() + 1);
        $this->save();
    }

    static public function getAllIdsArrByCreatedAtDesc($offset = 0, $page_size = 30){
        $ids_arr = \OLOG\DB\DBWrapper::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset)
        );
        return $ids_arr;
    }

    public function __construct(){
        $this->created_at_ts = time();
    }

    public function canDelete(&$message){
        if ($this->getDisableDelete()){
            $message = 'Delete disabled';
            return false;
        }

        return true;
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
     * @param string $timestamp
     */
    public function setCreatedAtTs($timestamp)
    {
        $this->created_at_ts = $timestamp;
    }
}