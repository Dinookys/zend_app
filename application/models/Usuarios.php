<?php

class Application_Model_Usuarios
{

    protected $db;

    protected $name = 'zf_usuarios';

    function __construct()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
    }

    /**
     * Method getPerfis Application_Model_Usuarios
     * 
     * @return array
     */
    public function getPerfis()
    {
        $sql = "SELECT id,role FROM zf_perfis WHERE 1";
        $this->db->setFetchMode(Zend_Db::FETCH_OBJ);
        $result = $this->db->fetchAll($sql);
        return $result;
    }

    /**
     * Method insert Application_Model_Usuarios
     * 
     * @param
     *            array
     * @return boolean
     */
    public function insert($data = array())
    {
        $bind = $this->clearData($data);
        $bind['password'] = sha1($bind['password']);
        
        try {
            if ($this->db->insert($this->name, $bind)) {
                return true;
            }
        } catch (Zend_Db_Adapter_Exception $e) {
            throw new Zend_Exception($e->getMessage());
        }
    }

    /**
     * Method update Application_Model_Usuarios
     * 
     * @param
     *            array
     * @return boolean
     */
    public function update($data = array())
    {
        $bind = $this->clearData($data);
        if (isset($bind['password'])) {
            $bind['password'] = sha1($bind['password']);
        }
        ;
        
        unset($bind['id']);
        
        try {
            $where = $this->db->quoteInto('id = ?', $data['id']);
            if ($this->db->update($this->name, $bind, $where)) {
                return true;
            }
        } catch (Zend_Db_Adapter_Exception $e) {
            throw new Zend_Exception($e->getMessage());
        }
    }

    /**
     * Method select Application_Model_Usuarios
     * 
     * @param
     *            string
     * @return array
     * @tutorial param $email = null return all rows
     */
    public function select($email = null)
    {
        try {
            if ($email) {
                $result = $this->db->fetchRow('SELECT u.id, u.nome, u.email, u.id_perfil, u.acesso, p.role FROM ' . $this->name . ' AS u LEFT JOIN zf_perfis AS p ON u.id_perfil = p.id WHERE email = ?', array(
                    $email
                ), Zend_Db::FETCH_OBJ);
            } else {
                $this->db->setFetchMode(Zend_Db::FETCH_OBJ);
                $result = $this->db->fetchAll('SELECT u.id, u.nome, u.email, u.id_perfil, u.acesso, p.role FROM ' . $this->name . ' AS u LEFT JOIN zf_perfis AS p ON u.id_perfil = p.id WHERE 1 ORDER BY id ASC');
            }
            
            return $result;
        } catch (Zend_Db_Adapter_Exception $e) {
            throw new Zend_Exception($e->getMessage());
        }
    }

    /**
     * Recupera dados da tabela #__clientes
     * 
     * @param Zend_DB::FETCH $mode            
     * @return array
     */
    public function selectAll($filterState = 1)
    {
        try {
            $filterState = $this->db->quoteInto('state = ?', $filterState);
            $result = $this->db->fetchAll('SELECT u.id, u.nome, u.email, u.id_perfil, u.acesso, u.state, p.role FROM ' . $this->name . ' AS u LEFT JOIN zf_perfis AS p ON u.id_perfil = p.id WHERE ' . $filterState . ' ORDER BY id DESC', NULL, Zend_Db::FETCH_OBJ);
            
            return $result;
        } catch (Zend_Db_Adapter_Exception $e) {
            throw new Zend_Exception($e->getMessage());
        }
    }

    /**
     * Method selectById Application_Model_Usuarios
     * 
     * @param
     *            string
     * @return array
     */
    public function selectById($id = null)
    {
        try {
            if ($id) {
                $result = $this->db->fetchRow('SELECT id, nome, email, id_perfil, acesso FROM ' . $this->name . ' WHERE id = ?', array(
                    $id
                ), Zend_Db::FETCH_ASSOC);
                return $result;
            }
            
            return false;
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

    /**
     * Method clearData Application_Model_Usuarios
     * 
     * @param
     *            array
     * @return array
     */
    private function clearData($data)
    {
        $result = $this->db->describeTable($this->name);
        $cols = array_keys($result);
        $cleardata = array();
        foreach ($data as $key => $value) {
            if (in_array($key, $cols)) {
                $cleardata[$key] = $value;
            }
        }
        
        return $cleardata;
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
}