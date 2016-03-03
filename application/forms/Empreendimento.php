<?php

class Application_Form_Empreendimento extends Zend_Form
{

    public function init()
    {
        // Verifica se o campo select esta com o valor null   
        $required = new Zend_Validate_NotEmpty ();
        $required->setType ($required->getType() | Zend_Validate_NotEmpty::STRING | Zend_Validate_NotEmpty::ZERO); 
        
        $data_now = new Zend_Date();
        
        $options_sim_nao = array('Não','Sim');
        $options_categoria = array(
            'Alto padrão'=> 'Alto padrão',
            'Médio padrão' => 'Médio padrão',
            'Econômico' => 'Econômico'            
        );
        $options_empty = array('');


        $this->addElement('text','nome',array(
            'label'  =>  'Nome',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(12)
        ));
        
        $this->addElement('text','logradouro',array(
            'label'  =>  'Logradouro',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('select','categoria',array(
            'label'  =>  'Categoria',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'multiOptions' => $options_categoria,            
            'decorators' => $this->setColSize(3)
        ));

        $this->addElement('select','cad_corretagem',array(
            'label'  =>  'Cad. Corretagem Mediação',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'multiOptions' => $options_sim_nao,            
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('text','incorporadora',array(
            'label'  =>  'Incorporadora',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text','tipo',array(
            'label'  =>  'Tipo',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text','comissao',array(
            'label'  =>  'Comissão do empreendimento',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('hidden','unidades',array(
            'label' =>'',
            'decorators' => $this->setColSize(1)
        ));
        
        $this->addElement('hidden', 'created_user_id', array(
            'value' => CURRENT_USER_ID,
            'decorators' => $this->setColSize(1)
        ));
        
        $this->addElement('hidden', 'last_user_id', array(
            'value' => CURRENT_USER_ID,
            'decorators' => $this->setColSize(1)
        ));
        
        $this->addElement('hidden', 'locked', array(
            'value' => CURRENT_USER_ID,
            'decorators' => $this->setColSize(1)
        ));
        
        $this->addElement('hidden', 'locked_by', array(
            'value' => CURRENT_USER_ID,
            'decorators' => $this->setColSize(1)
        ));
        
        $this->addElement('submit','Enviar',array(
            'label'  =>  'Enviar',
            'ignore' => 'true',
            'class' => 'btn btn-success pull-right',
            'decorators' => $this->setColSize(12,false)
        ));
        
        $this->setDecorators(array(            
            'FormElements',
            array(array('in' => 'HtmlTag') , array('tag' => 'div', 'class' => 'row')),
            'Form',            
            array('HtmlTag', array('tag' => 'div', 'class' => 'panel panel-body panel-default'))
        ));
        
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'empreendimento');
        $this->setMethod('post');
    }
    
    private function setColSize($size = 12, $label = true, $addon = false)
    {
        $decorator = array(
            'Label',
            'ViewHelper',
            'Errors',
            'Description',            
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-group col-xs-'. $size .'')),
            array('Description', array('escape'=>false, 'tag'=>'span', 'class' => "input-group-addon"))
            
        );
        
        if(!$label){
            unset($decorator['0']);
        }
        
        if(!$addon){
            unset($decorator['5']);
        }
        
        return $decorator;
    }
    
    public function addFieldId($id) {
        $this->addElement('hidden', 'id', array(
            'value' => $id,
            'decorators' => $this->setColSize(12)
        ));
    }

}