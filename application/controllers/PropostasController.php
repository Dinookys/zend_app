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
		$modelCliente = new Application_Model_Clientes();
		$documentos = new Application_Model_Documentos();
		$form = new Application_Form_Proposta();

		if($request->isPost() && $form->valid($request->getPost())){
			
			$data = $request->getPost();
			$validCPF = $model->selectBy($data['cpf']);
			// Verifica se o CPF foi alterado
			if($validCPF['id'] == $id){
				if($model->insert($data)){
					$this->view->messages = array('Sucesso!');
					$this->view->message_type = 'alert-success';
				}else if($model->update($id, $data)){
					$this->view->messages = array('Atualizado com sucesso!');
					$this->view->message_type = 'alert-success';
				}else{
					$this->view->messages = array('Nenhuma alteração feita!');						
				}				
			}else{
				$this->view->messages = array('CPF já esta cadastrado');
				$this->view->message_type = 'alert-warning';
			}
						
		}else{
			// Verificando se ja existe uma proposta
			$dataProposta = $model->selectByClientId($id);
			if($dataProposta){
				$data = $dataProposta;
				if(!empty($data['dados_extas'])){
					$dadosExtras = json_decode($data['dados_extras'], true);
					$data = array_merge($data, $dadosExtras);
					unset($data['dados_extras']);
				}				
			}else{			    
				// Recuperando dados do cliente
				$data = $modelCliente->selectById($id);				
				// removendo id de quem criou a ficha do cliente
				unset($data['created_user_id']);
			}			
			$data = array_merge($data, json_decode($data['dados_cliente'], true));
			unset($data['dados_cliente']);
		}
		
		if($data['locked'] == 1 && $data['locked_by'] != CURRENT_USER_ID && $data['locked_by'] != 0){
		    $this->view->messages = array('Item bloqueado para edição');
		    $this->view->form = '';
		    return false;
		}else{
		    $model->lockRow($data['id'], CURRENT_USER_ID, 1);
		}
		
		if (in_array($data['created_user_id'], $this->_ids) or $data['created_user_id'] == CURRENT_USER_ID or in_array(CURRENT_USER_ROLE, $this->_acl['fullControll'])) {		
			$form->populate($data);
			$this->view->barTitle = "Editando Proposta :: " . $data['nome'];
			$this->view->form = $form;
			
			// Pega Condições de pagamento
			$condicoes = $model->selectCondicoesPagamento($id);
			if($condicoes){
				$this->view->sinal = $condicoes['sinal'];
				$this->view->parcelas = json_decode($condicoes['parcelas'], true);	
			}
			// Pega todos os arquivos relacinados ao cliente
			$this->view->anexos = $documentos->readDir(PUBLIC_PATH . DIRECTORY_SEPARATOR . 'uploads', $id);
			$this->view->documentos = $documentos;
			return false;		
		}
		
		$this->view->messages = array(
					'Sem permissão de acesso'
			);
		$this->view->barTitle = 'Editando Proposta';
		$this->view->form = '';
    }

    public function anexosAction()
    {
		$request = $this->_request;
		$model_cliente = new Application_Model_Clientes();

		if ($request->isPost()) {
			$data = $request->getPost();
		} else {
			$id = $request->getParam('id');
			if (! isset($data['id'])) {
				$data['id'] = $id;
			}
		}

		$cliente = $model_cliente->selectById($data['id']);
		$cliente_data = json_decode($cliente['dados_cliente'], true);

		if (in_array($cliente['created_user_id'], $this->_ids) or $cliente['created_user_id'] == CURRENT_USER_ID or in_array(CURRENT_USER_ROLE, $this->_acl['fullControll'])) {
			$this->view->barTitle = 'Anexar arquivos para :: ' . strtoupper($cliente_data['nome']);
			$this->view->propostaId = $data['id'];
			$this->view->allow = true;
		} else {
			$this->view->allow = false;
			$this->view->propostaId = $id;
			$this->view->messages = array(
					'Sem permissão de acesso'
			);
		}
    }

    public function condicoesPagamentoAction()
    {
		$form = new Application_Form_CondicoesPagamento();
		$model = new Application_Model_Propostas();
		$model_cliente = new Application_Model_Clientes();
		$request = $this->_request;
		$id = $request->getParam('id');

		if ($request->isPost()) {
			$data = $request->getPost();
			$data['parcelas'] = json_encode($data['parcelas']);
				
			// Valida os dados
			if($form->isValid($data)){
				
				if($model->selectCondicoesPagamento($id)){
					$result = $model->updateCondicoesPagamento($id, $data);					
				}else{
					$result = $model->insertCondicoesPagamento($data);
				}
				 
				if($result){
					$this->view->messages = array(
							'Atualizado com sucesso!'
					);
				}
			}			

		} else {
			$cliente = $model_cliente->selectById($id);
			if ($cliente) {
				$dataFromDb = $model->selectCondicoesPagamento($id);
				$data = array();
				$data['id_cliente'] = $cliente['id'];								
				if($dataFromDb){
					$data = $dataFromDb;
				}
			}else{
				$this->view->messages = array(
						'Cliente não encontrado!'
				);
				return false;
			}
		}
		
		$form->populate($data);
		$this->view->selectOptions = array(
		    'dinheiro' => 'Dinheiro',
		    'cheque' => 'Cheque',
		    'cartão' => 'Cartão'
		);
		$this->view->id = $id;
		$this->view->form = $form;
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

		$this->redirect('/'.$this->_controllerName.'/index/filter/0');
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

			$this->_FlashMessenger->setNamespace($this->_controllerName)->addMessage(sprintf('%s %s com sucesso!', $totalData, $textoRemovido));
		}

		$this->redirect('/'.$this->_controllerName);
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

			$this->_FlashMessenger->setNamespace($this->_controllerName)->addMessage(sprintf('%s %s com sucesso!', $totalData, $textoRemovido));
		}

		$this->redirect('/'.$this->_controllerName);
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
		$this->redirect('/'.$this->_controllerName);
    }

    public function alteraStateAction()
    {
        // ajax tpl
        $this->_helper->layout->setLayout('ajax');
        $request = $this->_request;
        $model = new Application_Model_Propostas();
        
        if($request->isPost()){
            $data = $request->getPost();            
            $id = $data['id'];
            
            echo $model->updateSample($id, array(
                'status' => $data['status'],
                'autorizado' => $data['autorizado'],
                'last_user_id' => $data['last_user_id']
            ));
        }        
    }


}

