<?php

class DocumentosController extends Zend_Controller_Action
{

    public function init()
    {
        $auth = Zend_Auth::getInstance();
        $this->data_user = $auth->getIdentity();
        
        if (! $auth->hasIdentity()) {
            $this->redirect('/login');
        } else {
            $acl = new Application_Model_Acl_Acl();
            if (! $acl->isAllowed()) {
                $this->redirect('/error/forbidden');
            }
        }
        
        $this->view->user = $this->data_user;
        $this->view->model_user = new Application_Model_Usuarios();
        
        $this->_modelUsers = new Application_Model_Usuarios();
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        
        $this->_custom = $config->getOption('custom');
        
        // Acessando permissões
        $this->_acl = $config->getOption('acl');
        // Pegando array de configurações para a criação do menu
        $this->view->menu = $config->getOption('menu');
        
        $this->_FlashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->headTitle(strtoupper($this->getRequest()
            ->getControllerName()) . ' | ' . $this->_custom['company_name']);
        
        $this->view->controllerName = $this->_controllerName = $this->getRequest()->getControllerName();
        $this->view->actionName = $this->_actionName = $this->getRequest()->getActionName();
        $this->view->user = $this->data_user;
        $this->_FlashMessenger->clearMessages($this->_controllerName);
        
        if ($this->data_user->childrens_ids) {
            $this->_ids = $this->data_user->childrens_ids;
            $this->_ids[] = CURRENT_USER_ID;
        } else {
            $this->_ids = array(
                CURRENT_USER_ID
            );
        }
        $this->view->date = new Zend_Date();
    }

    public function indexAction()
    {
        // action body
    }

    public function uploadAjaxAction()
    {
        $request = $this->_request;

        if($request->isPost()){
			
        	$data = $request->getPost();
        	
        	$files = new Zend_File_Transfer_Adapter_Http();
        	print_r($data);
        	print_r($files->getFileInfo());        	

        }
    }


}