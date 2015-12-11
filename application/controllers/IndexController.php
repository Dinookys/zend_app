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
        }
    }

    public function init()
    {                
       
    }

    public function indexAction()
    {          
        $acl = new Application_Model_Acl_Acl();
    }

}

