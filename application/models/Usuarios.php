<?php

class Application_Model_Usuarios
{

    protected $db;

    function __construct()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
    }

    public function getPerfis()
    {
        $sql = "SELECT id,role FROM zf_perfis WHERE 1";
        $this->db->setFetchMode(Zend_Db::FETCH_OBJ);
        $result = $this->db->fetchAll($sql);
        return $result;
    }
    
    /**
     * listId
     * @return array()
     * 
     */
    public function listId(){
        $list = $this->getPerfis();
        $list_options = array();
        
        foreach ($list as $value){
            $list_options[] = $value->id;
        }
        
        return $list_options;        
    }
    
    /**
     * listName
     * @return array()
     *
     */
    public function listName(){
        $list = $this->getPerfis();
        $list_options = array();
    
        foreach ($list as $value){
            $list_options[] = $value->role;
        }
    
        return $list_options;
    }
}