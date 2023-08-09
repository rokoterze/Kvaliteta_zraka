<?php
include('AbstractPage.class.php');
include('system/util/DatabaseHelper.class.php');

class CreatePage extends AbstractPage{
    public $templateName = 'create';

    public function execute(){
       $this->functionSelector();
    }

    private function functionSelector(){
        $method = $_GET['method'] ?? '';
        if($method == null){return $this->data = "No method defined!";}
        
        else if($method == 'createStation'){
            $this->createStation();
        }
        else if($method == 'createPollutant'){
            $this->createPollutant();
        }
        else if($method == 'user'){
            $this->createUser();
        }

        else return $this->data = "Incorrect method defined!";
    }
    private function createUser(){
        //API id: 12
        //API creates a new user with unique token with 5 minutes valid time.
        $name = $_GET['name'] ?? '';
        $surname = $_GET['surname'] ?? '';
        $method = $_GET['method'] ?? '';

        $database_table = "korisnik";
        
        $time = date("d.m.Y H:i:s");
        $time_unix = DatabaseHelper::ConvertToUnix($time);

        $valid = date("d.m.Y H:i:s", strtotime("+5 minutes"));
        $valid_unix = DatabaseHelper::ConvertToUnix($valid);

        if($name == null || $surname == null){return $this->data = "No name or surname defined!";}
       
        if(DatabaseHelper::checkUser($name, $surname, $time_unix) == false){
            $token = DatabaseHelper::tokenGenerator($name, $surname);

            $sql = "INSERT INTO `korisnik`(`korisnikIme`, `korisnikPrezime`, `token`, `vrijeme`, `valid`, `validUnix`)
            VALUES ('$name', '$surname', '$token', '$time', '$valid', '$valid_unix')";
         
            AppCore::getDB()->sendQuery($sql);
            DatabaseHelper::createActivityLog($method, $database_table, $token);

            return $this->data = "Name: ".$name."<br>Surname: ".$surname."<br>Token: ".$token."<br>Valid until: ".$valid;
        }
        else return $this->data = "User is already created!";
    }

    private function createStation(){
        //API id: 10
        //API creates a new station.
        $station_name = $_GET['stationName'] ?? '';
        $station_key = $_GET['stationKey'] ?? '';
        $method = $_GET['method'] ?? '';
        $token = $_GET['token'] ?? '';
        
        $database_table = "postaje";

        $time = date("d.m.Y H:i:s");
        $time_unix = DatabaseHelper::ConvertToUnix($time);

        if($station_name == null){return $this->data = "No station name defined!";}
        if($station_key == null){return $this->data = "No station key defined!";}

        if(DatabaseHelper::tokenValidation($token, $time_unix) == false){
            $this->data = "Token is not valid, please create a new one.";
        }

        else if(DatabaseHelper::checkStation($station_key, $station_name) == true){
            $this->data = "Station already exists in database!";
        }

        else {
            $sql = "INSERT INTO `$database_table`(`postajaKey`, `postajaNaziv`) 
            VALUES ('$station_key','$station_name') ";

            try {
                AppCore::getDB()->sendQuery($sql);
            } 
            catch (DatabaseException $e) {
                $e->show();
            }
            
            DatabaseHelper::createActivityLog($method, $database_table, $token);
            $this->data = "Station succesfully created!";
        }
    }

    private function createPollutant(){
        //API id: 11
        //API creates a new pollutant.
        $pollutant_name = $_GET['pollutantName'] ?? '';
        $pollutant_key = $_GET['pollutantKey'] ?? '';
        $method = $_GET['method'] ?? '';
        $token = $_GET['token'] ?? '';
        
        $database_table = "polutanti";

        $time = date("d.m.Y H:i:s");
        $time_unix = DatabaseHelper::ConvertToUnix($time);

        if($pollutant_name == null){return $this->data = "No pollutant name defined!";}
        if($pollutant_key == null){return $this->data = "No pollutant key defined!";}
        
        if(DatabaseHelper::tokenValidation($token, $time_unix) == false){
            $this->data = "Token is not valid, please create a new one.";
        }

        else if(DatabaseHelper::checkPollutant($pollutant_key, $pollutant_name) == true){
            $this->data = "Pollutant already exists in database!";
        }

        else {
            $sql = "INSERT INTO `$database_table` (`polutantKey`, `polutantNaziv`) 
            VALUES ('$pollutant_key','$pollutant_name') ";

            try {
                AppCore::getDB()->sendQuery($sql);
            } 
            catch (DatabaseException $e) {
                $e->show();
            }
            
            DatabaseHelper::createActivityLog($method, $database_table, $token);
            $this->data = "Pollutant succesfully created!";
        }
    }

    
}