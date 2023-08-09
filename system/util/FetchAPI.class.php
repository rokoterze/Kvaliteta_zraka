<?php
require ('DatabaseHelper.class.php');
class FetchAPI{
    const BASE_API_URL = 'https://iszz.azo.hr/iskzl/rs/podatak/export/json?';
    
    /**
     * Summary of getData
     * @param mixed $url
     * @return mixed
     */
    protected static function getData($url){
        $json_format = file_get_contents($url);
        return json_decode($json_format); 
    }

    public static function fetchDailyData(){ //Main function which will be executed on IndexPage.class.php.
        foreach(DatabaseHelper::getDataTypes() as $tablica_key => $tablica_value){
            self::fetchDailyDataTable($tablica_key, $tablica_value);
        }
    }
    protected static function fetchDailyDataTable($tablica_key, $tablica_value){ //This function should be written better, because if IndexPage.class.php is not loaded on daily basis, gap with data will be created. Solution is to create a new function which will check last day in database from daily_fetch_log and fetch all data since that day until yesterday.  
        $yesterday_date = date("d.m.Y",strtotime("-1 days")); //Yesterday because today's data is mostly empty JSON on fetch api site, but can be set to today also.
        //If needed, here can be created for loop which will decrease days until last x days, code below should be inserted inside of loop too. Problem will be that too many data will be fetched which can cause unexpected crush.
        try{
            foreach(DatabaseHelper::getStations() as $station_key => $station_value){ //Needs optimization, $station_value not used!

                foreach(DatabaseHelper::getPollutants() as $pollutant_key => $pollutant_value){ //Needs optimization, $polutant_value not used!

                    if(DatabaseHelper::checkLogForDailyFetch($station_key, $pollutant_key, $tablica_value) == false){ 

                        //This part of a code generates all available combination to create valid URL which will display and later fetch JSON data.

                        $fetch_api_url = self::BASE_API_URL."postaja=".$station_key."&polutant=".$pollutant_key."&tipPodatka=".$tablica_key."&vrijemeOd=".$yesterday_date."&vrijemeDo=".$yesterday_date;
                        $response = self::getData($fetch_api_url);
                        
                        if($response != null){ //This check is created to skip createLogForDailyFetch function if there is null response.
                            DatabaseHelper::insertFetchData($station_key, $pollutant_key, $tablica_value, $response);
                            DatabaseHelper::createLogForDailyFetch($station_key, $pollutant_key, $tablica_value);
                        }
                        
                    }
                }
            }
        }
        catch(SystemException $e){
            $e->show();
        }
    }
}