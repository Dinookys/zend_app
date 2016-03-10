<?php

class UsuariosController extends Zend_Controller_Action
{

    protected $_modelUsers = null;

    protected $_custom = null;

    protected $data_user = null;

    protected $_actionName = null;

    protected $_FlashMessenger = null;

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
        
        $this->view->controllerName = $this->getRequest()->getControllerName();
        $this->view->actionName = $this->_actionName = $this->getRequest()->getActionName();
        $this->view->messages = $this->_FlashMessenger->getMessages($this->_actionName);
        $this->view->user = $this->data_user;
        
    }

    public function indexAction()
    {
        $request = $this->_request;
        if ($request->isPost()) {
            $data = $request->getPost();
        }
        
        $filter = $request->getParam('filter');
        
        if($filter == '0'){
            $select = $this->_modelUsers->selectAll('0');
        }else{
            $select = $this->_modelUsers->selectAll('1');
        }
        
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage($this->_custom['itemCountPerPage'])
                  ->setCurrentPageNumber($this->_getParam('page',1));
        
        $this->view->paginator = $paginator;
        $this->view->barTitle = 'Usuários';       
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
                    $this->_FlashMessenger->setNamespace('index')->addMessage('Cadastrado realizado com sucesso.');
                    $this->view->message_type = 'alert-success';
                    $this->redirect('usuarios/edit/id/'.$this->_modelUsers->lastInserId());
                } else {
                    $this->view->messages = array('O email <b>' . $data['email'] . '</b> já está cadastrado');                    
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

            if(empty($data['password'])){
                unset($data['password']);
            }
            
            if ($form->isValid($data)) {
                $checkData = $this->_modelUsers->select($data['email']);
                
                if ($checkData->id != $data['id'] && $checkData->email == $data['email']) {
                        $this->view->messages = array('O email <b>' . $data['email'] . '</b> já está cadastrado');
                }else {
                    $this->_modelUsers->update($data);                    
                    $this->view->message_type = 'alert-success';
                    $this->view->messages = array('Atualizado com sucesso!');                    
                }
            } else {
                $form->populate($data);
            }
        } else {
            $data = $this->_modelUsers->selectById($request->getParam('id'));
            // se for vazio redireciona para a index
            if ($data) {                
                $form->populate($data);
            } else {
                $this->redirect('/usuarios/index');
            }
        }
        
        if(in_array($data['role'], array('Corretor','Supervisor'))){
            $roleName = 'Coordenador';
        }else if(in_array($data['role'], array('Coordenador'))){
            $roleName = 'Gerente';
        }else{
            $roleName = '';
        }
        
        $options = $form->setSuperior($roleName);        
        $form->getElement('parent_id')->addMultiOptions($options);        
        
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
        
        $this->redirect('/usuarios/index/filter/0');
    }

    public function userAction()
    {
       $form = new Application_Form_PerfilUsuario();       
       $request = $this->_request;
       
       if ($request->isPost()) {
            $data = $request->getPost();            
            if ($form->isValid($data)) {
                $checkData = $this->_modelUsers->select($data['email']);
                
                if ($checkData->id != $data['id'] && $checkData->email == $data['email']) {
                    $this->_FlashMessenger->setNamespace($this->_actionName)->addMessage('O email <b>' . $data['email'] . '</b> já está cadastrado');
                } else {
                    $this->_modelUsers->update($data);                    
                    $this->view->message_type = 'alert-success';
                    $this->_FlashMessenger->setNamespace($this->_actionName)->addMessage('Atualizado com sucesso!');
                }
            } else {
                $form->populate($data);
            }
        } else {
            $data = $this->_modelUsers->selectById($this->data_user->id);
            
            if ($data) {
                $form->populate($data);
            } else {
                $this->redirect('/index');
            }
        }
       
       $this->view->barTitle = 'Editando usuário';
       $this->view->formUser = $form; 
    }

    public function trashAction()
    {
        $request = $this->_request;
        $model = new Application_Model_Clientes();
        
        if ($request->isPost()) {
            $data = array_keys($request->getPost());
            $totalData = count($data);
            
            $textoRemovido = 'item movido para lixeira';
            if ($totalData > 1) {
                $textoRemovido = 'itens movidos para lixeira';
            }
            
            foreach ($data as $id) {
                $model->trash($id, 0);
            }
            
            $this->_FlashMessenger->setNamespace('index')->addMessage(sprintf('%s %s com sucesso!', $totalData, $textoRemovido));
        }
        
        $this->redirect('/usuarios/index');
    }

    public function unlockAction()
    {              
        $this->redirect('/usuarios/index');
    }
}