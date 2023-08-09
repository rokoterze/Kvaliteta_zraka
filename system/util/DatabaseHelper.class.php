<?php
class DatabaseHelper{
    public static function insertFetchData($postaja_key, $polutant_key, $tablica_value, $data) //Universal function for data insert which automatically selects table from database by $tablica_value
    {
        foreach ($data as $single_data) {

            $timeConvert = self::ConvertFromUnix($single_data->vrijeme); //Convert UNIX time to standard time format, to make work with database data easier.

            $sql = "INSERT INTO `$tablica_value`(`postajaKey`, `polutantKey`, `vrijednost`, `mjernaJedinica`, `vrijemeUnix`, `vrijeme`) 
                    VALUES ('$postaja_key','$polutant_key','$single_data->vrijednost','$single_data->mjernaJedinica','$single_data->vrijeme', '$timeConvert')";

            try {
                AppCore::getDB()->sendQuery($sql);
            }
            catch (DatabaseException $e) {
                $e->show();
            }
        }
    }

    public static function checkLogForDailyFetch($postaja_key, $polutant_key, $tablica_value){ //This function checks the database to see if there is already a fetch request for today.
        $todays_date = date("d.m.Y");

        $sql = "SELECT * FROM `daily_fetch_log` 
                WHERE `date` = '$todays_date' AND `postajaKey` = '$postaja_key' AND `polutantKey` = '$polutant_key' AND `tableName` = '$tablica_value'";
        $check = AppCore::getDB()->sendQuery($sql);

        try {
            $check = AppCore::getDB()->sendQuery($sql);

            if($check->num_rows > 0){
                return true;
            }
            else{
                return false;
            };
        } 
        catch (DatabaseException $e){
            $e->show();
        }
}
    public static function createLogForDailyFetch($postaja_key, $polutant_key, $tablica_value){ //This function creates a fetch request log for today.
        $todays_date = date("d.m.Y");
        $time_now = date("H:i:s");

        $sql = "INSERT INTO `daily_fetch_log`(`postajaKey`, `polutantKey`, `tableName`, `date`, `time`) 
        VALUES ('$postaja_key','$polutant_key','$tablica_value', '$todays_date','$time_now')";

        try {
            AppCore::getDB()->sendQuery($sql);
        } 
        catch (DatabaseException $e) {
            $e->show();
        }
    }

    protected static function ConvertFromUnix($unix){
        $unix = $unix/1000; //On site where api is fetched, unix time is display in ms format, to convert it properly ms needs to be removed.
        return date("d.m.Y\ H:i", $unix);
    }

    public static function ConvertToUnix($date){
        return strtotime($date);
    }
    
    public static function convertToJSONFormat($data){
        $counter = 0;
        $empty_array = "[]";
        $array = array();
            while($row = mysqli_fetch_assoc($data)){
                $array[] = $row;
                $counter++;
            }

            if($counter > 0){
                return json_encode($array, JSON_PRETTY_PRINT);
            }

        else return $empty_array;
    }

    public static function tokenGenerator($name, $surname){ //Generates unique token with small chance to make a duplicate one with valid period.
        $random_number = rand(10000,99999);
        $token = ucwords($name[0]).$random_number.ucwords($surname[0]);
        return $token;
    }

    public static function tokenValidation($token, $time_unix){ 
        $sql = "SELECT * FROM `korisnik` 
        WHERE `token` = '$token' AND `validUnix` > '$time_unix'";
        
        AppCore::getDB()->sendQuery($sql);

        try {
            $result = AppCore::getDB()->sendQuery($sql);
        } 
        catch (DatabaseException $e) {
            $e->show();
        }

        if(MySQLIDatabase::numberOfRows($result) == 0){
            return false;
        }

        else return true; //Returns true if token is valid.
    }

    public static function checkUser($name, $surname, $time_unix){
        $sql = "SELECT * FROM `korisnik` 
        WHERE `korisnikIme` = '$name' AND `korisnikPrezime` = '$surname' AND `validUnix` > '$time_unix' ";                 
              
        try {
            $result = AppCore::getDB()->sendQuery($sql);
        } 
        catch (DatabaseException $e) {
            $e->show();
        }

        if(MySQLIDatabase::numberOfRows($result) == 0){
            return false;
        }

        else return true; //Returns true if user exists with valid token.
    }

    public static function createActivityLog($method, $table_name, $token){ //Creates activity log for every create, update and delete operation.
        $todays_date = date("d.m.Y");
        $time_now = date("H:i:s");

        $sql = "INSERT INTO `log`(`method`, `tableName`, `token`, `date`, `time`) 
        VALUES ('$method','$table_name','$token','$todays_date','$time_now')";

        try {
            AppCore::getDB()->sendQuery($sql);
        } 
        catch (DatabaseException $e) {
            $e->show();
        }
    }

    public static function checkStation($station_key, $station_name){
        $sql = "SELECT * FROM `postaje` WHERE `postajaKey` = '$station_key' OR `postajaNaziv` = '$station_name'";
        
        try {
            $result = AppCore::getDB()->sendQuery($sql);
        } 
        catch (DatabaseException $e) {
            $e->show();
        }

        if(MySQLIDatabase::numberOfRows($result) == 0){
            return false;
        }

        else return true; //Returns true if station already exist in database with given parameters. 
    }
    public static function checkStationByKey($station_key){
        $sql = "SELECT * FROM `postaje` WHERE `postajaKey` = '$station_key'";
        
        try {
            $result = AppCore::getDB()->sendQuery($sql);
        } 
        catch (DatabaseException $e) {
            $e->show();
        }

        if(MySQLIDatabase::numberOfRows($result) == 0){
            return false;
        }

        else return true; //Returns true if station already exist in database with given key. 
    }

    public static function checkPollutant($pollutant_key, $pollutant_name){
        $sql = "SELECT * FROM `polutanti` WHERE `polutantKey` = '$pollutant_key' OR `polutantNaziv` = '$pollutant_name'";
        
        try {
            $result = AppCore::getDB()->sendQuery($sql);
        } 
        catch (DatabaseException $e) {
            $e->show();
        }

        if(MySQLIDatabase::numberOfRows($result) == 0){
            return false;
        }

        else return true; //Returns true if pollutant already exist in database with given parameters. 
    }

    public static function checkPollutantByKey($pollutant_key){
        $sql = "SELECT * FROM `polutanti` WHERE `polutantKey` = '$pollutant_key'";
        
        try {
            $result = AppCore::getDB()->sendQuery($sql);
        } 
        catch (DatabaseException $e) {
            $e->show();
        }

        if(MySQLIDatabase::numberOfRows($result) == 0){
            return false;
        }

        else return true; //Returns true if station already exist in database with given key. 
    }

    public static function getStations(){ //This function return associative array of all stations from database
        $sql = "SELECT * FROM `postaje`;";
        $stations = array();
        try {
            $result = AppCore::getDB()->sendQuery($sql);
        } 
        catch (DatabaseException $e) {
            $e->show();
        }
        while($row = AppCore::getDB()->fetchArray($result)){
            $stations[$row['postajaKey']] = $row['postajaNaziv'];
        }
        return $stations;
    }
    
    public static function getPollutants(){//This function return associative array of all pollutants from database
        $sql = "SELECT * FROM `polutanti`;";
        $pollutants = array();
        try {
            $result = AppCore::getDB()->sendQuery($sql);
        } 
        catch (DatabaseException $e) {
            $e->show();
        }
        while($row = AppCore::getDB()->fetchArray($result)){
            $pollutants[$row['polutantKey']] = $row['polutantNaziv'];
        }
        return $pollutants;
    }
    public static function getDataTypes(){//This function return associative array of all data types from database
        $sql = "SELECT * FROM `tipovi_podataka`;";
        $dataType = array();
        try {
            $result = AppCore::getDB()->sendQuery($sql);
        } 
        catch (DatabaseException $e) {
            $e->show();
        }
        while($row = AppCore::getDB()->fetchArray($result)){
            $dataType[$row['podatakKey']] = $row['podatakNaziv'];
        }
        return $dataType;
    }

    // public static function getAveragePollutantValue($station_key, $start_date_UNIX, $end_date_UNIX){
    //     $sql = "SELECT * FROM dnevni_podaci 
    //             WHERE postajaKey = '$station_key'
    //             AND vrijemeUnix >= '$start_date_UNIX'
    //             AND vrijemeUnix <= '$end_date_UNIX'
    //             ORDER BY ID ASC";
    //     $dataType = array();
    //     try {
    //         $result = AppCore::getDB()->sendQuery($sql);
    //     } 
    //     catch (DatabaseException $e) {
    //         $e->show();
    //     }
    //     while($row = AppCore::getDB()->fetchArray($result)){
    //         $dataType[$row['polutantKey']] = $row['polutantKey'];
    //     }
    //     return $dataType;
    // }

}
