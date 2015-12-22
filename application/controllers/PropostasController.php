<?php

class PropostasController extends Zend_Controller_Action
{

    protected $data_user = null;

    protected $_custom = null;

    protected $_actionName = null;

    protected $_controllerName = null;

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
        
        $this->_custom = $config->getOption('custom');
        // Pegando array de configurações para a criação do menu
        $this->view->menu = $config->getOption('menu');
        
        $this->_FlashMessenger = $this->_helper->getHelper('FlashMessenger');        
        $this->view->headTitle(strtoupper($this->getRequest()
            ->getControllerName()) . ' | ' . $this->_custom['company_name']);
        
        $this->view->controllerName = $this->_controllerName = $this->getRequest()->getControllerName();
        $this->view->actionName = $this->_actionName = $this->getRequest()->getActionName();        
        $this->view->user = $this->data_user;
        $this->_FlashMessenger->clearMessages($this->_controllerName);
        
        $this->view->date = new Zend_Date();
    }

    public function indexAction()
    {
        $model = new Application_Model_Propostas();        
        $this->view->data = $model->selectAll();
    }

    public function addAction()
    {
        // action body
    }

    public function editAction()
    {
        // action body
    }

    public function trashAction()
    {
        // action body
    }

    public function restoreAction()
    {
        // action body
    }

    public function deleteAction()
    {
        // action body
    }


}









