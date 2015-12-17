<?php

class Application_Form_FichaAtendimento extends Zend_Form
{

    public function init()
    {
        // Verifica se o campo select esta com o valor null   
        $required = new Zend_Validate_NotEmpty ();
        $required->setType ($required->getType() | Zend_Validate_NotEmpty::STRING | Zend_Validate_NotEmpty::ZERO);
        
        
        $this->setElementDecorators($this->setColSize());
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            'Description',
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-group col-xs-12'))
        ), array('Enviar', 'id'), true);
        
        $options_estado_civil = array(
            '-- Selecione --', 
            'casado' => 'Casado',
            'solteiro' => 'Solteiro',
            'divorciado' => 'Divorciado',
            'viuvo' => 'Viuvo'
        );
        
        $options_sim_nao = array('Não','Sim');
        
        $this->addElement('text','nome',array(
            'label'  =>  'Nome',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('select','estado_civil',array(
            'label'  =>  'Estado Civil',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'multiOptions' => $options_estado_civil,
            'validators' => array($required),
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text','endereco',array(
            'label'  =>  'Endereço',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control'
        ));
        
        $this->addElement('text','bairro',array(
            'label'  =>  'Bairro',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('select','estado',array(
            'label'  =>  'Estado',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'multiOptions' => $options_estado_civil,
            'validators' => array($required),
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('select','cidade',array(
            'label'  =>  'Cidade',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'multiOptions' => $options_estado_civil,
            'validators' => array($required)
            ,'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text','cep',array(
            'label'  =>  'CEP',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text','fone_resid',array(
            'label'  =>  'Fone Resid.',
            'required'  =>  false,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(4)
        ));
        
        $this->addElement('text','fone_com',array(
            'label'  =>  'Fone Com.',
            'required'  =>  false,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(4)
        ));
        
        $this->addElement('text','fone_cel',array(
            'label'  =>  'Celular',
            'required'  =>  false,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(4)
        ));
        
        $this->addElement('text','email',array(
            'label'  =>  'E-mail',
            'required'  =>  false,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text','data_nasc',array(
            'label'  =>  'Dt de Nascimento',
            'required'  =>  false,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text','empresa_trabalha',array(
            'label'  =>  'Empresa na qual trabalha',
            'required'  =>  false,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            
        ));

        $this->addElement('text','profissao',array(
            'label'  =>  'Profissão',
            'required'  =>  false,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text','cargo',array(
            'label'  =>  'Cargo',
            'required'  =>  false,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text','renda_familiar',array(
            'label'  =>  'Renda Familiar',
            'required'  =>  false,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('select','renda',array(
            'label'  =>  'Renda',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'multiOptions' => $options_estado_civil,
            'validators' => array($required),
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('select','fgts',array(
            'label'  =>  'FGTS',            
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'multiOptions' => $options_sim_nao,
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('select','fgts_tres_anos',array(
            'label'  =>  'Mais de 3 anos',            
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'multiOptions' => $options_sim_nao,
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('text','saldo_fgts',array(
            'label'  =>  'Saldo FGTS',
            'required'  =>  false,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('text','valor_entrada',array(
            'label'  =>  'Valor de entrada',
            'required'  =>  false,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(3)
        ));        
        
        $this->addElement('select','meio_comunicacao',array(
            'label'  =>  'Meio de Comunicação',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'multiOptions' => $options_estado_civil,
            'validators' => array($required)
        ));
        
        $this->addElement('textarea','observacoes',array(
            'label'  =>  'Observações',
            'required'  =>  true,
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'cols'  => 80,
            'rows'  => 5
        ));
        
        $this->addElement('submit','Enviar',array(
            'label'  =>  'Enviar',
            'class' => 'btn btn-success pull-right'
        ));
        
        $this->setDecorators(array(            
            'FormElements',
            array(array('in' => 'HtmlTag') , array('tag' => 'div', 'class' => 'row')),
            'Form',            
            array('HtmlTag', array('tag' => 'div', 'class' => 'panel panel-body panel-default'))
        ));
        
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'ficha-atendimento');
        $this->setMethod('post');        
        
    }
    
    private function setColSize($size = 12)
    {
        return array(
            'Label',
            'ViewHelper',
            'Errors',
            'Description',
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-group col-xs-'. $size .''))
        );
    }

}

