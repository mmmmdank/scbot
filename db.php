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
        $r = mysql_query("insert into json_stats_dump (JSON,DATE) values ('".mysql_real_escape_string(json_encode($stats))."','".date("Ymd")."')", $this->link);
        if (!$r) {
    die('Invalid query: ' . mysql_error());
}
        $row = mysql_fetch_array($r, MYSQL_ASSOC);
    }
    
    public function doStatsAlreadyExistsForToday(){
        $r = mysql_query("SELECT * FROM json_stats_dump where DATE='".date("Ymd")."'", $this->link);      
		$row = mysql_fetch_array($r, MYSQL_ASSOC);	
        return $row && count($row)>0;
    }
    
    public function getJsonGroupsForToday(){
        $r = mysql_query("SELECT * FROM 'json_stats_dump' where DATE='".date("Ymd")."'", $this->link);
        $row = mysql_fetch_array($r, MYSQL_ASSOC);
        return decode_json($row['JSON']);
    }
    
}
?>
