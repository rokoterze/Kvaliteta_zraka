<?php
class DatabaseException extends Exception{
    public function show(){
        return (
        "<br>Error => " . $this->getMessage() . 
        "<br>File => " . $this->getFile() . 
        "<br>Line => " . $this->getLine() . 
        "<br>StackTrace => "  . $this->getTraceAsString()
        );
    }
}