<?php

class PropostasController extends Zend_Controller_Action
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

		$this->view->selectOptions = array(
		    'Dinheiro' => 'Dinheiro',
		    'Cheque' => 'Cheque',
		    'Boleto' => 'Boleto',
		    'Transferência' => 'Transferência',
		    'Nota promissória' => 'Nota promissória'
		);

    }

    public function indexAction()
    {
		$model = new Application_Model_Propostas();		
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

		// Recuperando dados do clientes baseado no Perfil ativo.
		if (in_array(CURRENT_USER_ROLE, $this->_acl['fullControl'])) {
			$select = $model->selectAll($filter, $like);
		} else {
			$ids = implode(',', $this->_ids);
			$select = $model->selectByUsersIds($ids, $filter, $like);
		}

		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage($this->_custom['itemCountPerPage'])
        ->setCurrentPageNumber($this->_getParam('page',1));		
        $this->view->empreendimento = new Application_Model_Empreendimentos();
		$this->view->paginator = $paginator;
		$this->view->messages = $this->_FlashMessenger->getMessages($this->_controllerName);		
		$this->view->barTitle = "Propostas";
    }

    public function editAction()
    {
		$request = $this->getRequest();
		$cid = $request->getParam('cid');
		$id = $request->getParam('id');
		$model = new Application_Model_Propostas();
		$modelCliente = new Application_Model_Clientes();
		$documentos = new Application_Model_Documentos();
		$form = new Application_Form_Proposta();				
		
		if(!in_array(CURRENT_USER_ROLE, array('Corretor','Financeiro'))){
		    $users = new Application_Model_Usuarios();
		    $db = Zend_Db_Table::getDefaultAdapter();
		    $result = $db->fetchAll($users->selectAll());
		    $options = array();
		    $required = new Zend_Validate_NotEmpty();
		    $required->setType($required->getType() | Zend_Validate_NotEmpty::STRING | Zend_Validate_NotEmpty::ZERO);
		    
		    foreach ($result as $key => $value){
		        $options[$value['id']] = $value['nome'] .' - '. $value['role'];		            
		    }
		    
		    $form->addElement('select', 'created_user_id', array(
		       'label' => 'Usuário',
		        'title' => 'Mude usuário responsável pela proposta',
                'required' => true,
                'filters' => array(
                    'StringTrim'
                ),
                'class' => 'form-control',
                'multiOptions' => $options,
                'validators' => array(
                    $required
                ),
                'decorators' => $form->setColSize(4)
            )); 
		    
		}

		if($request->isPost() && $form->isValid($request->getPost())){
		    
		    $data = $request->getPost();
		    
		    if($id == 0){
		        unset($data['id']);
                if($model->insert($data)){
                    $this->_FlashMessenger->setNamespace($this->_controllerName)->addMessage('Proposta adicionada com sucesso!');
                    $this->view->message_type = 'alert-success';
                    $this->redirect($this->_controllerName .'/edit/id/'. $model->lastInserId(). '/cid/' . $cid);                    
                }
		    }else if($model->update($id, $data)){
		        $this->view->messages = array('Atualziado com sucesso!');
		        $this->view->message_type = 'alert-success';
		    }else{
		        $this->view->messages = array('Sem alterações!');
		        $this->view->message_type = 'alert-info';
		    }	    

		}else{
		    $dataProposta = $model->selectById($id);
			// Verificando se ja existe uma proposta					
			if($dataProposta){
			    
				$data = $dataProposta;
				if(!empty($data['dados_extras'])){
					$dadosExtras = json_decode($data['dados_extras'], true);
					$data = array_merge($data, $dadosExtras);
					unset($data['dados_extras']);
				}				
			}else{
				$data = $modelCliente->selectById($cid);
				$dadosExtras = json_decode($data['dados_cliente'], true);
				$data = array_merge($data, $dadosExtras);
				// removendo id de quem criou a ficha do cliente
				$data['created_user_id'] = CURRENT_USER_ID;
			}		

		}
		
		$model->lockRow($data['id'], CURRENT_USER_ID, 1);
		
		// Verifica se esta bloquedo
        $is_locked = $this->_acl_model->checkLocked($data['locked'], $data['locked_by']);
        
        if ($is_locked) {
		    $this->view->messages = array('Item bloqueado para edição');
		    $this->view->hide = true;
		    $this->view->form = '';
		    return false;		    
		}
		
		// Verifica sem tem permissão pra acessar o conteudo.
		$is_autorized = $this->_acl_model->autorized($data['created_user_id'], $this->_ids);
		if(!$is_autorized){
		    $this->view->messages = array(
		        'Sem permissão de acesso'
		    );
		    $this->view->barTitle = 'Editando Proposta';
		    $this->view->form = '';
		    return false;
		}
		 
		// Pega Condições de pagamento
		$condicoes = $model->selectCondicoesPagamento($id);
		if($condicoes){
		    $this->view->sinal = $condicoes['sinal'];
		    $this->view->parcelas = json_decode($condicoes['parcelas'], true);
		}
		// Pega todos os arquivos relacinados ao cliente
		$this->view->anexos = $documentos->readDir(PUBLIC_PATH . DIRECTORY_SEPARATOR . 'uploads', $id);
		$this->view->documentos = $documentos;
		
		$this->view->barTitle = "Editando Proposta :: " . $data['nome'];
		$this->view->form = $form;
		$this->view->data = $data;		
		$form->populate($data);
		
    }

    public function anexosAction()
    {
		$request = $this->_request;
		$model_cliente = new Application_Model_Propostas();

		if ($request->isPost()) {
			$data = $request->getPost();
		} else {
			$id = $request->getParam('id');
			if (! isset($data['id'])) {
				$data['id'] = $id;
			}
		}

		$cliente = $model_cliente->selectById($data['id']);
		$cliente_data = json_decode($cliente['dados_extras'], true);

		if (in_array($cliente['created_user_id'], $this->_ids) or $cliente['created_user_id'] == CURRENT_USER_ID or in_array(CURRENT_USER_ROLE, $this->_acl['fullControl'])) {
			$this->view->barTitle = 'Anexar arquivos para :: ' . strtoupper($cliente_data['nome']) .' #' . $id;
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
			$cliente = $model->selectById($id);
			
			$dados_extras = json_decode($cliente['dados_extras'], true);
			
			if ($cliente) {
				$dataFromDb = $model->selectCondicoesPagamento($id);
				$data = array();
				$data['id_proposta'] = $cliente['id'];		
				
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
		$this->view->id = $id;
		$this->view->form = $form;
		$this->view->barTitle = 'Condiçoes de pagamento:: ' . strtoupper($dados_extras['nome']) .' #' . $id;
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
		$model = new Application_Model_Propostas();

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
            
            if(isset($data['status'])){
                echo $model->updateSample($id, array(
                    'status' => $data['status'],
                    'autorizado' => $data['autorizado'],
                    'last_user_id' => $data['last_user_id']
                ));
                
            }else{
                echo $model->updateSample($id, array(                    
                    'autorizado' => $data['autorizado'],
                    'last_user_id' => $data['last_user_id']
                ));
            }
        }        
    }

    public function propostaAction()
    {
        $model = new Application_Model_Propostas();
		$modelCliente = new Application_Model_Clientes();
		$modelImovel = new Application_Model_Empreendimentos();
		$request = $this->_request;
		$id = $request->getParam('id');

		if($id){
			$data = $model->selectByClientId($id);

			if($data['dados_extras'] && $data['status'] == 1){
			    $data = json_decode($data['dados_extras'], true);
			    
			    $empreendimento = $modelImovel->selectById($data['imovel'], array('nome','logradouro', 'incorporadora'));

			    $condicoes = $model->selectCondicoesPagamento($id);
			    $data['imovel'] = $empreendimento['nome'];
			    $data['parcelas'] = json_decode($condicoes['parcelas'],true);

				$this->view->empreendimento = $empreendimento;
			    $this->view->data = $data;			    
			}else{
			    $this->view->data = array();
			    $this->view->messages = array('A proposta sem aprovação');
		    	$this->view->message_type = "alert-info";
			}
			
		}

    }

    public function mediacaoAction()
    {
    	$model = new Application_Model_Propostas();
		$modelCliente = new Application_Model_Clientes();
		$modelImovel = new Application_Model_Empreendimentos();
		$request = $this->_request;
		$id = $request->getParam('id');

		if($id){
			$data = $model->selectByClientId($id);
			if($data['dados_extras']){
			    $data = json_decode($data['dados_extras'], true);
			    $empreendimento = $modelImovel->selectById($data['imovel']);

			    // Verificando se tem corretagem para o empreendimento			    
			    if($empreendimento['cad_corretagem'] == false){
			    	$this->view->messages = array('Proposta sem contrato de mediação');
			    	$this->view->message_type = "alert-info";
			    	$this->view->data = array();
			    	return false;			    	
			    }

			    $condicoes = $model->selectCondicoesPagamento($id);
			    $data['imovel'] = $empreendimento['nome'];
			    $data['cad_corretagem'] = $empreendimento['cad_corretagem'];
			    $data['parcelas'] = json_decode($condicoes['parcelas'],true);
			    $this->view->data = $data;			    
			}else{
			    $this->view->data = array();
			}
			
		}
    }

    public function archiveAction()
    {
        // action body
    }


}





