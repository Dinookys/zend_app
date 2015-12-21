<?php

class ClientesController extends Zend_Controller_Action
{

    protected $db = null;

    protected $data_user = null;

    protected $_custom = null;

    protected $_layout = null;

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
        $model = new Application_Model_Clientes();        
        $request = $this->_request;
        $filter = $request->getParam('filter');        
        
        if($filter == '0'){
            $data = $model->selectAll('0');
        }else{
            $data = $model->selectAll('1');
        }
        
        $data = $model->convertData($data);
        $this->view->messages = $this->_FlashMessenger->getMessages($this->_controllerName);
        $this->view->data = $data;
        $this->view->barTitle = "Clientes";
        
    }

    public function addAction()
    {
        $form = new Application_Form_AddCadastroCliente();
        $model = new Application_Model_Clientes();
        $request = $this->_request;
    
        if($request->isPost() && $form->isValid($request->getPost())){
            $data = $request->getPost();
    
            $check = $model->selectBy($data['cpf'], false);
    
            if($check){
                $this->view->messages = array('CPF '. $data['cpf'] .' já cadastrado!');
                $this->view->message_type = 'alert-warning';
                $form->populate($data);
            }else{
                if($model->insert($data)){
                    $this->view->messages = array('Cadastro realizado com sucesso');                    
                    $this->view->message_type = 'alert-success';
                    $this->redirect($this->_controllerName . 'edit/id/' . $model->lastInserId());
                }else{
                    $form->populate($data);
                }
            }
    
        }
    
        $this->view->barTitle = "Novo Cliente";
        $this->view->form = $form;
    }

    public function editAction()
    {
        $form = new Application_Form_AddCadastroCliente();
        $model = new Application_Model_Clientes();
        $request = $this->_request;
    
        if($request->isPost() && $form->isValid($request->getPost())){
    
            $data = $request->getPost();
            $id = $data['id'];
            $cpf = $data['cpf'];    
            $data['last_user_id'] = CURRENT_USER_ID;
            
            $check = $model->selectBy($cpf, false);
            
            if($check && $check['id'] != $id){
                $this->view->messages = array('CPF já cadastro.');
                $this->view->message_type = "alert-danger";
                $form->populate($data);                
            }else if($model->update($data['id'], $data) == 0){
                $this->view->messages = array('Não foi feito nenhuma alteração.');                
                $this->view->message_type = "alert-info";
            }else{
                $this->view->messages = array('Atualizado com sucesso!');           
                $this->view->message_type = "alert-success";
            }
    
            $form->populate($data);
    
        }else{
            $id = $this->getParam('id');
            $result = $model->selectById($id);
    
            if($result){
                $data = json_decode($result['dados_cliente'], true);
                $data['cpf'] = $result['cpf'];
                $data['last_user_id'] = $result['last_user_id'];
                $data['created_user_id'] = $result['created_user_id'];
                $form->populate($data);
            }else{
                $this->view->messages = array('Cadastro não encontrado');                
                $this->view->message_type = "alert-danger";
            }
        }
    
        $form->addFieldId($id);
        $this->view->barTitle = "Editando Cliente";
        $this->view->form = $form;
    }

    public function deleteAction()
    {
        $request = $this->_request;
        $model = new Application_Model_Clientes();
        
        if ($request->isPost()) {
            $data = array_keys($request->getPost());
            $totalData = count($data);
            
            $textoRemovido = 'item removido';
            if ($totalData > 1) {
                $textoRemovido = 'itens removidos';
            }
            
            foreach ($data as $id) {
                $model->delete($id);
            }
            
            $this->_FlashMessenger->setNamespace('index')->addMessage(sprintf('%s %s com sucesso!', $totalData, $textoRemovido));
        }
        
        $this->redirect('/clientes/index/filter/0');
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
        
        $this->redirect('/clientes/index');
    }

    public function restoreAction()
    {
        $request = $this->_request;
        $model = new Application_Model_Clientes();
        
        if ($request->isPost()) {
            $data = array_keys($request->getPost());
            $totalData = count($data);
            
            $textoRemovido = 'item restaurado ';
            if ($totalData > 1) {
                $textoRemovido = 'itens restaurados';
            }
            
            foreach ($data as $id) {
                $model->trash($id, 1);
            }
            
            $this->_FlashMessenger->setNamespace('index')->addMessage(sprintf('%s %s com sucesso!', $totalData, $textoRemovido));
        }
        
        $this->redirect('/clientes/index');
    }
}