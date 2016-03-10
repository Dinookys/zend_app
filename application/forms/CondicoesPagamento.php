<?php

class Application_Form_CondicoesPagamento extends Zend_Form
{

    public function init()
    {
        // Verifica se o campo select esta com o valor null
        $required = new Zend_Validate_NotEmpty();
        $required->setType($required->getType() | Zend_Validate_NotEmpty::STRING | Zend_Validate_NotEmpty::ZERO);        
        $data_now = new Zend_Date();
        
        $this->addElement('hidden','parcelas', array(
            'Label' => '',
            'required' => true,           
            'filters'   =>  array('StringTrim'),
            'class'     => 'form-control',
            'decorators' => $this->setColSize(12)
        ));

        $this->addElement('hidden', 'id_proposta', array(
            'value' => '',
            'decorators' => $this->setColSize(12)
        ));
        
        $this->addElement('hidden', 'last_user_id', array(
            'value' => CURRENT_USER_ID,
            'decorators' => $this->setColSize(12)
        ));
        
        $this->setDecorators(array(
            'FormElements',
            array(
                array(
                    'in' => 'HtmlTag'
                ),
                array(
                    'tag' => 'div',
                    'class' => 'row'
                )
            ),
            'Form',
            array(
                'HtmlTag',
                array(
                    'tag' => 'div',
                    'class' => 'panel panel-body panel-default'
                )
            )
        ));
        
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'condicoes-pagamento');
        $this->setMethod('post');

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

}

