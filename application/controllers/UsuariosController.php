<?php

class UsuariosController extends Zend_Controller_Action
{
    
    protected $_modelUsers = null;
    protected $_custom = null;

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
        $this->_modelUsers = new Application_Model_Usuarios;
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $this->_custom = $config->getOption('custom');        
        $this->view->headTitle($this->getRequest()->getControllerName() . ' | ' . $this->_custom['company_name'] );
    }

    public function indexAction()
    {
        $this->view->data = $this->_modelUsers->select();
    }

    public function addAction()
    {
        $form = new Application_Form_AddUsuario;
        $request = $this->_request;
        if($request->isPost())
        {
            $data = $request->getPost();
            
            if($form->isValid($data))
            {
                if(!$this->_modelUsers->select($data['email']))
                {
                    $this->_modelUsers->insert($data);
                    $form->populate(array());
                    $this->view->messages = $this->view->partial('common/message.phtml', array('message_type' => 'alert-success', 'messages' => array('Cadastrado realizado com sucesso.')));
                }else{
                    $this->view->messages = $this->view->partial('common/message.phtml', array('messages' => array('O email <b>'. $data['email'] .'</b> já está cadastrado')));                    
                }
                
            }else{                                    
                $form->populate($data);
            }
        }
        
        $this->view->cadastroForm =  $form;        
    }

    public function editAction()
    {

        $form = new Application_Form_EditUsuario;        
        $request = $this->_request;        
        
        if($request->isPost())
        {
            $data = $request->getPost();
        
            if($form->isValid($data))
            {   
                $checkData = $this->_modelUsers->select($data['email']);
                
                if($checkData->id != $data['id'] && $checkData->email == $data['email'] )
                {
                    $this->view->messages = $this->view->partial('common/message.phtml', array('messages' => array('O email <b>'. $data['email'] .'</b> já está cadastrado')));
                }else{
                    $this->_modelUsers->update($data);
                    $form->populate(array());
                    $this->view->messages = $this->view->partial('common/message.phtml', array('message_type' => 'alert-success', 'messages' => array('Atualizado realizado com sucesso.')));
                }
        
            }else{
                $form->populate($data);
            }
        }else{
            $data = $this->_modelUsers->selectById($request->getParam('id'));
            
            if($data){
                $form->populate($data);
            }else{
                $this->_redirect('/usuarios/index');
            }
        }
        
        $this->view->editForm =  $form;
    }


}

