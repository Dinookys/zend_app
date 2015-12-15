<?php

class Application_Model_Usuarios
{

    protected $db;

    protected $name = 'zf_usuarios';

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

    public function insert($data)
    {
        $bind = $this->clearData($data);
        $bind['password'] = sha1($bind['password']);        
        
        try {
            $this->db->insert($this->name, $bind);
        } catch (Zend_Db_Adapter_Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($data)
    {
        $bind = $this->clearData($data);
        unset($bind['id']);        
        
        try {            
            $this->db->update($this->name, $bind, 'id =' .$data['id'] );                        
        } catch (Zend_Db_Adapter_Exception $e) {
            return $e->getMessage();
        }
    }

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
            return $e->getMessage();
        }
    }

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
            return $e->getMessage();
        }
    }

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
}