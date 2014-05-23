<?php

class sc {
    //CONSTANTS
    private $dbName="sharemonkey";
    private $dbHost="localhost";
    private $dbUser="root";
    private $dbPass="Wi11break$";
    private $link;
    
    private $tracks = array( "106944823"
                            ,"103486177"
                            ,"goldilox"=>"99583408"
                            ,"bears"=>"97204021"
			                ,"forklift"=>"95783468"
			                ,"firetrucks"=>"91741317"
                            ,"chaka"=>"79674516"
                            ,"capleton"=>"77591808"
                            ,"tractors"=>"87757601"
                            ,"fela"=>"72433836"
                            ,"magnumpig"=>"68747384");
    
    //goldilox //https://api.soundcloud.com/groups/28955/contributions/99583408?client_id=b45b1aa10f1ac2941910a7f0d10f8e28
        //bears!
	//forklift
	//firetrucks
	//chaka
	//capleton/cutty
	//tractrs
	//fela
	//magnum
	
    
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
