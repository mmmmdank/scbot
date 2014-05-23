<?php

class sc {
    //CONSTANTS
    private $dbName="sharemonkey";
    private $dbHost="localhost";
    private $dbUser="root";
    private $dbPass="Wi11break$";
    private $link;
    
    private $tracks = array( "cow"=>"106944823"
                            ,"whales"=>"103486177"
                            ,"goldilox"=>"99583408"
                            ,"bears"=>"97204021"
			                ,"forklift"=>"95783468"
			                ,"firetrucks"=>"91741317"
                            ,"chaka"=>"79674516"
                            ,"capleton"=>"77591808"
                            ,"tractors"=>"87757601"
                            ,"fela"=>"72433836"
                            ,"magnumpig"=>"68747384");
    
    private $helperConfig = array(
       "uid" => "",
       "client_id" => "",
       "token" => "",
       "api_url" => "",
       "users_url_fragment" => ""
    );

    public function init() {
        $this->connectToDB();
        $this->getConfigFromDB();
        $this->setTimeZone();
        print($this->getCurrentGroupsByName("tractors"));
    }

    private function setTimeZone() {
        date_default_timezone_set( 'America/New_York');
    }

    private function connectToDB() {
        $this->link = mysql_connect($this->dbHost, $this->dbUser, $this->dbPass);
        if (!$this->link) {
            die('Could not connect: ' . mysql_error());
        }
        mysql_select_db($this->dbName);    
    }
    
    private function getConfigFromDB() {
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

    private function getTrackIdByName($name) {
        return $this->tracks[$name];
    }
    
    private function makeGroupsURL($name) {
        return $this->helperConfig['api_url'] . "tracks/" . $this->getTrackIdByName($name) . "/groups/?representation=mini&linked_partitioning=1&limit=5000&client_id=" .  $this->helperConfig['client_id'];
        
    }
    
    private function getXmlFrom($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return simplexml_load_string($data);
    }
    
    public function getCurrentGroupsByName($name) {
        $group_list_url=$this->makeGroupsURL($name);
        $xml=$this->getXmlFrom($group_list_url);
           
        foreach ($xml->children() as $xmlgroup) {
            $group = array();
            foreach($xmlgroup->children() as $attr) {
               /* print($attr->getName()." ||| ". $attr. "<br>");*/
                if($attr->getName() == "id") {$group['id']=$attr;}
                if($attr->getName() == "name") {$group['name']=$attr;}
            }
            if($group['id'] && $group['name']) {
                array_push($groups,$group);
            }
        }
        return $groups;
    }

} 


?>
