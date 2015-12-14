<?php

class LoginController extends Zend_Controller_Action
{
       
    public function init()
    {
        
    }

    public function indexAction()
    {
        $form = new Application_Form_Login;
        $request = $this->_request;
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $custom = $config->getOption('custom');
        
        if($request->isPost())
        {
            $data = $request->getPost();
             
            if($form->isValid($data))
            {
                $data = $form->getValues();                
                $login = Application_Model_Login::login($data['email'], $data['senha']);
                
                if($login === true)
                {
                    $this->redirect('/index');
                }else{                    
                    $this->_helper->FlashMessenger->addMessage($login);
                    $this->view->messages = $this->_helper->FlashMessenger->getMessages();                    
                    $form->populate($data);
                }
            }            
        }else{
            $this->view->messages = $this->_helper->FlashMessenger->getMessages();
        }        
        
        $this->view->form = $form;
        $this->view->logourl = $custom['logourl'];
        
    }
    
    public function logoutAction()
    {
         $auth = Zend_Auth::getInstance();
         $auth->clearIdentity();
         $this->redirect('/index');
    }

}

