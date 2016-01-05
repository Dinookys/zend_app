<?php

class PropostasController extends Zend_Controller_Action
{

    protected $data_user = null;

    protected $_custom = null;

    protected $_acl = null;

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
            $acl = new Application_Model_Acl_Acl();
            if (! $acl->isAllowed()) {
                $this->redirect('/error/forbidden');
            }
        }
        
        $this->view->user = $this->data_user;
        
        $this->_modelUsers = new Application_Model_Usuarios();
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        
        $this->_acl = $config->getOption('acl');
        
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
        $this->view->model_user = new Application_Model_Usuarios();
        
        if ($this->data_user->childrens_ids) {
            $this->_ids = $this->data_user->childrens_ids;
            $this->_ids[] = CURRENT_USER_ID;
        } else {
            $this->_ids = array(
                CURRENT_USER_ID
            );
        }
    }

    public function indexAction()
    {
        $model = new Application_Model_Propostas();
        $request = $this->_request;
        $filter = $request->getParam('filter');
        
        if (is_null($filter)) {
            $filter = 1;
        }
        
        // Recuperando dados do clientes baseado no Perfil ativo.
        if (in_array(CURRENT_USER_ROLE, $this->_acl['fullControll'])) {
            $data = $model->selectAll($filter);
        } else {
            $ids = implode(',', $this->_ids);
            $data = $model->selectByUsersIds($ids, $filter);
        }
        
        $data = $model->convertData($data);
        $this->view->messages = $this->_FlashMessenger->getMessages($this->_controllerName);
        $this->view->data = $data;
        $this->view->barTitle = "Propostas";
    }

    public function editAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $model = new Application_Model_Propostas();
        $documentos = new Application_Model_Documentos();
        
        $form = new Application_Form_Proposta();
        
        $model->insert(array(
            'id_cliente' => $id
        ));
        
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
            
            $data = $model->selectByClientId($id);
            
            if (! empty($data['dados_extras'])) {
                $data = array_merge($data, json_decode($data['dados_extras'], true));
                unset($data['dados_extras']);
            }
            
            $data = array_merge($data, json_decode($data['dados_cliente'], true));
            unset($data['dados_cliente']);
        }
        
        if ($data['locked'] == 1 && $data['locked_by'] != CURRENT_USER_ID && $data['locked_by'] != 0) {
            $this->view->messages = array(
                'Item bloqueado para edição'
            );
            $this->view->form = '';
            return false;
        } else {
            $model->lockRow($data['id'], CURRENT_USER_ID, 1);
        }
        
        if (in_array($data['created_user_id'], $this->_ids) or $data['created_user_id'] == CURRENT_USER_ID or in_array(CURRENT_USER_ROLE, $this->_acl['fullControll'])) {
            $this->view->barTitle = "Editando proposta :: " . $data['nome'];
            $form->populate($data);
            $this->view->form = $form;            
        } else {
            $this->view->messages = array(
                'Sem permissão de acesso'
            );
        }
        
        // recuperando anexos da proposta
        $this->view->anexos = $documentos->readDir(PUBLIC_PATH . DIRECTORY_SEPARATOR . 'uploads', $id);
        $this->view->gereDocs = new Application_Model_Documentos();
        
    }

    public function deleteAction()
    {
        $request = $this->_request;
        $model = new Application_Model_Propostas();
        
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
        
        $this->redirect('/propostas/index/filter/0');
    }

    public function trashAction()
    {
        $request = $this->_request;
        $model = new Application_Model_Propostas();
        
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
        
        $this->redirect('/propostas/index');
    }

    public function restoreAction()
    {
        $request = $this->_request;
        $model = new Application_Model_Propostas();
        
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
        
        $this->redirect('/propostas/index');
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
        $this->redirect('/propostas/index');
    }

    public function anexosAction()
    {
        $request = $this->_request;
        if($request->isPost()){
            $data = $request->getPost();            
            $this->view->propostaId = $data['id'];            
        }
    }

}