<?php

class Application_Model_Login
{
    public static function login($login,$senha)
    {
        $model = new self;
        
        // Estancia a conexão com o banco de dados
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // Estancia o Zend_Auth para indica em qual tabela e quais campos fazer a verificação
        $adapter = new Zend_Auth_Adapter_DbTable($db);
        $adapter->setTableName('zf_usuarios')
                ->setIdentityColumn('email')
                ->setCredentialColumn('password')
                /*->setCredentialTreatment('SHA1(CONCAT(?,salt))') */;
        
        // Atribuindo campo extra para a verificação
        $select = $adapter->getDbSelect();
        $select->where('acesso = 1');
        
        $adapter->setIdentity($login);
        $adapter->setCredential($senha);
        
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);        
        
        
        if($result->isValid()){
            // Gravando dados na sessão
            $contents = $adapter->getResultRowObject(null,'password');
            $auth->getStorage()->write($contents);
            return true;
        }else{
            return $model->getMessages($result);
        }
    }
    
    private function getMessages(Zend_Auth_Result $result){
        switch ($result->getCode())
        {
            case $result::FAILURE_IDENTITY_NOT_FOUND:
                $msg = "Login não encontrado";
                break;
                
            case $result::FAILURE_IDENTITY_AMBIGUOUS:
                $msg = "Login em duplicidade";
                break;
                
            case $result::FAILURE_CREDENTIAL_INVALID:
                $msg = "Senha inválida";
                break;
                
            default:
                $msg = "Login/senha inválidos";                
        }
        
        return $msg;
    }

}

