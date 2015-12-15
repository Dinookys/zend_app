<?php

class Application_Form_CriarUsuario extends Zend_Form
{

    public function init()
    {
        // Verifica se o campo select esta com o valor null   
        $required = new Zend_Validate_NotEmpty ();
        $required->setType ($required->getType() | Zend_Validate_NotEmpty::INTEGER | Zend_Validate_NotEmpty::ZERO);
        
        // Pega a lista de perfis no banco de dados na tabla #__perfis
        $perfis = new Application_Model_Usuarios();        
        $lista_options = $perfis->getPerfis();
        
        $options_perfil = array("-- Selecione --");
        
        foreach ($lista_options as $option){
            $options_perfil[$option->id] = $option->role;
        }  

        
        $this->addElement('text','nome',array(
            'label'  =>  'Nome',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control'
        ));

        $this->addElement('text','email',array(
            'label'  =>  'Email',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control'
        ));
        
        $this->addElement('select','perfil',array(
            'label'  =>  'Perfil',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'multiOptions' => $options_perfil,
            'validators' => array($required)
        ));
                
        
        $this->addElement('password','senha',array(
            'label'  =>  'Senha',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control'
        ));

        $this->addElement('password','confirm',array(
            'label'  =>  'Confirma Senha',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'validators' => array(
                array('identical', false, array('token' => 'senha'))
            )
        ));
        
        $submit = $this->addElement('submit','Cadastrar', array(
            'class' => 'btn btn-primary btn-md'            
        ));               
        
        $this->setElementDecorators(array(
            'ViewHelper',            
            'Errors',
            'Label',
            'Description',
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))            
        ), array('Cadastrar'), false);       
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',            
            'Description',
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))
        ), array('Cadastrar'), true);

        $this->setDecorators(array(
            'FormElements',
            'Form',            
            array('HtmlTag', array('tag' => 'div', 'class' => 'form', 'id'=> 'cadastrar_usuario'))
        ));
        
        $this->setMethod('post'); 
    }


}

