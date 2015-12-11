<?php

class Application_Form_Login extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        $this->addElement('text','email',array(
            'label'  =>  'Email',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control'
        ));
        
        $this->addElement('password','senha',array(
            'label'  =>  'Senha',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control'
        ));
        
        $submit = $this->addElement('submit','Acessar', array(
            'class' => 'btn btn-primary btn-md'            
        ));               
        
        $this->setElementDecorators(array(
            'ViewHelper',            
            'Errors',
            'Label',
            'Description',
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))            
        ), array('Acessar'), false);       
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',            
            'Description',
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))
        ), array('Acessar'), true);

        $this->setDecorators(array(
            'FormElements',
            'Form',            
            array('HtmlTag', array('tag' => 'div', 'class' => 'form', 'id'=> 'user_login'))
        ));
        
        $this->setMethod('post');        
    }

}

