<?php

class Application_Model_Clientes
{

    protected $name = 'zf_clientes';

    protected $db;

    function __construct()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
    }
    
    /** Recupera dados da tabela #__clientes
     * @param Zend_DB::FETCH $mode
     * @return array
     */
    public function selectAll($filterState = 1, $mode = Zend_Db::FETCH_OBJ)
    {   
        try {
            
            $filterState = $this->db->quoteInto('state = ?', $filterState);
            
            $sql = "SELECT * FROM ".$this->name. " WHERE ". $filterState . " ORDER BY id DESC";
            $this->db->setFetchMode($mode);
            $result = $this->db->fetchAll($sql);
            return $result;
        }catch (Zend_Exception $e){
            throw new Zend_Exception($e->getMessage());
            return false;
        }
    }    

    /** Recupera dados da tabela #__clientes pelo campo id
     * @param string $id
     * @param boolean $userId use true para não recuperar pelo ID do usuario     
     */
    public function selectById($id, $userId = false)
    {
        try {
            
            if($userId){
                $sql = "SELECT * FROM ".$this->name." WHERE id = ? AND created_user_id = ?";
                $result = $this->db->fetchRow($sql, array($id, CURRENT_USER_ID), Zend_Db::FETCH_ASSOC);
            }else{
                $sql = "SELECT * FROM ".$this->name." WHERE id = ?";
                $result = $this->db->fetchRow($sql, array($id), Zend_Db::FETCH_ASSOC);
            }
            
            return $result;
        }catch (Zend_Exception $e){
            throw new Zend_Exception($e->getMessage());
            return false;
        }       
        
    }

    /** Recupera dados da tabela #__clientes pelo campo CPF
     * @param string $cpf
     * @param boolean $userId use true para não recuperar pelo ID do usuario
     */
    public function selectBy($cpf, $userId = false)
    {
        try {
            if($userId){
                $sql = "SELECT * FROM ".$this->name." WHERE cpf = ? AND created_user_id = ?";
                $result = $this->db->fetchRow($sql, array($cpf, CURRENT_USER_ID), Zend_Db::FETCH_ASSOC);
            }else{
                $sql = "SELECT * FROM ".$this->name." WHERE cpf = ?";
                $result = $this->db->fetchRow($sql, array($cpf), Zend_Db::FETCH_ASSOC);
            }
            return $result;            
        }catch (Zend_Exception $e){
            throw new Zend_Exception($e->getMessage());
            return false;
        }
    }

    /** Adiciona dados na tabela #__clientes
     * @param array $data     
     */
    public function insert($data = array())
    {
        $bind = array();
        $bind['cpf'] = $data['cpf'];
        $bind['last_user_id'] = $data['last_user_id'];
        $bind['created_user_id'] = $data['created_user_id'];
        $bind['dados_cliente'] = $this->convertToJson($data, array(
            'cpf',
            'last_user',
            'created_user_id',
            'Enviar'
        ));
        
        try {
            $result = $this->db->insert($this->name, $bind);
            return $result;
        } catch (Zend_Db_Adapter_Exception $e) {
            throw new Zend_Exception($e->getMessage());
            return false;
        }
    }

    /** Atualiza dados na tabela #__clientes
     * @param string $id
     * @param array $data
     */
    public function update($id, $data = array())
    {
        $bind = array();
        $bind['cpf'] = $data['cpf'];
        $bind['last_user_id'] = $data['last_user_id'];
        $bind['created_user_id'] = $data['created_user_id'];
        $bind['dados_cliente'] = $this->convertToJson($data, array(
            'id',
            'cpf',
            'last_user',
            'created_user_id',
            'Enviar'
        ));
        
        try {
            $where = $this->db->quoteInto('id=?', $data['id']);
            $result = $this->db->update($this->name, $bind, $where);
            return $result;
        } catch (Zend_Db_Adapter_Exception $e) {
            throw new Zend_Exception($e->getMessage());
            return false;
        }        
        
    }

    /**
     * Method remove Application_Model_Usuarios
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
     * Retorna array no formato Json removendo alguns parametros     * 
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