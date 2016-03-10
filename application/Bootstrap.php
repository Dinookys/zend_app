<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    public function __construct($application)
    {
        $identiy = Zend_Auth::getInstance()->getIdentity();
        
        if($identiy){
            define('CURRENT_USER_ID', $identiy->id);            
            define('CURRENT_USER_NAME', $identiy->nome);
            define('CURRENT_USER_EMAIL', $identiy->email);
            define('CURRENT_USER_ROLE', $identiy->role);
        }
        
        $locale = new Zend_Locale('pt_BR');
        Zend_Registry::set('Zend_Locale', $locale);        
        
        parent::__construct($application);
     
    }
    
    public function _initTranslate() {
        $translator = new Zend_Translate ( array ('adapter' => 'array', 'content' => '../resources/languages', 'locale' => 'pt_BR', 'scan' => Zend_Translate::LOCALE_DIRECTORY ) );
        Zend_Validate_Abstract::setDefaultTranslator ( $translator );
    }
    
}