<?php

abstract class MasterController extends Zend_Controller_Action
{
    protected $db = null;
    protected $data_user = null;
    protected $_custom;
    
    public function preDispatch()
    {
        parent::preDispatch();
    
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
    }
     
    public function init()
    {
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $this->_custom = $config->getOption('custom');
        $this->view->headTitle($this->_custom['company_name'] . ' | ' . $this->getRequest()->getControllerName());
    }
}