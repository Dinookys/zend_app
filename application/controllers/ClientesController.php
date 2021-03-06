<?php

class ClientesController extends Zend_Controller_Action
{

    protected $data_user = null;

    protected $_custom = null;

    protected $_acl = null;

    protected $_acl_model = null;

    protected $_actionName = null;

    protected $_controllerName = null;

    protected $_FlashMessenger = null;

    protected $_ids = null;

    public function init()
    {
        $auth = Zend_Auth::getInstance();
        $this->data_user = $auth->getIdentity();
        
        if (! $auth->hasIdentity()) {
            $this->redirect('/login');
        } else {
            $this->_acl_model = new Application_Model_Acl_Acl();
            if (! $this->_acl_model->isAllowed()) {
                $this->redirect('/error/forbidden');
            }
        }
        
        $this->view->user = $this->data_user;
        $this->view->model_user = new Application_Model_Usuarios();
        $this->view->model = new Application_Model_Clientes();  
        
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
        $model = new Application_Model_Clientes();
        $request = $this->_request;
        $filter = $request->getParam('filter');
        
        $like = NULL;
        
        if ($request->isPost()) {
            $data = $request->getPost();
            $like = $data['search'];
            $this->view->data = $data;
        }
        
        if (is_null($filter)) {
            $filter = 1;
        }
        
        // Recuperando dados do clientes baseado no Perfil ativo.
        if (in_array(CURRENT_USER_ROLE, $this->_acl['fullControl'])) {            
            $select = $model->selectAll($filter, $like);
        } else {
            $ids = implode(',', $this->_ids);
            $select = $model->selectByUsersIds($filter, $ids, $like);
        }
        
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage($this->_custom['itemCountPerPage'])
        ->setCurrentPageNumber($this->_getParam('page',1));
        
        $this->view->paginator = $paginator;
        
        $this->view->messages = $this->_FlashMessenger->getMessages($this->_controllerName);        
        $this->view->barTitle = "Clientes";
    }

    public function addAction()
    {
        $form = new Application_Form_CadastroCliente();
        $model = new Application_Model_Clientes();
        $request = $this->_request;
        
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $data = $request->getPost();
            
            $check = $model->selectBy($data['cpf']);
            
            if ($check) {
                $this->view->messages = array(
                    'CPF ' . $data['cpf'] . ' já cadastrado!'
                );
                $this->view->message_type = 'alert-warning';
                $form->populate($data);
            } else {
                if ($model->insert($data)) {
                    $this->view->messages = array(
                        'Cadastro realizado com sucesso'
                    );
                    $this->view->message_type = 'alert-success';
                    $this->redirect($this->_controllerName . '/edit/id/' . $model->lastInserId());
                } else {
                    $form->populate($data);
                }
            }
        }
        
        $this->view->barTitle = "Novo Cliente";
        $this->view->form = $form;
    }

    public function editAction()
    {
        $form = new Application_Form_CadastroCliente();
        $model = new Application_Model_Clientes();
        $request = $this->_request;        
        
        if ($request->isPost() && $form->isValid($request->getPost())) {
            
            $data = $request->getPost();
            $id = $data['id'];
            $cpf = $data['cpf'];
            $data['last_user_id'] = CURRENT_USER_ID;
            
            $check = $model->selectBy($cpf);
            
            if ($check && $check['id'] != $id) {
                $this->view->messages = array(
                    'CPF já cadastro.'
                );
                $this->view->message_type = "alert-danger";
                $form->populate($data);
            } else 
                if ($model->update($data['id'], $data) == 0) {
                    $this->view->messages = array(
                        'Não foi feito nenhuma alteração.'
                    );
                    $this->view->message_type = "alert-info";
                } else {
                    $this->view->messages = array(
                        'Atualizado com sucesso!'
                    );
                    $this->view->message_type = "alert-success";
                }
            
            $form->populate($data);
        } else {
            $id = $this->getParam('id');
            $result = $model->selectById($id);
            $data = json_decode($result['dados_cliente'], true);
            $data['id'] = $result['id'];
            $data['cpf'] = $result['cpf'];
            $data['last_user_id'] = $result['last_user_id'];
            $data['created_user_id'] = $result['created_user_id'];
            $data['locked'] = $result['locked'];
            $data['locked_by'] = $result['locked_by'];
            
            $is_locked = $this->_acl_model->checkLocked(
                array(
                    'locked_by' => $data['locked_by'], 
                    'locked' => $data['locked']
                    
                ));
            
            if ($is_locked) {
                $this->view->messages = array('Item bloqueado para edição');
                $this->view->form = '';
                return false;
            }else{                
                $model->lockRow($data['id'], CURRENT_USER_ID, 1);
            }
            
            if ($result['created_user_id'] == CURRENT_USER_ID or in_array(CURRENT_USER_ROLE, $this->_acl['fullControl']) or in_array($result['created_user_id'], $this->_ids)) {                
                $form->populate($data);
            } else {
                $this->view->form = '';
                $this->view->messages = array(
                    'Cadastro não encontrado'
                );
                $this->view->message_type = "alert-danger";
                return false;
            }
        }
        
        $form->addFieldId($id);
        // pegando data padrao Us para pt_BR
        $data_desc = new Zend_Date($data['data']);
        $form->getElement('data_desc')->setDescription(
				'<label>Data</label><p>' . $data_desc->toString('dd/MM/yyyy') .'</p>'
				);
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
            
            $this->_FlashMessenger->setNamespace($this->_controllerName)->addMessage(sprintf('%s %s com sucesso!', $totalData, $textoRemovido));
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
            
            $this->_FlashMessenger->setNamespace($this->_controllerName)->addMessage(sprintf('%s %s com sucesso!', $totalData, $textoRemovido));
        }
        
        $this->redirect('/clientes/index');
    }
    
    public function archiveAction()
    {       
        $request = $this->_request;
        $model = new Application_Model_Clientes();
    
        if ($request->isPost()) {
            $data = array_keys($request->getPost());
            $totalData = count($data);
    
            $textoRemovido = 'item movido para arquivados ';
            if ($totalData > 1) {
                $textoRemovido = 'itens movido para arquivados';
            }
    
            foreach ($data as $id) {
                $model->trash($id, 3);
            }
    
            $this->_FlashMessenger->setNamespace($this->_controllerName)->addMessage(sprintf('%s %s com sucesso!', $totalData, $textoRemovido));
        }
    
        $this->redirect('/clientes/index');
    }        

    public function unlockAction()
    {
        $request = $this->_request;
        $model = new Application_Model_Clientes();
        
        if ($request->isPost()) {
            $data = $request->getPost();
            if (isset($data['locked_by']) == CURRENT_USER_ID) {
                $model->lockRow($data['id'], 0, 0);
            }
        }
        $this->redirect('/clientes/index');
    }

}

