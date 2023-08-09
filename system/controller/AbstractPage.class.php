<?php
abstract class AbstractPage{

    protected $data = []; 

    public function __construct(){
        $this->execute();
        $this->show();
    }


    public function show(){
        $templateName = $this->templateName;
        $data = $this->data;

        include_once('system/view/'.$templateName.'.tpl.php');
    }
}