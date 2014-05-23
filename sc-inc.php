<?php

class sc {
    //CONSTANTS
    private $dbName="sharemonkey";
    private $dbHost="localhost";
    private $dbUser="root";
    private $dbPass="Wi11break$";
    private $link;
    private $helperConfig = array(
       "uid" => "",
       "client_id" => "",
       "token" => "",
       "api_url" => "",
       "users_url_fragment" => ""
    );

    public function init() {
        $this->getPropertiesFromDB();
        $this->setTimeZone();
    }

    private function setTimeZone() {
        date_default_timezone_set( 'America/New_York');
    }

    private function getPropertiesFromDB() {
        $this->link = mysql_connect($this->dbHost, $this->dbUser, $this->dbPass);
        if (!$this->link) {
            die('Could not connect: ' . mysql_error());
        }
        mysql_select_db($this->dbName);

        $r = mysql_query("SELECT * FROM appconfig where UID='VLAD'");
        $row = mysql_fetch_array($r, MYSQL_ASSOC);
        print("<Br>uid: ".$row['uid']."<br>clientid: ".$row['clientid']."<br>auth token:".$row['OAuth']."<br>");

        var_dump($this->helperConfig);
        
 
        $this->helperConfig['api_url'] = $row['apiURL'];
        $this->helperConfig['token'] = $row['OAuth'];
        $this->helperConfig['client_id']=$row['clientid'];
        $this->helperConfig['users_url_fragment'] = $row['users_url_fragment'];
        print("<h4>Pulled config from DB....</h4>");
        print("auth token: ".$this->helperConfig['token']."<br><br><hr>");
    }

    public function getCurrentGroupsByTrackId($trackid) {
        $group_list_url=$this->helperConfig['api_url'] . "tracks/" . $trackid . "/groups/?representation=mini&linked_partitioning=1&limit=5000&client_id=" .  $this->helperConfig['client_id'];




    }

} 


?>
