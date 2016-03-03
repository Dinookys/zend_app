<?php

class Application_Form_Financeiro extends Zend_Form
{

    public function init()
    {
        
        $this->addElement('hidden', 'sinal', array(
            'value' => '',
            'decorators' => $this->setColSize(12, false)
        ));
        
        $this->addElement('hidden', 'liberar', array(
            'value' => '0',
            'decorators' => $this->setColSize(1, false)
        ));
        
        $this->addElement('hidden', 'restante', array(
            'value' => '',
            'decorators' => $this->setColSize(12, false)
        ));
        
        $this->addElement('hidden', 'recebido', array(
            'value' => '',
            'decorators' => $this->setColSize(12, false)
        ));        
        
        $this->addElement('hidden', 'nome', array(
            'value' => '',
            'decorators' => $this->setColSize(12, false)
        ));
        
        $this->addElement('hidden', 'parcelas', array(
            'value' => '',
            'decorators' => $this->setColSize(12, false)
        ));

        $this->addElement('hidden', 'comissao', array(
            'value' => '',
            'decorators' => $this->setColSize(12, false)
        ));
        
        $this->addElement('hidden', 'parcelas_pagas', array(
            'value' => '',
            'decorators' => $this->setColSize(12, false)
        ));
        
        $this->addElement('hidden', 'id_cliente', array(
            'value' => '',
            'decorators' => $this->setColSize(1, false)
        ));
        
        $this->addElement('hidden', 'last_user_id', array(
            'value' => '',
            'decorators' => $this->setColSize(1, false)
        ));
        
        $this->addElement('hidden', 'locked', array(
            'value' => CURRENT_USER_ID,
            'decorators' => $this->setColSize(1, false)
        ));
        
        $this->addElement('hidden', 'locked_by', array(
            'value' => CURRENT_USER_ID,
            'decorators' => $this->setColSize(1, false)
        ));
        
        $this->addElement('submit', 'Enviar', array(
            'label' => 'Enviar',
            'ignore' => 'true',
            'class' => 'btn btn-success pull-right',
            'decorators' => $this->setColSize(1, false)
        ));
    }

    private function setColSize($size = 12, $label = true, $addon = false)
    {
        $decorator = array(
            'Label',
            'ViewHelper',
            'Errors',
            'Description',
            array(
                'HtmlTag',
                array(
                    'tag' => 'div',
                    'class' => 'form-group col-xs-' . $size . ''
                )
            ),
            array(
                'Description',
                array(
                    'escape' => false,
                    'tag' => 'span',
                    'class' => "input-group-addon"
                )
            )
        );
    
        if (! $label) {
            unset($decorator['0']);
        }
    
        if (! $addon) {
            unset($decorator['5']);
        }
    
        return $decorator;
    }
    
    public function addFieldId($id)
    {
        $this->addElement('hidden', 'id', array(
            'value' => $id,
            'decorators' => $this->setColSize(12)
        ));
    }
}

