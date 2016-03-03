<?php

class ComissoesController extends Zend_Controller_Action
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
        $request = $this->_request;
        $filter = $request->getParam('filter');
        
        if (is_null($filter)) {
            $filter = 1;
        }
        
        $model_proposta = new Application_Model_Propostas();
        $select = $model_proposta->getPropostasAutorizadas($filter, true);
        
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage($this->_custom['itemCountPerPage'])->setCurrentPageNumber($this->_getParam('page', 1));
        
        $this->view->paginator = $paginator;
        $this->view->barTitle = "Liberar de comissões";
    }

    public function editAction()
    {
        $request = $this->_request;
        $model_proposta = new Application_Model_Propostas();
        $model_imovel = new Application_Model_Empreendimentos();
        $form = new Application_Form_Financeiro();
        $id = $request->getParam('id');
        
        $data = $model_proposta->getPropostaAutorizada($id);
        $dados_extras = json_decode($data['dados_extras'], true);
        
        $data['nome'] = $dados_extras['nome'];
        $data['data_proposta'] = $dados_extras['data_proposta'];
        unset($data['dados_extras']);
        
        // Se estiver zerado sera gerado um erro informando a situação
        $empreendimento = $model_imovel->selectById($dados_extras['imovel']);
        
        if (empty($empreendimento['comissao'])) {
            $this->view->messages = array(
                'Valor da comissão do empreendimento: <b>' . $empreendimento['nome'] . '</b> não informado!'
            );
            $this->view->message_type = 'alert-warning';
            $this->view->hide = true;
            return false;
        } else {
            // Passando o valor da comissão do imóvel
            $data['total'] = $empreendimento['comissao'];
        }
        
        if ($data['locked'] == 1 && $data['locked_by'] != CURRENT_USER_ID && $data['locked_by'] != 0) {
            $this->view->messages = array(
                'Item bloqueado para edição'
            );
            $this->view->form = '';
            $this->view->hide = true;
            return false;
        } else {
            $model_proposta->lockRow($data['id_cliente'], CURRENT_USER_ID, 1);
        }
        
        if ($request->isPost()) {
            $data = $request->getPost();
            
            if (isset($data['parcelas_pagas'])) {
                $data['parcelas_pagas'] = json_encode($data['parcelas_pagas']);
            }
            
            if (isset($data['comissao'])) {
                $data['comissao'] = json_encode($data['comissao']);
            }
            
            if ($form->valid($data)) {
                $dataUpdate = $data;
                
                unset($dataUpdate['locked']);
                unset($dataUpdate['locked_by']);
                unset($dataUpdate['nome']);
                unset($dataUpdate['total']);
                
                if ($model_proposta->updateCondicoesPagamento($id, $dataUpdate)) {
                    $this->view->messages = array(
                        'Atualizado com sucesso!'
                    );
                    $this->view->message_type = 'alert-success';
                }
            }
        }
        
        $this->view->barTitle = 'Parcelas: ' . $data['nome'];
        $this->view->selectOptions = array(
            'dinheiro' => 'Dinheiro',
            'cheque' => 'Cheque',
            'cartão' => 'Cartão'
        );
        
        $form->populate($data);
        $this->view->form = $form;
        $this->view->data = $data;
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
        
        $this->redirect('/' . $this->_controllerName);
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
        
        $this->redirect('/comissoes/index');
    }

    public function unlockAction()
    {
        $request = $this->_request;
        $model = new Application_Model_Propostas();
        
        if ($request->isPost()) {
            $data = $request->getPost();
            if (isset($data['locked_by']) == CURRENT_USER_ID) {
                $model->lockRow($data['id_cliente'], 0, 0);
            }
        }
        $this->redirect('/' . $this->_controllerName);
    }
}

