<?php
include('AbstractPage.class.php');
include('system/util/DatabaseHelper.class.php');

class UpdatePage extends AbstractPage{
    public $templateName = 'update';
    public function execute(){
       $this->functionSelector();
    }
    private function functionSelector(){
        $method = $_GET['method'] ?? '';
        if($method == null){return $this->data = "No method defined!";}
        
        else if($method == 'updateStation'){
            $this->updateStation();
        }
        else if($method == 'updatePollutant'){
            $this->updatePollutant();
        }
        else if($method == 'updateStationKey'){
            $this->updateStationKey();
        }
        else if($method == 'updatePollutantKey'){
            $this->updatePollutantKey();
        }

        else return $this->data = "Incorrect method defined!";
    }

    private function updateStation(){
        //API id: 13
        //API updates an existing stations name by providing correct station key.
        $new_station_name = $_GET['newStationName'] ?? '';
        $station_key = $_GET['stationKey'] ?? '';
        $method = $_GET['method'] ?? '';
        $token = $_GET['token'] ?? '';

        $database_table = "postaje";

        $time = date("d.m.Y H:i:s");
        $time_unix = DatabaseHelper::ConvertToUnix($time);

        if($new_station_name == null){return $this->data = "No new station name defined!";}
        if($station_key == null){return $this->data = "No station key defined!";}

        if(DatabaseHelper::tokenValidation($token, $time_unix) == false){
            $this->data = "Token is not valid, please create a new one.";
        }

        else if(DatabaseHelper::checkStationByKey($station_key) == false){
            $this->data = "Station key does not exist in database!";
        }

        else {
            $sql = "UPDATE `$database_table` SET `postajaNaziv` = '$new_station_name' 
            WHERE `postajaKey` = '$station_key'";

            try {
                AppCore::getDB()->sendQuery($sql);
            } 
            catch (DatabaseException $e) {
                $e->show();
            }
            
            DatabaseHelper::createActivityLog($method, $database_table, $token);
            $this->data = "Station name successfully updated!";
        }
    } 
    private function updatePollutant(){
        //API id: 14
        //API updates an existing pollutant name by providing correct pollutant key.
        $new_pollutant_name = $_GET['newPollutantName'] ?? '';
        $pollutant_key = $_GET['pollutantKey'] ?? '';
        $method = $_GET['method'] ?? '';
        $token = $_GET['token'] ?? '';

        $database_table = "polutanti";

        $time = date("d.m.Y H:i:s");
        $time_unix = DatabaseHelper::ConvertToUnix($time);

        if($new_pollutant_name == null){return $this->data = "No new pollutant name defined!";}
        if($pollutant_key == null){return $this->data = "No pollutant key defined!";}

        if(DatabaseHelper::tokenValidation($token, $time_unix) == false){
            $this->data = "Token is not valid, please create a new one.";
        }

        else if(DatabaseHelper::checkPollutantByKey($pollutant_key) == false){
            $this->data = "Pollutant key does not exist in database!";
        }

        else {
            $sql = "UPDATE `$database_table` SET `polutantNaziv` = '$new_pollutant_name' 
            WHERE `polutantKey` = '$pollutant_key'";

            try {
                AppCore::getDB()->sendQuery($sql);
            } 
            catch (DatabaseException $e) {
                $e->show();
            }
            
            DatabaseHelper::createActivityLog($method, $database_table, $token);
            $this->data = "Pollutant name successfully updated!";
        }
    } 
    private function updateStationKey(){
        //API id: 15
        //API updates an existing stations key by providing a new key
        $new_station_key = $_GET['newStationKey'] ?? '';
        $old_station_key = $_GET['stationKey'] ?? '';
        $method = $_GET['method'] ?? '';
        $token = $_GET['token'] ?? '';

        $database_table = "postaje";

        $time = date("d.m.Y H:i:s");
        $time_unix = DatabaseHelper::ConvertToUnix($time);

        if($new_station_key == null){return $this->data = "No new station key defined!";}
        if($old_station_key == null){return $this->data = "No old station key defined!";}

        if(DatabaseHelper::tokenValidation($token, $time_unix) == false){
            $this->data = "Token is not valid, please create a new one.";
        }

        else if(DatabaseHelper::checkStationByKey($old_station_key) == false){
            $this->data = "Station key does not exist in database!";
        }

        else if(DatabaseHelper::checkStationByKey($new_station_key) == true){
            $this->data = "Station with new key value already exist in database!";
        }

        else {
            $sql = "UPDATE `$database_table` SET `postajaKey` = '$new_station_key' 
            WHERE `postajaKey` = '$old_station_key'";

            try {
                AppCore::getDB()->sendQuery($sql);
            } 
            catch (DatabaseException $e) {
                $e->show();
            }
            
            DatabaseHelper::createActivityLog($method, $database_table, $token);
            $this->data = "Station key successfully updated!";
        }
        
    } 
    private function updatePollutantKey(){
        //API id: 16
        //API updates an existing pollutant key by providing a new key.
        $new_pollutant_key = $_GET['newPollutantKey'] ?? '';
        $old_pollutant_key = $_GET['pollutantKey'] ?? '';
        $method = $_GET['method'] ?? '';
        $token = $_GET['token'] ?? '';

        $database_table = "polutanti";

        $time = date("d.m.Y H:i:s");
        $time_unix = DatabaseHelper::ConvertToUnix($time);

        if($new_pollutant_key == null){return $this->data = "No new pollutant key defined!";}
        if($old_pollutant_key == null){return $this->data = "No old pollutant key defined!";}

        if(DatabaseHelper::tokenValidation($token, $time_unix) == false){
            $this->data = "Token is not valid, please create a new one.";
        }

        else if(DatabaseHelper::checkPollutantByKey($old_pollutant_key) == false){
            $this->data = "Pollutant key does not exist in database!";
        }

        else if(DatabaseHelper::checkPollutantByKey($new_pollutant_key) == true){
            $this->data = "Pollutant with new key value already exist in database!";
        }

        else {
            $sql = "UPDATE `$database_table` SET `polutantKey` = '$new_pollutant_key' 
            WHERE `polutantKey` = '$old_pollutant_key'";

            try {
                AppCore::getDB()->sendQuery($sql);
            } 
            catch (DatabaseException $e) {
                $e->show();
            }
            
            DatabaseHelper::createActivityLog($method, $database_table, $token);
            $this->data = "Pollutant key successfully updated!";
        }
    } 

}