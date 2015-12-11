<?php
class Application_Model_Acl_Acl extends Zend_Acl{
    
    protected $_role;
    protected $_current_resource;
    
    function __construct()
    {        
        $auth = Zend_Auth::getInstance();
        $db = Zend_Db_Table::getDefaultAdapter();
        $request = Zend_Controller_Front::getInstance()->getRequest();
                
        $this->_current_resource = $request->getControllerName() . ':' . $request->getActionName();     
        
        $user = $auth->getIdentity();
        
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $result = $db->fetchRow('SELECT role, resources FROM zf_perfis WHERE id = ?', $user->id_perfil);
        $this->_role = $result->role;
        
        $resources = explode(',', $result->resources);
        $this->addRole($this->_role);        
        
        foreach ($resources as $value){
            $this->addResource($value);
        }
        
        $this->allow($this->_role, $resources);
        
        if(!in_array($this->_current_resource, $this->getResources())){
            $this->addResource($this->_current_resource);
        }
        
        if(!in_array($this->_current_resource, $resources)){
            $this->deny($this->_role, $this->_current_resource);
        }
    }
    
    public function isAllowed() {
        return parent::isAllowed($this->_role, $this->_current_resource);
    }
}