<?php
require('core.functions.php');
require('util/RequestHandler.class.php');

class AppCore{   

    protected static $dbObj;
   
    public function __construct(){
        $this->initDB();
        $this->initOptions();
        $this->autoLoad();
    }

    public static function handleException($e){
        $e->show();
    }
    protected function initDB(){
        require_once('config.inc.php');
        require_once('model/MySQLiDatabase.class.php');

        self::$dbObj = new MySQLiDatabase($host, $user, $password, $database);
    }

    public static final function getDB(){
        return self::$dbObj;
    }
    
    public function initOptions(){
        require('options.inc.php');
    }

    public function autoLoad(){ 
        RequestHandler::handle();
    }
}