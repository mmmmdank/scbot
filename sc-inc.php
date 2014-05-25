<?php
include('config.php');
include('db.php');

class sc {
    //CONSTANTS
    private $config;     
    private $db;
    private $helperConfig;
    
    function __construct() {
        $this->config = new sc-bot-config;     
        $this->db = new sc-bot-db;
    }
    
    private $session_data = array(
        'groups_by_track'=>array(),
        'groups_by_track_counts'=>array(),
        'all_shared_groups'=>array()
    );
    
    public function init() {   
        $this->db->init();
        $this->helperConfig = $this->db->getConfigFromDB();
        $this->setTimeZone();
    }
    
    public function getStats() {
        if($this->db->doStatsAlreadyExistsForToday()) {
            print('<br>already got stats in db for today...<br>');
            $stats = $this->db->getJsonGroupsForToday();
        }
        else {
            print('<br>...fetching stats for sc<');
            $stats = array();
            $groupStats = $this->getGroupStats();
            $profileStats = $this->getProfileStats();
            $trackStats = $this->getTrackStats();

            $stats['profileStats'] = $profileStats;
            $stats['groupStats'] = $groupStats;
            $stats['trackStats'] = $trackStats;
            
            $this->db->saveJsonStatsToDB($stats);
        }
        return $stats;
    }
    
    private function initGroups() {
        foreach($this->config->tracks as $name=>$id) {
            //$groups = $this->getCurrentGroupsByName($name);
	        $this->session_data['groups_by_track'][$name] = $groups;
            $this->session_data['groups_by_track_counts'][$name] = count($groups);
        }
        
        foreach($this->session_data['groups_by_track'] as $g4t) {
            foreach($g4t as $group) {
                if(!in_array($group, $this->session_data['all_shared_groups'])) {
                    array_push($this->session_data['all_shared_groups'], $group);   
                }
            }
        }
        
    }
    
    private function getGroupStats() {
        $stats = array();
        if(count($this->session_data['groups_by_track'])==0) { 
            $this->initGroups();
        }
        
        //$stats['groups'] = $this->session_data['groups_by_track'];
        $stats['groups_counts'] = $this->session_data['groups_by_track_counts'];
        $stats['groups_total'] = count($this->session_data['all_shared_groups']);
         
        return $stats;
    }
    private function getProfileStats() {
        $stats = array();
        return $stats;
    }
    private function getTrackStats() {
        $stats = array();
        return $stats;
    }
    private function saveStatsToDB() {
        return false;
    }
    
    
    public function doA50Reshares1stSample() {
         /*$groups = $this->getCurrentGroupsByName("tractors");*/
        $this->unshareTrackFromGroups("bears", $this->getCurrentGroupsByName("bears"),10);
        $this->shareTrackToGroups("bears", $this->getCurrentGroupsByName("forklift"),10);
        
        $this->unshareTrackFromGroups("forklift", $this->getCurrentGroupsByName("forklift"),10);
        $this->shareTrackToGroups("forklift", $this->getCurrentGroupsByName("bears"),10);
        
        $this->unshareTrackFromGroups("cow", $this->getCurrentGroupsByName("cow"),10);
        $this->shareTrackToGroups("cow", $this->getCurrentGroupsByName("chaka"),10);
        
        $this->unshareTrackFromGroups("firetrucks", $this->getCurrentGroupsByName("firetrucks"),10);
        $this->shareTrackToGroups("firetrucks", $this->getCurrentGroupsByName("tractors"),10);
        
        $this->unshareTrackFromGroups("whales", $this->getCurrentGroupsByName("whales"),10);
        $this->shareTrackToGroups("whales", $this->getCurrentGroupsByName("forklift"),10);
        
        print('ding!');
    }
    
    public function spitSwapRun1() {
         /*$groups = $this->getCurrentGroupsByName("tractors");*/
        $this->spitSwap("bears","forklift",10);
        $this->spitSwap("forklift","cow",10);
        $this->spitSwap("cow","whales",10);
        $this->spitSwap("whales","firetrucks",10);
        $this->spitSwap("firetrucks","bears",10);
        
        $this->spitSwap("bears","firetrucks",10);
        $this->spitSwap("forklift","cow",10);
        $this->spitSwap("bears","whales",10);
        $this->spitSwap("forklift","firetrucks",10);
        $this->spitSwap("whales","cow",10);
        
        print('ding! - spitSwapRun1');
    }
    
    private function spitSwap($track1, $track2, $num){
        $tr1groups=$this->getCurrentGroupsByName($track1);
        $tr2groups=$this->getCurrentGroupsByName($track2);
        
        $this->unshareTrackFromGroups($track1, $tr1groups,$num);
        $this->unshareTrackFromGroups($track2, $tr2groups,$num);
        $this->shareTrackToGroups($track1, $tr2groups, $num);
        $this->shareTrackToGroups($track2, $tr1groups, $num);
    
    }

    private function setTimeZone() {
        date_default_timezone_set( 'America/New_York');
    }

    private function getTrackIdByName($name) {
        return $config->tracks[$name];
    }
    
    private function makeGroupsURL($name) {
        return $this->helperConfig['api_url'] . "tracks/" . $this->getTrackIdByName($name) . "/groups/?representation=mini&linked_partitioning=1&limit=5000&client_id=" .  $this->helperConfig['client_id'];   
    }
    
    private function makeUnshareUrl($name, $group_id) {
       return $this->helperConfig['api_url'] . "groups/" . $group_id . "/contributions/" . $this->getTrackIdByName($name) . "?client_id=" . $this->helperConfig['client_id'];
    }
    
    private function makeShareUrl($name, $group_id) {
       return $this->helperConfig['api_url'] . "groups/" . $group_id . "/contributions?client_id=" . $this->helperConfig['client_id'];
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
        $groups = array();
           
        foreach ($xml->children() as $xmlgroup) {
            $group = array();
            foreach($xmlgroup->children() as $attr) {
                if($attr->getName() == "id") {$group['id']=$attr[0];}
                if($attr->getName() == "name") {$group['name']=$attr[0];}
            }
            if($group['id'] && $group['name']) {
                array_push($groups,$group);
            }
        }
        return $groups;
    }
    
    public function unshareTrack($name, $group_id) {
        $url = $this->makeUnshareUrl($name, $group_id);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $this->helperConfig['token']));

        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch); 
        var_dump($data);
    }
    
    public function shareTrack($name, $group_id) {
        $url = $this->makeShareUrl($name, $group_id);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS    ,"track%5Bid%5D=".$this->getTrackIdByName($name));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $this->helperConfig['token'], 'Content-Length:'.strlen("track%5Bid%5D=".$this->getTrackIdByName($name))));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        var_dump($data);
    }
    
    public function unshareTrackFromGroups($name, $groups, $num) {
        $counter=0;
        foreach($groups as $g) {
            if($num>0) {$counter++; if($counter>=$num) {break;}}
            $this->unshareTrack($name,$g["id"]);
        }
    }
    
    public function shareTrackToGroups($name, $groups, $num) {
        $counter=0;
        foreach($groups as $g) {
            if($num>0) {$counter++; if($counter>=$num) {break;}}
            $this->shareTrack($name,$g["id"]);
        }
    }
    
    public function getAllMyGroups() {
        return $this->session_data['all_shared_groups'];
    }

} 


?>
