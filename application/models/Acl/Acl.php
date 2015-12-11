<?php
class Application_Model_Acl_Acl extends Zend_Acl{
    
    function __construct(){
         $db = Zend_Db_Table::getDefaultAdapter();
         $db->setFetchMode(Zend_Db::FETCH_OBJ);
         $result = $db->fetchAll('SELECT * FROM zf_perfis WHERE 1');
         print_r($result);
    }    
}