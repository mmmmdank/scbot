ForToday<?php
class sc-bot-db {
    private $link;
    private $config;
    
    public function init($config) {
        $this->config = $config;
    }
    
    private function connectToDB() {
        $this->link = mysql_connect($this->config->dbHost, $this->config->dbUser, $this->config->dbPass);
        if (!$this->link) {
            die('Could not connect: ' . mysql_error());
        }
        mysql_select_db($this->config->dbName);    
    }
    
    public function getConfigFromDB() {
        $appconfig = array();
        $r = mysql_query("SELECT * FROM appconfig where UID='VLAD'");
        $row = mysql_fetch_array($r, MYSQL_ASSOC);
        $appconfig['api_url'] = $row['apiURL'];
        $appconfig['token'] = $row['OAuth'];
        $appconfig['client_id']=$row['clientid'];
        $appconfig['users_url_fragment'] = $row['users_url_fragment'];
        return $appconfig;
    }
    
    public function saveJsonStatsToDB($stats){
        $r = mysql_query("insert into 'json_stats_dump' (JSON,DATE) values ('".json_encode($stats)."','".date("Ymd")."')");
        $row = mysql_fetch_array($r, MYSQL_ASSOC);
    }
    
    public function doStatsAlreadyExistsForToday(){
        $r = mysql_query("SELECT * FROM 'json_stats_dump' where DATE='".date("Ymd")."'");
        $row = mysql_fetch_array($r, MYSQL_ASSOC);
        return count($row)>0;
    }
    
    public function getJsonGroupsForToday(){
        $r = mysql_query("SELECT * FROM 'json_stats_dump' where DATE='".date("Ymd")."'");
        $row = mysql_fetch_array($r, MYSQL_ASSOC);
        return decode_json($row['JSON']);
    }
    
}
?>