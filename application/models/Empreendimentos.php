<?php

class Application_Model_Empreendimentos
{

    private $name = 'zf_empreendimentos';

    private $db;

    public function __construct()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
    }

    /**
     * Retorna uma query com informações do empreendimentos cadastrados
     * 
     * @param number $filter            
     * @throws Zend_Db_Exception
     * @return mixed|boolean
     */
    public function selectQueryList($filter = 1)
    {
        try {
            
            $select = new Zend_Db_Select($this->db);
            $select->from($this->name);
            $select->where('state=?', $filter);
            $select->order('id DESC');            
            return $select;
            
        } catch (Zend_Db_Exception $e) {
            throw new Zend_Db_Exception($e->getMessage());
            return false;
        }
    }
    
    /**
     * Retorna um array com informações do empreendimentos cadastrados
     *
     * @param number $filter
     * @throws Zend_Db_Exception
     * @return mixed|boolean
     */
    public function selectAll($filter = 1)
    {
        try {
            $sql = 'SELECT * FROM ' . $this->name . ' WHERE state=?';
            return $this->db->fetchAll($sql, array(
                $filter
            ), Zend_Db::FETCH_OBJ);
    
        } catch (Zend_Db_Exception $e) {
            throw new Zend_Db_Exception($e->getMessage());
            return false;
        }
    }
    
    /**
     * 
     * @param unknown $id
     * @param array $cols
     * @throws Zend_Db_Exception
     * @return mixed|bool
     */
    public function selectById($id, $cols = array()){
        try{

            if(!empty($cols)){
                $sql = 'SELECT '. implode(',', $cols) .' FROM '. $this->name .' WHERE id=?';                
            }else{
                $sql = 'SELECT * FROM '. $this->name .' WHERE id=?';    
            }

            return $this->db->fetchRow($sql,array($id), Zend_db::FETCH_ASSOC);
        }catch (Zend_Db_Exception $e){
            throw new Zend_Db_Exception($e->getMessage());
            return false;
        }
    }

    /**
     * Retonar true ou false
     * 
     * @param array $data            
     * @throws Zend_Db_Exception
     * @return number|boolean
     */
    public function insert($data)
    {
        try {
            if ($this->db->insert($this->name, $data)) {
                return true;
            } else {
                return false;
            }
        } catch (Zend_Db_Expception $e) {
            throw new Zend_Db_Exception($e->getMessage());
            return false;
        }
    }
    
    public function update($data) {
        try {
            $where = $this->db->quoteInto('id=?', $data['id']);
            unset($data['id']);
            if ($this->db->update($this->name, $data, $where)) {
                return true;
            } else {
                return false;
            }
        } catch (Zend_Db_Expception $e) {
            throw new Zend_Db_Exception($e->getMessage());
            return false;
        };
    }
    
    /**
     * Exclui o item
     * @param number|string $id
     * @throws Zend_Exception
     * @return boolean
     */
    public function delete($id)
    {
        try {
            $where = $this->db->quoteInto('id = ?', $id);
            $this->db->delete($this->name, $where);
            return true;
        } catch (Zend_Db_Adapter_Exception $e) {
            throw new Zend_Exception($e->getMessage());
            return false;
        }
    }
    
    /**
     * Trash atualiza estado do item     
     * @param int $id
     * @param int $state
     * @throws Zend_Exception
     */
    public function trash($id, $state = 0)
    {
        try {
            $where = $this->db->quoteInto('id = ?', $id);
            $bind = array(
                'state' => $state
            );
            $this->db->update($this->name, $bind, $where);
            return true;
        } catch (Zend_Db_Adapter_Exception $e) {
            throw new Zend_Exception($e->getMessage());
        }
    }
    
    /**
     * Bloqueia outras pessoas quando estiver editando
     * @param unknown $id
     * @param unknown $current_user_id
     * @param unknown $value
     * @throws Zend_Db_Exception
     */
    public function lockRow($id, $current_user_id, $value){
        try {
            $where = $this->db->quoteInto('id=?', $id);
            $result = $this->db->update($this->name, array('locked' => $value, 'locked_by' => $current_user_id), $where);
            return $result;
        }catch (Zend_Db_Exception $e){
            throw new Zend_Db_Exception($e->getMessage());
            return false;
        }
    }
}

