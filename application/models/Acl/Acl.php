<?php
class Application_Model_Acl_Acl extends Zend_Acl{
    
    protected $_role;
    protected $_current_resource;
    
    function __construct()
    {        
        $auth = Zend_Auth::getInstance();        
        $request = Zend_Controller_Front::getInstance()->getRequest();
                
        $this->_current_resource = $request->getControllerName() . ':' . $request->getActionName();     
        
        $user = $auth->getIdentity();
        
        $this->_role = $user->role;
        $this->addRole($this->_role);
        
        if(is_array($user->resources)){
            $resources = explode(',', $user->resources);
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
            
        }else{
            $this->addResource($this->_current_resource);
            $this->allow($this->_role, $this->_current_resource);
        }        

    }
    
    public function isAllowed() {
        return parent::isAllowed($this->_role, $this->_current_resource);
    }
}