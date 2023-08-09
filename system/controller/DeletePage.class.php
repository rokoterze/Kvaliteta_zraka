<?php
include('AbstractPage.class.php');
include('system/util/DatabaseHelper.class.php');

class DeletePage extends AbstractPage{
    public $templateName = 'delete';
    public function execute(){
       $this->functionSelector();
    }
    private function functionSelector(){
        $method = $_GET['method'] ?? '';
        if($method == null){return $this->data = "No method defined!";}
        
        else if($method == 'deleteStation'){
            $this->deleteStationByKey();
        }
        else if($method == 'deletePollutant'){
            $this->deletePollutantByKey();
        }

        else return $this->data = "Incorrect method defined!";
    }

    private function deleteStationByKey(){ //prenamjenit u delete!
        //API id: 17
        //API deletes an existing station by providing a key.
        $station_key = $_GET['stationKey'] ?? '';
        $method = $_GET['method'] ?? '';
        $token = $_GET['token'] ?? '';

        $database_table = "postaje";

        $time = date("d.m.Y H:i:s");
        $time_unix = DatabaseHelper::ConvertToUnix($time);

        if($station_key == null){return $this->data = "No station key defined!";}

        if(DatabaseHelper::tokenValidation($token, $time_unix) == false){
            $this->data = "Token is not valid, please create a new one.";
        }

        else if(DatabaseHelper::checkStationByKey($station_key) == false){
            $this->data = "Station key does not exist in database!";
        }

        else {
            $sql = "DELETE FROM `$database_table` 
            WHERE `postajaKey` = '$station_key'";

            try {
                AppCore::getDB()->sendQuery($sql);
            } 
            catch (DatabaseException $e) {
                $e->show();
            }
            
            DatabaseHelper::createActivityLog($method, $database_table, $token);
            $this->data = "Station successfully deleted!";
        }
    } 
    private function deletePollutantByKey(){ 
        //API id: 18
        //API deletes an existing pollutant by providing a key.
        $pollutant_key = $_GET['pollutantKey'] ?? '';
        $method = $_GET['method'] ?? '';
        $token = $_GET['token'] ?? '';

        $database_table = "polutanti";

        $time = date("d.m.Y H:i:s");
        $time_unix = DatabaseHelper::ConvertToUnix($time);

        if($pollutant_key == null){return $this->data = "No pollutant key defined!";}

        if(DatabaseHelper::tokenValidation($token, $time_unix) == false){
            $this->data = "Token is not valid, please create a new one.";
        }

        else if(DatabaseHelper::checkPollutantByKey($pollutant_key) == false){
            $this->data = "Pollutant key does not exist in database!";
        }

        else {
            $sql = "DELETE FROM `$database_table` 
            WHERE `polutantKey` = '$pollutant_key'";

            try {
                AppCore::getDB()->sendQuery($sql);
            } 
            catch (DatabaseException $e) {
                $e->show();
            }
            
            DatabaseHelper::createActivityLog($method, $database_table, $token);
            $this->data = "Pollutant successfully deleted!";
        }
    } 
}