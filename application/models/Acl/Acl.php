<?php
class Application_Model_Acl_Acl extends Zend_Acl{
    
    protected $_role;
    protected $_current_resource;
    
    function __construct()
    {        
        $auth = Zend_Auth::getInstance();        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');        
        $user = $auth->getIdentity();
        $this->_current_resource = $request->getControllerName() . ':' . $request->getActionName();        
 
        $aclResources = $config->getOption('acl');
        $resources = $aclResources[$user->role];
        $this->_role = $user->role;
        $this->addRole($this->_role);
        
        if(!in_array($this->_current_resource, $this->getResources())){
            $this->addResource($this->_current_resource);
        }
        
        if(isset($resources) && $resources != '*'){
            
            $resources = explode(',', $resources);
            foreach ($resources as $value){
                if(!in_array($value, $this->getResources())){
                    $this->addResource($value);
                }
            }
            
            if(in_array($this->_current_resource, $resources) OR in_array($request->getControllerName(), $resources) ){
                $this->allow($this->_role, $this->_current_resource);
            }else{
                $this->deny($this->_role, $this->_current_resource);
            }
            
        }elseif($resources == '*'){                       
            $this->allow($this->_role, $this->_current_resource);
        }else{                       
            $this->deny($this->_role, $this->_current_resource);
        }

    }
    
    public function isAllowed() {
        return parent::isAllowed($this->_role, $this->_current_resource);
    }
}