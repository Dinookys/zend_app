<?php

class UsuariosController extends Zend_Controller_Action
{

    protected $_modelUsers = null;

    protected $_custom = null;

    protected $data_user;
    
    protected $_actionName;
    
    protected $_FlashMessenger;

    public function preDispatch()
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
    }

    public function indexAction()
    {
        $request = $this->_request;
        if ($request->isPost()) {
            $data = $request->getPost();
        }        
        $this->view->barTitle = 'Usuários';
        $this->view->data = $this->_modelUsers->select();        
    }

    public function addAction()
    {
        $form = new Application_Form_AddUsuario();
        $request = $this->_request;
        if ($request->isPost()) {
            $data = $request->getPost();
            
            if ($form->isValid($data)) {
                if (! $this->_modelUsers->select($data['email'])) {
                    $this->_modelUsers->insert($data);
                    $form->populate(array());
                    
                    $this->_FlashMessenger->setNamespace('index')->addMessage('Cadastrado realizado com sucesso.');
                    $this->view->message_type = 'alert-success';
                    $this->redirect('usuarios/index');
                } else {
                    $this->_FlashMessenger->setNamespace($this->_actionName)->addMessage('O email <b>' . $data['email'] . '</b> já está cadastrado');
                }
            } else {
                $form->populate($data);
            }
        }
        
        $this->view->barTitle = 'Adicionando usuário';
        $this->view->cadastroForm = $form;
    }

    public function editAction()
    {
        $form = new Application_Form_EditUsuario();
        $request = $this->_request;
        
        if ($request->isPost()) {
            $data = $request->getPost();
            
            if ($form->isValid($data)) {
                $checkData = $this->_modelUsers->select($data['email']);
                
                if ($checkData->id != $data['id'] && $checkData->email == $data['email']) {
                    $this->_FlashMessenger->setNamespace($this->_actionName)->addMessage('O email <b>' . $data['email'] . '</b> já está cadastrado');
                } else {
                    $this->_modelUsers->update($data);
                    $form->populate(array());
                    $this->view->message_type = 'alert-success';
                    $this->_FlashMessenger->setNamespace($this->_actionName)->addMessage('Atualizado com sucesso!');
                }
            } else {
                $form->populate($data);
            }
        } else {
            $data = $this->_modelUsers->selectById($request->getParam('id'));
            
            if ($data) {
                $form->populate($data);
            } else {
                $this->redirect('/usuarios/index');
            }
        }
        $this->view->barTitle = 'Editando usuário';
        $this->view->editForm = $form;
    }

    public function deleteAction()
    {
        $request = $this->_request;
        
        if ($request->isPost()) {
            $data = array_keys($request->getPost());
            $totalData = count($data);
            
            $textoRemovido = 'item removido';
            if ($totalData > 1) {
                $textoRemovido = 'itens removidos';
            }
            
            foreach ($data as $id) {
                if ($id != $this->data_user->id) {
                    $this->_modelUsers->delete($id);
                } else {
                    $totalData = $totalData - 1;
                    $message = sprintf('O id <b> %1s </b> não pode ser excluido', $id);
                    $this->_FlashMessenger->setNamespace('index')->addMessage($message);
                }
            }
            
            $this->_FlashMessenger->setNamespace('index')->addMessage(sprintf('%s %s com sucesso!', $totalData, $textoRemovido));
        }
        
        $this->redirect('/usuarios/index');
    }
}