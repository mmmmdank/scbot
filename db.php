ForToday<?php
class scbotdb {
    private $link;
    private $config;
    
    function __construct($config) {
        $this->config = $config;
        $this->connectToDb();
    }
    
    private function connectToDB() {
        $this->link = mysql_connect($this->config->dbHost, $this->config->dbUser, $this->config->dbPass);
        if (!$this->link) {
            die('Could not connect: ' . mysql_error());
        }
        mysql_select_db($this->config->dbName, $this->link);    
    }
    
    public function getConfigFromDB() {
        $appconfig = array();
        $r = mysql_query("SELECT * FROM appconfig where UID='VLAD'", $this->link);
        $row = mysql_fetch_array($r, MYSQL_ASSOC) or die(mysql_error());
        $appconfig['api_url'] = $row['apiURL'];
        $appconfig['token'] = $row['OAuth'];
        $appconfig['client_id']=$row['clientid'];
        $appconfig['users_url_fragment'] = $row['users_url_fragment'];
        return $appconfig;
    }
    
    public function saveJsonStatsToDB($stats){
        $r = mysql_query("insert into json_stats_dump (JSON, DATE) values ('".mysql_real_escape_string(json_encode($stats))."','".date("Ymd")."')", $this->link);
        return $r;
    }
    
    public function doStatsAlreadyExistsForToday(){
        $r = mysql_query("SELECT * FROM json_stats_dump where DATE='".date("Ymd")."'", $this->link);      
		$row = mysql_fetch_array($r, MYSQL_ASSOC);	
        if($r) {
            $row = mysql_fetch_array($r, MYSQL_ASSOC);	
             return count($row)>0;
        }
        else {
            return false;
        }
    }
    
    public function getJsonGroupsForToday(){
        $r = mysql_query("SELECT * FROM json_stats_dump where DATE='".date("Ymd")."'", $this->link);
        $row = mysql_fetch_array($r, MYSQL_ASSOC);
        return json_decode($row['JSON'], true);
    }
    
    public function getDailyCounters(){
        $r = mysql_query("SELECT * FROM daily_share_counter where DATE='".date("Ymd")."'", $this->link);
        if($r) {
            $row = mysql_fetch_array($r, MYSQL_ASSOC);	
            return $row;
        }
        else {
            return false;
        }
    }
    
    public function bumpUpDailyCounter($which){ //values: SHARE_COUNTER, UNSHARE_COUNTER, GROUP_POLL_COUNTER
        $current_counters = $this->getDailyCounters();
        print('bump bump<br>');var_dump($current_counters);
        if($current_counters!=false) {
            print('<h1>1</h1>');
            $daily_counter = ($current_counters[$which])++;              
            $r = mysql_query("update daily_share_counter set ".$which."='". $daily_counter."' where DATE='".date("Ymd")."'", $this->link);
            print("<h4>".$daily_counter."</h4><h4>"."update daily_share_counter set ".$which."='". ((int)$daily_counter)++ ."' where DATE='".date("Ymd")."'"."</h4>");
            var_dump($r);
            return $r;            
        }
        else {
                        print('<h1>2</h1>');
            $r = mysql_query("insert into daily_share_counter (".$which.", DATE) values (1,'".date("Ymd")."')", $this->link);
            return $r;
        }
    
    }
    
}
?>
