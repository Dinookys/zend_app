<?php

class EmpreendimentosController extends Zend_Controller_Action
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
        $model = new Application_Model_Empreendimentos();
        $request = $this->_request;
        
        $filter = $request->getParam('filter');
        
        if(is_null($filter)){
            $filter = 1;
        }
        
        $like = NULL;
        
        if ($request->isPost()) {
            $data = $request->getPost();
            $like = $data['search'];
            $this->view->data = $data;
        }
        
        $select = $model->selectQueryList($filter, $like);
        
        $this->view->controllerName = $this->_controllerName;
        $this->view->model_user = new Application_Model_Usuarios();
        
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage($this->_custom['itemCountPerPage'])
        ->setCurrentPageNumber($this->_getParam('page',1));
        
        $this->view->paginator = $paginator;
        
        $this->view->barTitle = "Empreendimentos";
        $this->view->messages = $this->_FlashMessenger->getMessages($this->_controllerName);
    }

    public function addAction()
    {
        $model = new Application_Model_Empreendimentos();
        $request = $this->_request;
        $form = new Application_Form_Empreendimento();
        
        if($request->isPost()){
            $data = $request->getPost();
            // Convertendo campo para json
            $data['unidades'] = json_encode($data['unidades']);            
            if($form->isValid($data)){                
                if($model->insert($data)){
                    $this->_FlashMessenger->setNamespace($this->_controllerName)->addMessage('Empreendimento adicionado com sucesso');
                    $this->_redirect($this->_controllerName);
                }else{
                    $this->view->messages = array(
                        'Problemas ao tentar adicionar empreendimento. Por favor tente novamente'
                    );
                    $this->view->message_type = 'alert-danger';
                }    
            }            
            $form->populate($data);
        }        
        
        $this->view->barTitle = 'Cadastro de Empreendimento';
        $this->view->form = $form;
    }

    public function editAction()
    {
        $id = $this->_request->getParam('id');
        $request = $this->_request;
        $form = new Application_Form_Empreendimento();
        $model = new Application_Model_Empreendimentos();
        
        if($request->isPost()){
            $data = $request->getPost();
            $data['unidades'] = json_encode($data['unidades']);
            $data['last_user_id'] = CURRENT_USER_ID;
            $data['id'] = $id;
            
            if($form->isValid($data)){
                if($model->update($data)){
                    $this->view->messages = array(
                        'Atualizado com sucesso!'
                    );                    
                    $this->view->message_type = 'alert-success';
                    
                }else{
                    $this->view->messages = array(
                        'Problemas ao tentar atualizar!'
                    );                    
                    $this->view->message_type = 'alert-danger';
                }
            }
            
        }else{
            $data = $model->selectById($id);
        }
        
        $is_locked = $this->_acl_model->checkLocked($data['locked'],$data['locked_by']);                
            
        if ($is_locked) {
            $this->view->messages = array('Item bloqueado para edição');
            $this->view->form = '';
            $this->view->hide = true;
            return false;
        }else{
            $model->lockRow($data['id'], CURRENT_USER_ID, 1);
        }
        
        $form->addElement('hidden','id',array(
           'value' => $id 
        ));
        $form->populate($data);
        $this->view->form = $form;
    }

    public function deleteAction()
    {
		$request = $this->_request;
		$model = new Application_Model_Empreendimentos();

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
		$model = new Application_Model_Empreendimentos();

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
		$model = new Application_Model_Empreendimentos();

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
		$model = new Application_Model_Empreendimentos();
		if ($request->isPost()) {		    
			$data = $request->getPost();
			if (isset($data['locked_by']) == CURRENT_USER_ID) {
				$model->lockRow($data['id'], 0, 0);
			}
		}
		$this->redirect('/'.$this->_controllerName);
    }

    public function getImovelAction()
    {
        $request = $this->_request;
        $model = new Application_Model_Empreendimentos();
        $this->_helper->layout->setLayout('ajax');
        
        if($request->isPost()){            
            $data = $request->getPost();
            $result = $model->selectById($data['id']);       
            if($data['unidades']){
                $return = array();
                $result = json_decode($result['unidades']);
                // Formatando index
                foreach ($result as $unidade){
                    $return[] = $unidade;
                }
                echo json_encode($return);
                return false;
            }            
            echo json_encode($result);
        }
    }

    public function archiveAction()
    {
		$request = $this->_request;
		$model = new Application_Model_Empreendimentos();

		if ($request->isPost()) {
			$data = array_keys($request->getPost());
			$totalData = count($data);

			$textoRemovido = 'item movido para arquivados';
			if ($totalData > 1) {
				$textoRemovido = 'itens movidos para arquivados';
			}

			foreach ($data as $id) {
				$model->trash($id, 3);
			}

			$this->_FlashMessenger->setNamespace($this->_controllerName)->addMessage(sprintf('%s %s com sucesso!', $totalData, $textoRemovido));
		}

		$this->redirect('/'.$this->_controllerName);
    }


}











