<?php

class Application_Form_EditUsuario extends Zend_Form
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
        
        $this->addElement('select','id_perfil',array(
            'label'  =>  'Perfil',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'multiOptions' => $options_perfil,
            'validators' => array($required)
        )); 
        
        $this->addElement('select','acesso',array(
            'label'  =>  'Habilitado',
            'required'  =>  true,     
            'class'     => 'form-control',
            'multiOptions' => array('0'=>'não', '1'=>'sim'),            
        ))
        ->setDefault('acesso', '0');

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
            array('HtmlTag', array('tag' => 'div', 'class' => 'form', 'id'=> 'editar_usuario'))
        ));
        
        $this->setMethod('post'); 
    }

}