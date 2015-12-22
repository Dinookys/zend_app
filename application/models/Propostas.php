<?php

class Application_Model_Propostas
{
    
    protected $_name = 'zf_propostas';
    protected $_cliente_table = 'zf_clientes';    
    protected $db;
    
    function __construct()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
    }
    
    public function insert(){
        
    }
    
    public function selectAll($current_user = null){
        if($current_user){
            $sql = "SELECT p.*, c.dados_cliente FROM ". $this->_name ." as p LEFT JOIN ". $this->_cliente_table ." as c ON p.id_cliente = c.id WHERE c.created_user_id = ". CURRENT_USER_ID ." ORDER BY p.id DESC";
        }else{
            $sql = "SELECT p.*, c.dados_cliente FROM ". $this->_name ." as p LEFT JOIN ". $this->_cliente_table ." as c ON p.id_cliente = c.id ORDER BY p.id DESC";
        }
        
        try {
            $result = $this->db->fetchAll($sql,null,ZEND_DB::FETCH_OBJ);
            return $result;
            
        }catch (Zend_Exception $e){
            throw new Zend_Exception($e->getMessage());
            return false;
        }
        
    }
    
    public function update(){
        
    }
    
    /**
     * Method remove
     * @param int
     * @return boolean
     */
    public function delete($id)
    {
        try {
            $where = $this->db->quoteInto('id = ?', $id);
            $this->db->delete($this->name, $where);
            return true;
        }catch (Zend_Db_Adapter_Exception $e){
            throw new Zend_Exception($e->getMessage());
        }
    }
    
    /**
     * trash atualiza estado do item
     * @param int $id
     * @param int $state
     * @throws Zend_Exception
     */
    public function trash($id, $state= 0)
    {
        try {
            $where = $this->db->quoteInto('id = ?', $id);
            $bind = array('state' => $state);
            $this->db->update($this->name, $bind, $where);
            return true;
        }catch (Zend_Db_Adapter_Exception $e){
            throw new Zend_Exception($e->getMessage());
        }
    }
    
    public function lastInserId(){
        return $this->db->lastInsertId($this->name);
    }
    
    public function convertData($data){
        foreach ($data as $key => $value){
            foreach ($data[$key] as $inkey => $invalue){
                if($inkey == 'dados_cliente'){
                    $toArray = json_decode($invalue, true);
                    foreach ($toArray as $toArrayKey => $toArrayValue){
                        $data[$key]->$toArrayKey = $toArrayValue;
                    }
                    unset($data[$key]->$inkey);
                }
            }
        }
    
        return $data;
    }
    
    
    /**
     * Retorna array no formato Json removendo alguns parametros
     * @param array $data
     * @param array $excludes
     *            itens no array a serem removidos do json
     */
    private function convertToJson($data = array(), $excludes = array())
    {
        foreach ($excludes as $exclude) {
            unset($data[$exclude]);
        }
    
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}

