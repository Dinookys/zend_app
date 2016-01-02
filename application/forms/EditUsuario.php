<?php

class Application_Form_EditUsuario extends Zend_Form
{
    public $notEmpty;
    
    public function init()
    {
        // Verifica se o campo select esta com o valor null   
        $this->notEmpty = new Zend_Validate_NotEmpty ();
        $this->notEmpty->setType ($this->notEmpty->getType() | Zend_Validate_NotEmpty::INTEGER | Zend_Validate_NotEmpty::ZERO);
        
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
        
        $this->addElement('select','id_perfil',array(
            'label'  =>  'Perfil',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'multiOptions' => $options_perfil,
            'validators' => array($this->notEmpty)
        ));
        
        $this->addElement('select','parent_id',array(
            'label'  =>  'Superior',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',            
            'validators' => array($this->notEmpty),
            'registerInArrayValidator' => false
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
        
        $this->addElement('hidden','role',array(
            'label'  =>  ''
        ));
        
        $submit = $this->addElement('submit','Atualizar', array(
            'class' => 'btn btn-primary btn-md'            
        ));
        
        $this->setElementDecorators(array(            
            'ViewHelper',
            'Errors',            
            'Description',
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))
        ), array('Atualizar', 'id'), true);
        
        $this->setElementDecorators(array(
            'Label',
            'ViewHelper',
            'Errors',
            'Description',
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))
        ), array('Atualizar'), false);

        $this->setDecorators(array(
            'FormElements',
            'Form',            
            array('HtmlTag', array('tag' => 'div', 'class' => 'panel panel-body panel-default'))
        ));
        
        $this->setAttrib('id', 'editar_usuario');
        $this->setAttrib('class', 'form');
        $this->setMethod('post');
        
    }
    
    /**
     * Passa um array para $options_superior baseado na RoleName informado
     * @param string $roleName
     */
    public function setSuperior($roleName){
        $perfil = new Application_Model_Usuarios();
        $data = $perfil->selectByRole($roleName);
        $options = array("-- Selecione --");
        
       if($data){
           foreach ($data as $value){
               $options[$value->id] = $value->nome;
           }    
       }       
       return $options;     
    }
}