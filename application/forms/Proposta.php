<?php

class Application_Form_Proposta extends Zend_Form
{

    public function init()
    {
        // Verifica se o campo select esta com o valor null
        $required = new Zend_Validate_NotEmpty();
        $required->setType($required->getType() | Zend_Validate_NotEmpty::STRING | Zend_Validate_NotEmpty::ZERO);
        
        $data_now = new Zend_Date();
        
        $options_estado_civil = array(
            '-- Selecione --',
            'casado' => 'Casado',
            'solteiro' => 'Solteiro',
            'divorciado' => 'Divorciado',
            'viuvo' => 'Viuvo'
        );
        
        $options_empty = array(
            '-- Selecione --'
        );
        
        $options_imovel = array(
            '-- Selecione --',
            'imovel01' => 'Imóvel 01',
            'imovel02' => 'Imóvel 02',
            'imovel03' => 'Imóvel 03'
        );
        
        $options_meio_comunicacao = array(
            '-- Selecione --',
            'imovel01' => 'Telefone',
            'tv' => 'TV',
            'local' => 'Pass. no Local',
            'radio' => 'Rádio',
            'faixas' => 'Faixas',
            'email' => 'Email',
            'panfletagem' => 'Panfletagem',
            'mala direta' => 'Mala direta',
            'indicação' => 'Indicação',
            'internet' => 'Internet',
            'Jornal' => 'Jornal',
            'outdoor' => 'Outdoor',
            'outros' => 'Outros'
        );
        
        $options_renda = array(
            '-- Selecione --',
            'formal' => 'Formal',
            'informal' => 'Informal',
            'mista' => 'Mista'
        );
        
        $options_sim_nao = array(
            'Não',
            'Sim'
        );
        
        // Adicionando tag HTML usando description do elemento
        $this->addElement('hidden', 'header3_imovel', array(
            'description' => '<h3>DADOS IMÓVEL</h3>',
            'ignore' => true,
            'decorators' => array(
                array(
                    'Description',
                    array(
                        'escape' => false,
                        'tag' => 'div',
                        'class' => "col-xs-12"
                    )
                )
            )
        ));
        
        $this->addElement('select', 'imovel', array(
            'label' => 'Imóvel',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_imovel,
            'validators' => array(
                $required
            ),
            'decorators' => $this->setColSize(4)
        ));
        
        $this->addElement('select', 'imovel_bloco_quadra', array(
            'label' => 'Bloco/Quadra',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_imovel,
            'validators' => array(
                $required
            ),
            'decorators' => $this->setColSize(4)
        ));
        
        $this->addElement('select', 'imovel_unidade', array(
            'label' => 'Unidade',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_imovel,
            'validators' => array(
                $required
            ),
            'decorators' => $this->setColSize(4)
        ));
        
        // Adicionando tag HTML usando description do elemento
        $this->addElement('hidden', 'header3', array(
            'description' => '<h3>DADOS CADASTRAIS</h3>',
            'ignore' => true,
            'decorators' => array(
                array(
                    'Description',
                    array(
                        'escape' => false,
                        'tag' => 'div',
                        'class' => "col-xs-12"
                    )
                )
            )
        ));
        
        $this->addElement('text', 'nome', array(
            'label' => 'Nome',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text', 'cpf', array(
            'label' => 'CPF',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('select', 'estado_civil', array(
            'label' => 'Estado Civil',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_estado_civil,
            'validators' => array(
                $required
            ),
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('text', 'email', array(
            'label' => 'E-mail',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(5)
        ));
        
        $this->addElement('text', 'data_nasc', array(
            'label' => 'Dt de Nascimento',
            'required' => false,
            'description' => '<span class="glyphicon glyphicon-calendar"></span>',
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(2, true, true)
        ));
        
        $this->addElement('text', 'rg', array(
            'label' => 'Identidade',
            'required' => false,
            'description' => '',
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('text', 'rg_orgao_expeditor', array(
            'label' => 'Orgão expedidor',
            'required' => false,
            'description' => '',
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(2)
        ));
        
        // Adicionando tag HTML usando description do elemento
        $this->addElement('hidden', 'hr_filiacao', array(
            'description' => '<h3>FILIAÇÃO</h3>',
            'ignore' => true,
            'decorators' => array(
                array(
                    'Description',
                    array(
                        'escape' => false,
                        'tag' => 'div',
                        'class' => "col-xs-12"
                    )
                )
            )
        ));
        
        $this->addElement('text', 'filiacao_pai', array(
            'label' => 'Pai',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text', 'filiacao_mae', array(
            'label' => 'Mãe',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        // Adicionando tag HTML usando description do elemento
        $this->addElement('hidden', 'hr_end', array(
            'description' => '<hr/>',
            'ignore' => true,
            'decorators' => array(
                array(
                    'Description',
                    array(
                        'escape' => false,
                        'tag' => 'div',
                        'class' => "col-xs-12"
                    )
                )
            )
        ));
        
        $this->addElement('text', 'cep', array(
            'label' => 'CEP',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('text', 'endereco', array(
            'label' => 'Endereço',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(9)
        ));
        
        $this->addElement('text', 'bairro', array(
            'label' => 'Bairro',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('select', 'estado', array(
            'label' => 'Estado',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_empty,
            'validators' => array(
                $required
            ),
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('select', 'cidade', array(
            'label' => 'Cidade',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_empty,
            'validators' => array(
                $required
            ),
            'decorators' => $this->setColSize(3)
        ));
        
        // Adicionando tag HTML usando description do elemento
        $this->addElement('hidden', 'hr_phones', array(
            'description' => '<hr/>',
            'ignore' => true,
            'decorators' => array(
                array(
                    'Description',
                    array(
                        'escape' => false,
                        'tag' => 'div',
                        'class' => "col-xs-12"
                    )
                )
            )
        ));
        
        $this->addElement('text', 'fone_resid', array(
            'label' => 'Fone Resid.',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(4)
        ));
        
        $this->addElement('text', 'fone_com', array(
            'label' => 'Fone Com.',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(4)
        ));
        
        $this->addElement('text', 'fone_cel', array(
            'label' => 'Celular',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(4)
        ));
        
        // Adicionando tag HTML usando description do elemento
        $this->addElement('hidden', 'hr_contato', array(
            'description' => '<hr/>',
            'ignore' => true,
            'decorators' => array(
                array(
                    'Description',
                    array(
                        'escape' => false,
                        'tag' => 'div',
                        'class' => "col-xs-12"
                    )
                )
            )
        ));
        
        $this->addElement('text', 'empresa_trabalha', array(
            'label' => 'Empresa na qual trabalha',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize()
        ));
        
        $this->addElement('text', 'profissao', array(
            'label' => 'Profissão',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text', 'cargo', array(
            'label' => 'Cargo',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text', 'renda_pessoal', array(
            'label' => 'Renda Pessoal',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('select', 'renda', array(
            'label' => 'Renda',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_renda,
            'validators' => array(
                $required
            ),
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('select', 'fgts', array(
            'label' => 'FGTS',
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_sim_nao,
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('select', 'fgts_tres_anos', array(
            'label' => 'Mais de 3 anos',
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_sim_nao,
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('text', 'saldo_fgts', array(
            'label' => 'Saldo FGTS',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(3)
        ));
        
        // Adicionando tag HTML usando description do elemento
        $this->addElement('hidden', 'hr_conj', array(
            'description' => '<hr/>',
            'ignore' => true,
            'decorators' => array(
                array(
                    'Description',
                    array(
                        'escape' => false,
                        'tag' => 'div',
                        'class' => "col-xs-12"
                    )
                )
            )
        ));
        
        // Adicionando tag HTML usando description do elemento
        $this->addElement('hidden', 'header3_conj', array(
            'description' => '<h3>CONJUGE OU SEGUNDO PROPONENTE</h3>',
            'ignore' => true,
            'decorators' => array(
                array(
                    'Description',
                    array(
                        'escape' => false,
                        'tag' => 'div',
                        'class' => "col-xs-12"
                    )
                )
            )
        ));
        
        $this->addElement('text', 'conj_nome', array(
            'label' => 'Nome',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text', 'conj_cpf', array(
            'label' => 'CPF',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('select', 'conj_estado_civil', array(
            'label' => 'Estado Civil',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_estado_civil,
            'validators' => array(
                $required
            ),
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('text', 'conj_email', array(
            'label' => 'E-mail',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(5)
        ));
        
        $this->addElement('text', 'conj_data_nasc', array(
            'label' => 'Dt de Nascimento',
            'required' => false,
            'description' => '<span class="glyphicon glyphicon-calendar"></span>',
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(2, true, true)
        ));
        
        $this->addElement('text', 'conj_rg', array(
            'label' => 'Identidade',
            'required' => false,
            'description' => '',
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('text', 'conj_rg_orgao_expeditor', array(
            'label' => 'Orgão expedidor',
            'required' => false,
            'description' => '',
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(2)
        ));
        
        // Adicionando tag HTML usando description do elemento
        $this->addElement('hidden', 'conj_hr_filiacao', array(
            'description' => '<h3>FILIAÇÃO</h3>',
            'ignore' => true,
            'decorators' => array(
                array(
                    'Description',
                    array(
                        'escape' => false,
                        'tag' => 'div',
                        'class' => "col-xs-12"
                    )
                )
            )
        ));
        
        $this->addElement('text', 'conj_filiacao_pai', array(
            'label' => 'Pai',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text', 'conj_filiacao_mae', array(
            'label' => 'Mãe',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        // Adicionando tag HTML usando description do elemento
        $this->addElement('hidden', 'conj_hr_end', array(
            'description' => '<hr/>',
            'ignore' => true,
            'decorators' => array(
                array(
                    'Description',
                    array(
                        'escape' => false,
                        'tag' => 'div',
                        'class' => "col-xs-12"
                    )
                )
            )
        ));
        
        $this->addElement('text', 'conj_cep', array(
            'label' => 'CEP',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('text', 'conj_endereco', array(
            'label' => 'Endereço',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(9)
        ));
        
        $this->addElement('text', 'conj_bairro', array(
            'label' => 'Bairro',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('select', 'conj_estado', array(
            'label' => 'Estado',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_empty,
            'validators' => array(
                $required
            ),
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('select', 'conj_cidade', array(
            'label' => 'Cidade',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_empty,
            'validators' => array(
                $required
            ),
            'decorators' => $this->setColSize(3)
        ));
        
        // Adicionando tag HTML usando description do elemento
        $this->addElement('hidden', 'hr_phones_conj', array(
            'description' => '<hr/>',
            'ignore' => true,
            'decorators' => array(
                array(
                    'Description',
                    array(
                        'escape' => false,
                        'tag' => 'div',
                        'class' => "col-xs-12"
                    )
                )
            )
        ));
        
        $this->addElement('text', 'conj_fone_resid', array(
            'label' => 'Fone Resid.',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(4)
        ));
        
        $this->addElement('text', 'conj_fone_com', array(
            'label' => 'Fone Com.',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(4)
        ));
        
        $this->addElement('text', 'conj_fone_cel', array(
            'label' => 'Celular',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(4)
        ));
        
        // Adicionando tag HTML usando description do elemento
        $this->addElement('hidden', 'conj_hr_contato', array(
            'description' => '<hr/>',
            'ignore' => true,
            'decorators' => array(
                array(
                    'Description',
                    array(
                        'escape' => false,
                        'tag' => 'div',
                        'class' => "col-xs-12"
                    )
                )
            )
        ));
        
        $this->addElement('text', 'conj_empresa_trabalha', array(
            'label' => 'Empresa na qual trabalha',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize()
        ));
        
        $this->addElement('text', 'conj_profissao', array(
            'label' => 'Profissão',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text', 'conj_cargo', array(
            'label' => 'Cargo',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('text', 'conj_renda_pessoal', array(
            'label' => 'Renda Pessoal',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('select', 'conj_renda', array(
            'label' => 'Renda',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_renda,
            'validators' => array(
                $required
            ),
            'decorators' => $this->setColSize(6)
        ));
        
        $this->addElement('select', 'conj_fgts', array(
            'label' => 'FGTS',
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_sim_nao,
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('select', 'conj_fgts_tres_anos', array(
            'label' => 'Mais de 3 anos',
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_sim_nao,
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('text', 'conj_saldo_fgts', array(
            'label' => 'Saldo FGTS',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(3)
        ));
        
        // Adicionando tag HTML usando description do elemento
        $this->addElement('hidden', 'separador_entrada', array(
            'description' => '<hr/>',
            'ignore' => true,
            'decorators' => array(
                array(
                    'Description',
                    array(
                        'escape' => false,
                        'tag' => 'div',
                        'class' => "col-xs-12"
                    )
                )
            )
        ));
        
        $this->addElement('text', 'valor_entrada', array(
            'label' => 'Valor de entrada',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(3)
        ));
        
        $this->addElement('text', 'renda_familiar', array(
            'label' => 'Renda Familiar',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'decorators' => $this->setColSize(3)
        ));
        
        // Adicionando tag HTML usando description do elemento
        $this->addElement('hidden', 'hr_atendimento', array(
            'description' => '<hr/>',
            'ignore' => true,
            'decorators' => array(
                array(
                    'Description',
                    array(
                        'escape' => false,
                        'tag' => 'div',
                        'class' => "col-xs-12"
                    )
                )
            )
        ));
        
        // Adicionando tag HTML usando description do elemento
        $this->addElement('hidden', 'header3_atendimento', array(
            'description' => '<h3>SOBRE O ATENDIMENTO</h3>',
            'ignore' => true,
            'decorators' => array(
                array(
                    'Description',
                    array(
                        'escape' => false,
                        'tag' => 'div',
                        'class' => "col-xs-12"
                    )
                )
            )
        ));
        
        $this->addElement('select', 'meio_comunicacao', array(
            'label' => 'Meio de Comunicação',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'multiOptions' => $options_meio_comunicacao,
            'validators' => array(
                $required
            ),
            'decorators' => $this->setColSize()
        ));
        
        $this->addElement('textarea', 'observacoes', array(
            'label' => 'Observações',
            'required' => false,
            'filters' => array(
                'StringTrim'
            ),
            'class' => 'form-control',
            'cols' => 80,
            'rows' => 5,
            'decorators' => $this->setColSize()
        ));
        
        $this->addElement('hidden', 'data_proposta', array(
            'value' => $data_now->toString('YYYY-MM-dd'),
            'decorators' => $this->setColSize(12)
        ));
        
        $this->addElement('hidden', 'data', array(
            'value' => $data_now->toString('YYYY-MM-dd'),
            'decorators' => $this->setColSize(12)
        ));
        
        $this->addElement('hidden', 'created_user_id', array(
            'value' => CURRENT_USER_ID,
            'decorators' => $this->setColSize(12)
        ));
        
        $this->addElement('hidden', 'id', array(
            'value' => '',
            'decorators' => $this->setColSize(12)
        ));
        
        $this->addElement('hidden', 'last_user_id', array(
            'value' => CURRENT_USER_ID,
            'decorators' => $this->setColSize(12)
        ));
        
        $this->addElement('hidden', 'locked', array(
            'value' => CURRENT_USER_ID,
            'decorators' => $this->setColSize(12)
        ));
        
        $this->addElement('hidden', 'locked_by', array(
            'value' => CURRENT_USER_ID,
            'decorators' => $this->setColSize(12)
        ));
        
        $this->addElement('submit', 'Enviar', array(
            'label' => 'Enviar',
            'ignore' => 'true',
            'class' => 'btn btn-success pull-right',
            'decorators' => $this->setColSize(12, false)
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
        $this->setAttrib('id', 'ficha-atendimento');
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

    public function addFieldId($id)
    {
        $this->addElement('hidden', 'id', array(
            'value' => $id,
            'decorators' => $this->setColSize(12)
        ));
    }
}

