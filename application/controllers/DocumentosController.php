<?php

class DocumentosController extends Zend_Controller_Action
{

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
		// action body
	}

	public function uploadAjaxAction()
	{
		$this->_helper->layout->setLayout('ajax');
		$data = $this->_request->getPost();
		 
		if(isset($data['id'])){
			$extraDados = $data['id'];
			$extraDados .= '_' . date('d_m_Y');
		}else{
			$extraDados = date('d_m_Y');
		}
				 
		$path = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'uploads';		 
		$upload = new Zend_File_Transfer_Adapter_Http();
		$upload->setDestination($path);
		
		// Returns all known internal file information
		$files = $upload->getFileInfo();
		foreach ($files as $file => $info) {
			// Se não existir arquivo para upload
			if (!$upload->isUploaded($file)) {
				print '<p class="alert alert-warning">Nenhum arquivo selecionado para upload<p>';
				continue;
			}else{
				$fileName = $extraDados .'_'. str_replace(' ','_', strtolower($info['name']));
				// Renomeando o arquivo
				$upload->addFilter('Rename', array(
						'target'=> $path.DIRECTORY_SEPARATOR.$fileName,
						'overwrite' =>true));
			}
		  
			// Validação do arquivo ?
			if (!$upload->isValid($file)) {
				print '<p class="alert alert-danger" > <b>' . $file.'</b>. Arquivo inválido </p>';
				continue;
			}else{
				if($upload->receive($info['name'])){
					print '<p class="alert alert-success"> Arquivo: <b>' .$info['name']. '</b> enviado com sucesso e renomeado para: <b>'. $fileName .'</b> </p>';
				}
			}
		}
	}

}