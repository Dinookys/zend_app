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
    public function selectAll($filterState = 1, $like = NULL)
    {
        try {
            $select = new Zend_Db_Select($this->db);
            $select->from('' . $this->name . '');
            $select->where('state=?', $filterState);
            
            if(!is_null($like)){
                $columns = array('cpf', 'dados_cliente');
                $select->where($columns[0] . ' LIKE ?', '%'. $like .'%' );
                $select->orWhere($columns[1] . ' LIKE ?', '%'. $like .'%' );                
            }
            
            $select->order('id DESC');        
          return $select;
            
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
    public function selectByUsersIds($filterState = 1, $ids, $like = NULL)
    {
        try {
            
            $select = new Zend_Db_Select($this->db);
            $select->from('' . $this->name . '');
            $select->where('state=?', $filterState);
            $select->where('created_user_id IN ( '. $ids .' )');
            
            if(!is_null($like)){
                $columns = array('cpf', 'dados_cliente');
                $select->where($columns[0] . ' LIKE ?', '%'. $like .'%' );
                $select->orWhere($columns[1] . ' LIKE ?', '%'. $like .'%' );
            }
            
            $select->order('id DESC');            
            
            return $select;

        } catch (Zend_Exception $e) {
            throw new Zend_Exception($e->getMessage());
            return false;
        }
    }

    /**
     * 
     * @param string|int $id
     * @throws Zend_Exception
     * @return mixed|bool
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
            'last_user_id',
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
        $bind['dados_cliente'] = $this->convertToJson($data, array(
            'cpf',
            'last_user_id',
            'created_user_id',
            'locked',
            'locked_by',
            'Enviar',
            'id'
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
     * Methodo para converter um determinado valor em json para um array dentro de um array multidimensional
     * @param array com valor stdClass $data
     * @param string $findKey chave do valor a ser convertido para array
     * @return stdClass | boolean
     */
    public function convertData($data, $findKey = 'dados_cliente')
    {           
        if(!empty($data)){
            foreach ($data as $key => $value) {
                foreach ($data[$key] as $inkey => $invalue) {
                    if ($inkey == $findKey) {
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
        
        return false;        
    }

    /**
     * Retorna array no formato Json removendo alguns parametros *
     *
     * @param array $data            
     * @param array $excludes
     * itens no array a serem removidos do json
     */
    protected function convertToJson($data = array(), $excludes = array())
    {
        foreach ($excludes as $exclude) {
            unset($data[$exclude]);
        }
        
        return json_encode($data);
    }
    
    protected function clearData($data, $table)
    {
        if($table){
            $table = $table;
        }else{
            $table = $this->name;
        }
        
        $result = $this->db->describeTable($table);
        $cols = array_keys($result);
        $cleardata = array();
        foreach ($data as $key => $value) {
            if (in_array($key, $cols)) {
                $cleardata[$key] = $value;
            }
        }
    
        return $cleardata;
    }
}