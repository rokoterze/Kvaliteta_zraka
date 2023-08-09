<?php
include('AbstractPage.class.php');
include('system/util/DatabaseHelper.class.php');

class ReadPage extends AbstractPage{
    public $templateName = 'read';
    public function execute(){
        $this->getDataFromDatabase();
    }
    private function getDataFromDatabase(){ 
        //This is the main function which reads parameters from URL 
        $method = $_GET['method'] ?? '';
        $type = $_GET['type'] ?? '';
        $interval = $_GET['interval'] ?? '';
        $station_key = $_GET['station'] ?? '';
        $pollutant_key = $_GET['pollutant'] ?? '';

        $start_date = $_GET['start'] ?? '';
        $end_date = $_GET['end'] ?? '';

        if($station_key == null || $pollutant_key == null){return $this->data = "No station or pollutant defined!";}
        if($method == null){return $this->data = "No method defined!";}
        else switch($method){
            case 'get' : 
                if($interval == null){return $this->data = "No interval defined!";}
                else switch($interval){
                    case 'hourly' : //If it is hourly, it retrieves data from satni_podaci
                        if($type == null){return $this->data = "No type defined!";}
                        else if($type == 'latest'){
                            //API id: 1
                            //API returns JSON data for a specificed station and pollutant by latest recorded hour.
                            $sql = "SELECT * FROM satni_podaci 
                                    WHERE postajaKey = '$station_key' AND polutantKey = '$pollutant_key' 
                                    ORDER BY ID DESC LIMIT 1";
                            
                            try{
                                $result = AppCore::getDB()->sendQuery($sql);
                            } 
                            catch (DatabaseException $e) {
                                $e->show();
                            }
                            return $this->data = DatabaseHelper::convertToJSONFormat($result);
                        }
        
                        else if($type == 'lastday'){
                            //API id: 2
                            //API returns JSON data for a specificed station and pollutant of previous day by hour.
                            $yesterday_date = date("d.m.Y",strtotime("-1 days"));
                            $sql = "SELECT * FROM satni_podaci 
                                    WHERE postajaKey = '$station_key' AND polutantKey = '$pollutant_key' AND vrijeme LIKE '$yesterday_date%'
                                    ORDER BY ID ASC";
        
                            try{
                                $result = AppCore::getDB()->sendQuery($sql);
                            } 
                            catch (DatabaseException $e) {
                                $e->show();
                            }
                            return $this->data = DatabaseHelper::convertToJSONFormat($result);
                        }
        
                        else if($type == 'currentmonth'){
                            //API id: 3
                            //API returns JSON data for a specific station and pollutant type of current month by hour
                            $first_day_of_current_month_UNIX = DatabaseHelper::ConvertToUnix(date('01.m.Y', strtotime(date('d.m.Y'))));
                            $first_day_of_next_month_UNIX = DatabaseHelper::ConvertToUnix(date('d.m.Y', strtotime("first day of next month")));
        
                            $sql = "SELECT * FROM satni_podaci 
                                    WHERE postajaKey = '$station_key' AND polutantKey = '$pollutant_key' AND
                                    vrijemeUnix >= '$first_day_of_current_month_UNIX' AND vrijemeUnix < '$first_day_of_next_month_UNIX'
                                    ORDER BY ID ASC";
        
                            try{
                                $result = AppCore::getDB()->sendQuery($sql);
                            } 
                            catch (DatabaseException $e) {
                                $e->show();
                            };
                            return $this->data = DatabaseHelper::convertToJSONFormat($result);
                        }
        
                        else if($type == 'all'){
                            //API id: 4
                            //API returns all JSON data for a specificed station and pollutant type by hour.
                            $sql = "SELECT * FROM satni_podaci 
                                    WHERE postajaKey = '$station_key' AND polutantKey = '$pollutant_key'
                                    ORDER BY ID ASC";
        
                            try{
                                $result = AppCore::getDB()->sendQuery($sql);
                            } 
                            catch (DatabaseException $e) {
                                $e->show();
                            }
                            return $this->data = DatabaseHelper::convertToJSONFormat($result);
                        }
        
                        break;
        
                    case 'daily' : //If it is daily, it retrieves data from dnevni_podaci
                        if($type == 'latest'){
                            //API id: 5
                            //API returns JSON data for a specificed station and pollutant type by latest recorded day with average daily values.
                            $sql = "SELECT * FROM dnevni_podaci 
                                    WHERE postajaKey = '$station_key' AND polutantKey = '$pollutant_key' 
                                    ORDER BY ID DESC LIMIT 1";
                            
                            try{
                                $result = AppCore::getDB()->sendQuery($sql);
                            } 
                            catch (DatabaseException $e) {
                                $e->show();
                            };
                            return $this->data = DatabaseHelper::convertToJSONFormat($result);
                        }
                        if($type == 'currentmonth'){
                            //API id: 6
                            //API returns JSON data for a specificed station and pollutant type by current month with average daily values.
                            $first_day_of_current_month_UNIX = DatabaseHelper::ConvertToUnix(date('01.m.Y', strtotime(date('d.m.Y'))));
                            $first_day_of_next_month_UNIX = DatabaseHelper::ConvertToUnix(date('d.m.Y', strtotime("first day of next month")));
        
                            $sql = "SELECT * FROM dnevni_podaci 
                                    WHERE postajaKey = '$station_key' AND polutantKey = '$pollutant_key' AND
                                    vrijemeUnix >= '$first_day_of_current_month_UNIX' AND vrijemeUnix < '$first_day_of_next_month_UNIX'
                                    ORDER BY ID ASC";
        
                            try{
                                $result = AppCore::getDB()->sendQuery($sql);
                            } 
                            catch (DatabaseException $e) {
                                $e->show();
                            }
                            return $this->data = DatabaseHelper::convertToJSONFormat($result);
                        }
                        if($type == 'all'){
                            //API id: 7
                            //API returns all JSON data for a specificed station and pollutant type by average daily values.
                            $sql = "SELECT * FROM dnevni_podaci 
                                    WHERE postajaKey = '$station_key' AND polutantKey = '$pollutant_key'
                                    ORDER BY ID ASC";
        
                            try{
                                $result = AppCore::getDB()->sendQuery($sql);
                            } 
                            catch (DatabaseException $e) {
                                $e->show();
                            }
                            return $this->data = DatabaseHelper::convertToJSONFormat($result);
                        }
                        break;
                }
                break;

            case 'search' :
                if($interval == null){return $this->data = "No interval defined!";}
                else if($start_date == null)
                {
                    if($end_date == null){return $this->data = "No start and end date defined!";}
                    else return $this->data = "No start date defined!";
                }
                else if($end_date == null)
                {
                    if($start_date == null){return $this->data = "No start and end date defined!";}
                    else return $this->data = "No end date defined!";
                }
                
                else switch($interval){
                    case 'hourly' : //If it is hourly, it retrieves data from satni_podaci
                        //API id: 8
                        //API returns JSON data for a specificed station and pollutant type within start and end date search criteria by hourly values.
                        $start_date_UNIX = (DatabaseHelper::ConvertToUnix($start_date))*1000; //UNIX time is set to date => 00:00:00 [from start of the day]
                        $end_date_UNIX = ((DatabaseHelper::ConvertToUnix($end_date))*1000)+86399000; //UNIX time is set to date => 23:59:59 [until last second of the day, 86399000 = 23:59:59]

                        $sql = "SELECT * FROM satni_podaci 
                                    WHERE postajaKey = '$station_key' AND polutantKey = '$pollutant_key' AND
                                    vrijemeUnix >= '$start_date_UNIX' AND vrijemeUnix <= '$end_date_UNIX'
                                    ORDER BY ID ASC";
        
                        try{
                            $result = AppCore::getDB()->sendQuery($sql);
                        } 
                        catch (DatabaseException $e) {
                            $e->show();
                        }
                        return $this->data = DatabaseHelper::convertToJSONFormat($result);
                    
                    
                    case 'daily' : //If it is daily, it retrieves data from dnevni_podaci
                        //API id: 9
                        //API returns JSON data for a specificed station and pollutant type within start and end date search criteria by daily values.
                        $start_date_UNIX = (DatabaseHelper::ConvertToUnix($start_date))*1000; //UNIX time is set to date => 00:00:00 [from start of the day]
                        $end_date_UNIX = ((DatabaseHelper::ConvertToUnix($end_date))*1000)+86399000; //UNIX time is set to date => 23:59:59 [until last second of the day, 86399000 = 23:59:59]

                        $sql = "SELECT * FROM dnevni_podaci 
                                    WHERE postajaKey = '$station_key' AND polutantKey = '$pollutant_key' AND
                                    vrijemeUnix >= '$start_date_UNIX' AND vrijemeUnix <= '$end_date_UNIX'
                                    ORDER BY ID ASC";
        
                        try{
                            $result = AppCore::getDB()->sendQuery($sql);
                        } 
                        catch (DatabaseException $e){
                            $e->show();
                        }
                        return $this->data = DatabaseHelper::convertToJSONFormat($result);
                
                }
                break;

            }
        }
    }
