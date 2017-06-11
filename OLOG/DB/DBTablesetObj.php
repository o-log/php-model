<?php

namespace OLOG\DB;

class DBTablesetObj
{
    protected $sql_file_path_in_project_root = '';
    protected $readwrite_db_connector_id = '';
    protected $readonly_db_connector_id = '';

    public function __construct($readwrite_db_connector_id, $sql_file_path_in_project_root = '', $readonly_db_connector_id = '')
    {
        $this->sql_file_path_in_project_root = $sql_file_path_in_project_root;
        $this->readwrite_db_connector_id = $readwrite_db_connector_id;
        $this->readonly_db_connector_id = $readonly_db_connector_id;
    }

    /**
     * @return string
     */
    public function getSqlFilePathInProjectRoot()
    {
        return $this->sql_file_path_in_project_root;
    }

    /**
     * @param string $sql_file_path_in_project_root
     */
    public function setSqlFilePathInProjectRoot($sql_file_path_in_project_root)
    {
        $this->sql_file_path_in_project_root = $sql_file_path_in_project_root;
    }

    /**
     * @return string
     */
    public function getReadwriteDbConnectorId()
    {
        return $this->readwrite_db_connector_id;
    }

    /**
     * @param string $readwrite_db_connector_id
     */
    public function setReadwriteDbConnectorId($readwrite_db_connector_id)
    {
        $this->readwrite_db_connector_id = $readwrite_db_connector_id;
    }

    /**
     * @return string
     */
    public function getReadonlyDbConnectorId()
    {
        return $this->readonly_db_connector_id;
    }

    /**
     * @param string $readonly_db_connector_id
     */
    public function setReadonlyDbConnectorId($readonly_db_connector_id)
    {
        $this->readonly_db_connector_id = $readonly_db_connector_id;
    }


}