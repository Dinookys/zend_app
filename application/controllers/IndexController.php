<?php

class IndexController extends Zend_Controller_Action
{
    private $db;
    private $data_user;
    
    public function preDispatch(){
        parent::preDispatch();
        
        $auth = Zend_Auth::getInstance();
        $this->data_user = $auth->getIdentity();
        
        if(!$auth->hasIdentity()){
            $this->_helper->FlashMessenger->addMessage('Login não efetuado ou sessão expirada');
            $this->redirect('/login');
        }else{
            $acl = new Application_Model_Acl_Acl();
            
            if($acl->isAllowed()){
                echo "sim";
            }else{
                echo "não";
            }
        }
    }

    public function init()
    {       
       
    }

    public function indexAction()
    {          
        
    }
    
    public function listarAction() {
        
    }

}

