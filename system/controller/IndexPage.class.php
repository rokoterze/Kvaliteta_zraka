<?php
include('AbstractPage.class.php');
include('./system/util/FetchAPI.class.php');
class IndexPage extends AbstractPage{
    public $templateName = 'index';
   
    public function execute(){
        FetchAPI::fetchDailyData(); //When Index page is executed, this function updates table data with new data on daily basis.
        
        $sql = "SELECT * FROM url_link";
        $result = AppCore::getDB()->sendQuery($sql);
        $url_links=[];

        while($row = AppCore::getDB()->fetchArray($result)){
            $url_links[$row['ID']] = $row;
        }
        
        $this->data = ['url_links' => $url_links];
    }
}