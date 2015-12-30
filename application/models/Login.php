<?php

class Application_Model_Login
{
    protected $name = 'zf_usuarios';
    protected $perilName = 'zf_perfis';
    
    public static function login($login,$senha)
    {
        $model = new self;
        
        // Estancia a conexão com o banco de dados
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // Estancia o Zend_Auth para indica em qual tabela e quais campos fazer a verificação
        $adapter = new Zend_Auth_Adapter_DbTable($db);
        $adapter->setTableName($model->name)
                ->setIdentityColumn('email')
                ->setCredentialColumn('password')
                ->setCredentialTreatment('SHA1(CONCAT(?,salt))');
        
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
            $contents->childrens_ids = array();
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
            $result = $db->fetchRow('SELECT role FROM '. $model->perilName .' WHERE id = ?', $contents->id_perfil);
            $userchildrens = $db->fetchCol('SELECT id FROM '. $model->name .' WHERE parent_id = ?', $contents->id);
            
            if($userchildrens){
                $contents->childrens_ids = $userchildrens;
            }
            
            $contents = (object) array_merge((array) $contents,(array)$result);
            
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

