<?php

class IndexController extends Zend_Controller_Action
{

    protected $db = null;

    protected $data_user = null;

    protected $_custom = null;

    protected $_layout = null;

    protected $_actionName = null;

    protected $_controllerName = null;

    protected $_FlashMessenger = null;

    public function init()
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
        $this->view->messages = $this->_FlashMessenger->getMessages($this->_controllerName);
        $this->view->user = $this->data_user; 
    }

    public function indexAction()
    {
        switch (strtolower(CURRENT_USER_ROLE)){
            case 'gerente':
            case 'coordenador':
            case 'corretor':
                $this->redirect('clientes');
                break;
            case 'financeiro':
                $this->redirect('financeiro');
                break;
            case 'financeiro':
                $this->redirect('financeiro');
                break;
            case 'administrador':
                $this->redirect('usuarios');
                break;
            case 'financeiro':
                $this->redirect('financeiro');
                break;
        };
    }
}

