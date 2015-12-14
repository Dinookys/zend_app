<?php

class UsuariosController extends Zend_Controller_Action
{
       
    
    public function init()
    {
        
    }

    public function indexAction()
    {
        // action body
    }

    public function addAction()
    {
        $form = new Application_Form_CriarUsuario;
        $request = $this->_request;
        if($request->isPost()){
            $data = $request->getPost();
            
            if($form->isValid($data))
            {
                print_r($data);   
            }else{
                $this->view->messages = $this->_helper->FlashMessenger->getMessages();                    
                $form->populate($data);
            }
        }
        
        $this->view->cadastroForm =  $form;
    }


}



