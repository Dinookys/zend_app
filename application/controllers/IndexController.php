<?php

class IndexController extends Zend_Controller_Action
{

    protected $db = null;

    protected $data_user = null;

    protected $_custom = null;

    protected $_layout = null;

    protected $_actionName = null;

    protected $_FlashMessenger = null;

    public function preDispatch()
    {
    
        $auth = Zend_Auth::getInstance();
        $this->data_user = $auth->getIdentity();
    
        if(!$auth->hasIdentity()){
            $this->redirect('/login');
        }else{            
            $acl = new Application_Model_Acl_Acl();
            if(!$acl->isAllowed()){
                $this->redirect('/error/forbidden');  
            }
        }        
        $this->view->user = $this->data_user;
        parent::preDispatch();
    }

    public function init()
    {
        
        $this->_modelUsers = new Application_Model_Usuarios();
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $this->_FlashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->_custom = $config->getOption('custom');
        $this->view->headTitle(strtoupper($this->getRequest()
            ->getControllerName()) . ' | ' . $this->_custom['company_name']);
        
        $this->view->controllerName = $this->getRequest()->getControllerName();
        $this->view->actionName = $this->_actionName = $this->getRequest()->getActionName();
        $this->view->messages = $this->_FlashMessenger->getMessages($this->_actionName);
        $this->view->user = $this->data_user; 
    }

    public function indexAction()
    {   
        switch (strtolower($this->data_user->role)){
            case 'corretor':
                $this->redirect('index/ficha-atendimento');
                break;
        };
    }

    public function fichaAtendimentoAction()
    {
        $form = new Application_Form_FichaAtendimento();
        
        $this->view->formFicha = $form;
    }


}





