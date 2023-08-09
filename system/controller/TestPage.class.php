<?php
include('AbstractPage.class.php');
include('system/util/DatabaseHelper.class.php');

class TestPage extends AbstractPage{ //This class is created only for testing output purposes on ?page=Test page.
    
    public $templateName = 'test';

    public function execute(){ 

    }
}
