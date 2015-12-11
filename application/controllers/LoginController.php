<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $form = new Application_Form_Login;
        $request = $this->_request;
        
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
        
    }
    
    public function logoutAction()
    {
         $auth = Zend_Auth::getInstance();
         $auth->clearIdentity();
         $this->redirect('/index');
    }

}

