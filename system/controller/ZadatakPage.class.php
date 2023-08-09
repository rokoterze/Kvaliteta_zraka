<?php
include('AbstractPage.class.php');
include('system/util/DatabaseHelper.class.php');
//prikaz prosjecnih vrijednosti polutanta u odabranom intervalu za neku postaju
class ZadatakPage extends AbstractPage{
    public $templateName = 'zadatak';
    
    public function execute(){
        $this->getAverageValue();
    }

    public function getAverageValue(){
        $method = $_GET['method'] ?? '';
        $station_key = $_GET['stationKey'] ?? '';

        $start_date = $_GET['start'] ?? '';
        $end_date = $_GET['end'] ?? '';

        if($station_key == null){return $this->data = "No station defined!";}
        if($method == null){return $this->data = "No method defined!";}
        if($start_date == null || $end_date == null ){return $this->data = "Date not specified correctly";}

       
        $start_date_UNIX = (DatabaseHelper::ConvertToUnix($start_date))*1000; //pocetak dana
        $end_date_UNIX = ((DatabaseHelper::ConvertToUnix($end_date))*1000)+86399000; //kraj dana
        
        // $sql = "SELECT * FROM dnevni_podaci 
        //         WHERE postajaKey = '$station_key' AND
        //         vrijemeUnix >= '$start_date_UNIX' 
        //         AND vrijemeUnix <= '$end_date_UNIX'
        //         ORDER BY ID ASC";

        $sql = "SELECT postajaKey, polutantKey, AVG(vrijednost) as avg_vrijednost 
        FROM dnevni_podaci 
        WHERE postajaKey = '$station_key' 
        AND vrijemeUnix >= '$start_date_UNIX' 
        AND vrijemeUnix <= '$end_date_UNIX'
        GROUP BY postajaKey
        ORDER BY ID ASC";
                
                $dataType = array();
                try{
                    $result = AppCore::getDB()->sendQuery($sql);
                } 
                catch (DatabaseException $e) {
                    $e->show();
                }
                // while($row = AppCore::getDB()->fetchArray($result)){
                //     $dataType[$row['podatakKey']] = $row['podatakNaziv'];
                // }

                

            //return $this->data = DatabaseHelper::convertToJSONFormat($result);
            return $this->data = $dataType;
 
    }
}