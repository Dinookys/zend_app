<?php

class Application_Form_PerfilUsuario extends Zend_Form
{

    public function init()
    {
        
        // Pega a lista de perfis no banco de dados na tabela #__perfis
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
        
        $this->addElement('password','password',array(
            'label'  =>  'Senha',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'autocomplete' => 'off',
            'validators' => array(
                array('StringLength', false, array('min' => 4, 'max' => 10))
            )
        ));
        
        $this->addElement('password','confirm',array(
            'label'  =>  'Confirma Senha',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'autocomplete' => 'off',
            'validators' => array(
                array('identical', false, array('token' => 'password'))
            )
        ));

        $this->addElement('hidden','id',array(
            'label'  =>  ''
        ));        
        
        $submit = $this->addElement('submit','Atualizar', array(
            'class' => 'btn btn-primary btn-md'            
        ));        

        $this->setElementDecorators(array(
            'ViewHelper',            
            'Errors',
            'Label',
            'Description',
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))            
        ), array('Atualizar'), false);   
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',            
            'Description',
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))
        ), array('Atualizar', 'id'), true);

        $this->setDecorators(array(
            'FormElements',
            'Form',            
            array('HtmlTag', array('tag' => 'div', 'class' => 'panel panel-body panel-default'))
        ));
        
        $this->setAttrib('id', 'edit_usuario');
        $this->setAttrib('class', 'form');
        $this->setMethod('post');
    }

}