<?php

class ComissoesController extends Zend_Controller_Action
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
        $this->view->messages = $this->_FlashMessenger->getMessages($this->_controllerName);
        
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
        
        $like = NULL;
        
        if ($request->isPost()) {
            $data = $request->getPost();
            $like = $data['search'];
            $this->view->data = $data;
        }
        
        $model_proposta = new Application_Model_Propostas();
        $select = $model_proposta->getPropostasAutorizadas($filter, true, $like);
        
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
        
        if (empty($empreendimento)) {
            $this->view->messages = array(
                'Empreendimento não informado na proposta!'
            );
            $this->view->message_type = 'alert-warning';
            $this->view->hide = true;
            return false;
        } else {
            $unidades = json_decode($empreendimento['unidades'],true);
            // Passando o valor da comissão do imóvel
            foreach ($unidades as $unidade){
                if($unidade['bloco-quadra'] == $dados_extras['imovel_bloco_quadra']){
                    $data['total'] = $unidade['comissao'];
                }
            }
        }
        
        $is_locked = $this->_acl_model->checkLocked($data['locked'],$data['locked_by']);
            
        if ($is_locked) {
            $this->view->messages = array(
                'Item bloqueado para edição'
            );
            $this->view->form = '';
            $this->view->hide = true;
            return false;
        } else {
            $model_proposta->lockRow($data['id_proposta'], CURRENT_USER_ID, 1);
        }
        
        if ($request->isPost()) {
            $data = $request->getPost();
            $data['parcelas_pagas'] = isset($data['parcelas_pagas']) ? json_encode($data['parcelas_pagas']) : array();
            $data['comissao'] = isset($data['comissao']) ? json_encode($data['comissao']) : json_encode(array());

            if ($form->valid($data)) {
                $dataUpdate = $data;
                
                unset($dataUpdate['locked']);
                unset($dataUpdate['locked_by']);
                unset($dataUpdate['nome']);
                unset($dataUpdate['total']);
                $dataUpdate['last_modified'] = date('Y-d-m h:i:s', time());
                
                if ($model_proposta->updateCondicoesPagamento($id, $dataUpdate)) {
                    $this->view->messages = array(
                        'Atualizado com sucesso!'
                    );
                    $this->view->message_type = 'alert-success';
                }else{
                    $this->view->messages = array(
                        'Sem alterações!'
                    );
                    $this->view->message_type = 'alert-info';
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
        $this->redirect('/' . $this->_controllerName);
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
                $model->trashFinanceiro($id, 1);
            }
            
            $this->_FlashMessenger->setNamespace($this->_controllerName)->addMessage(sprintf('%s %s com sucesso!', $totalData, $textoRemovido));
        }
        
        $this->redirect('/' . $this->_controllerName);
    }

    public function archiveAction()
    {
        $request = $this->_request;
        $model = new Application_Model_Propostas();
        
        if ($request->isPost()) {
            $data = array_keys($request->getPost());
            $totalData = count($data);
        
            $textoRemovido = 'item movido para arquivados ';
            if ($totalData > 1) {
                $textoRemovido = 'itens movido para arquivados ';
            }
        
            foreach ($data as $id) {
                $model->trashFinanceiro($id, 3);
            }
        
            $this->_FlashMessenger->setNamespace($this->_controllerName)->addMessage(sprintf('%s %s com sucesso!', $totalData, $textoRemovido));
        }
        
        $this->redirect('/' . $this->_controllerName);
    }
    
    public function unlockAction()
    {
        $request = $this->_request;
        $model = new Application_Model_Propostas();
    
        if ($request->isPost()) {
            $data = $request->getPost();
            if (isset($data['locked_by']) == CURRENT_USER_ID) {
                $model->lockRow($data['id_proposta'], 0, 0);
            }
        }
        $this->redirect('/' . $this->_controllerName);
    }
    

}



