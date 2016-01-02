<?php

class Application_Model_Clientes
{

    private $name = 'zf_clientes';

    protected $db;

    function __construct()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
    }

    /**
     * @param number $filterState
     * @throws Zend_Exception
     */
    public function selectAll($filterState = 1)
    {
        try {
            
            $result = $this->db->fetchAll("SELECT * FROM " . $this->name . " WHERE state = ? ORDER BY id DESC", array(
                $filterState
            ), Zend_Db::FETCH_OBJ);
            
            return $result;
        } catch (Zend_Exception $e) {
            throw new Zend_Exception($e->getMessage());
            return false;
        }
    }

    /**
     * @param number $filterState            
     * @param string $userIds            
     * @throws Zend_Exception
     */
    public function selectByUsersIds($filterState = 1, $ids)
    {
        try {
            
            $result = $this->db->fetchAll("SELECT * FROM " . $this->name . " WHERE state = ? AND created_user_id IN (" . $ids . ") ORDER BY id DESC", array(
                $filterState
            ), Zend_Db::FETCH_OBJ);
            
            return $result;
        } catch (Zend_Exception $e) {
            throw new Zend_Exception($e->getMessage());
            return false;
        }
    }

    /**
     * selectById
     *
     * @param string $cpf            
     * @param bool $userId            
     * @param bool $parentId            
     * @throws Zend_Exception
     * @return mixed|boolean
     */
    public function selectById($id)
    {
        try {
            
            $sql = "SELECT * FROM " . $this->name . " WHERE id = ?";
            $result = $this->db->fetchRow($sql, array(
                $id
            ), Zend_Db::FETCH_ASSOC);
            
            return $result;
        } catch (Zend_Exception $e) {
            throw new Zend_Exception($e->getMessage());
            return false;
        }
    }

    /**
     * selectBy
     *
     * @param string $cpf            
     * @param bool $userId            
     * @param bool $parentId            
     * @throws Zend_Exception
     * @return mixed|boolean
     */
    public function selectBy($cpf)
    {
        try {
            
            $sql = "SELECT * FROM " . $this->name . " WHERE cpf = ?";
            $result = $this->db->fetchRow($sql, array(
                $cpf
            ), Zend_Db::FETCH_ASSOC);
            
            return $result;
        } catch (Zend_Exception $e) {
            throw new Zend_Exception($e->getMessage());
            return false;
        }
    }

    /**
     * Adiciona dados na tabela #__clientes
     *
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
            'locked',
            'locked_by',
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

    /**
     * Atualiza dados na tabela #__clientes
     *
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
            'cpf',
            'last_user',
            'created_user_id',
            'locked',
            'locked_by',
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
     *
     * @param
     *            int
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
        }
    }

    /**
     * trash atualiza estado do item
     *
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

    public function lastInserId()
    {
        return $this->db->lastInsertId($this->name);
    }

    public function convertData($data)
    {
        foreach ($data as $key => $value) {
            foreach ($data[$key] as $inkey => $invalue) {
                if ($inkey == 'dados_cliente') {
                    $toArray = json_decode($invalue, true);
                    foreach ($toArray as $toArrayKey => $toArrayValue) {
                        $data[$key]->$toArrayKey = $toArrayValue;
                    }
                    unset($data[$key]->$inkey);
                }
            }
        }
        
        return $data;
    }
    
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

    /**
     * Retorna array no formato Json removendo alguns parametros *
     *
     * @param array $data            
     * @param array $excludes
     * itens no array a serem removidos do json
     */
    private function convertToJson($data = array(), $excludes = array())
    {
        foreach ($excludes as $exclude) {
            unset($data[$exclude]);
        }
        
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}