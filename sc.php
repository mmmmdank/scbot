<style>
.song-share {
	display:none;
}
</style>
<?php
error_reporting(E_ALL); 
 ini_set("display_errors", 1); 
 
 
if($_GET['reshareall']) {
	/*REQUEST EXAMPLE: 
		https://api.soundcloud.com/tracks/95783468/groups/?representation=mini&linked_partitioning=1&limit=5000&client_id=b45b1aa10f1ac2941910a7f0d10f8e28      (type GET)
	
		https://api.soundcloud.com/users/26482522/groups?representation=mini&limit=15&offset=0&linked_partitioning=1&client_id=b45b1aa10f1ac2941910a7f0d10f8e28 (type GET)	
	
	
		https://api.soundcloud.com/groups/32054/contributions/95783468?client_id=b45b1aa10f1ac2941910a7f0d10f8e28 (DELETE)
		https://api.soundcloud.com/groups/32054/contributions?client_id=b45b1aa10f1ac2941910a7f0d10f8e28 (POST)
	*/
	if ($_GET['sleep'] && $_GET['repeat']) {$sleep = $_GET['sleep']; $repeat = $_GET['repeat'];}
	else {$repeat = 1;$sleep=0;}

	$link = mysql_connect('localhost', 'root', 'Wi11break$');
        if (!$link) {
            die('Could not connect: ' . mysql_error());
        }
        mysql_select_db("sharemonkey");

        $r = mysql_query("SELECT * FROM appconfig where UID='VLAD'");
        $row = mysql_fetch_array($r, MYSQL_ASSOC);
var_dump($row);
        print($r."<br>".$row."<br>".$link."<br>");
        print("<Br>".$row['uid']."<br>".$row['clientid']."<br>".$row['OAuth']."<br>");

        $start_time = time();
	$api_url = $row['apiURL'];
	$token = $_GET['auth_token'] ? $_GET['auth_token'] : $row['OAuth'];
	$client_id=$row['clientid'];
	//goldilox //https://api.soundcloud.com/groups/28955/contributions/99583408?client_id=b45b1aa10f1ac2941910a7f0d10f8e28
        //bears!
	//forklift
	//firetrucks
	//chaka
	//capleton/cutty
	//tractrs
	//fela
	//magnum
	
	$track_ids = array("106944823","103486177"
                        ,"99583408"
                        ,"97204021"
			,"95783468"
			,"91741317"
			,"79674516"
			,"77591808"
			,"87757601"
			,"72433836"
			,"68747384");
	
	//$tracks_url_fragment = "95783468";
	$users_url_fragment = $row['users_url_fragment'];
	//$usergroups_list_url=$api_url . "users/" . $users_url_fragment . "/groups/?representation=mini&limit=5000&offset=0&linked_partitioning=1&client_id=" . $client_id;

	$loop_counter=0;
	
	print("<h2>Start all song reshare</h2>");	
	print('auth token: '.$token.'<br><br><hr>');

date_default_timezone_set( 'America/New_York');
	while($loop_counter < $repeat) {
		$total_unshares_counter = 0;
		$total_reshares_counter = 0;
		$song_counter = 0;
		print("<h1>".date('m/d/Y h:i:s a', time())."\\/\/\/\/\/\/\/\/\/\/\/\/ LOOOP   NUUUMMBEEEEER " .$loop_counter. " of " .$repeat."\/\/\/\/\/\/\/\/\/\/\</h1>");
		
		foreach ($track_ids as $tracks_url_fragment) {
			$song_unshares_counter = 0;
			$song_reshares_counter = 0;
			$song_counter++;
			$group_list_url=$api_url . "tracks/" . $tracks_url_fragment . "/groups/?representation=mini&linked_partitioning=1&limit=5000&client_id=" . $client_id;
			
			print("<h2>**** Start song " .$tracks_url_fragment. " reshare</h2>");
			print("<br>request to: ".$group_list_url.'<br>');	
			$ch = curl_init($group_list_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_VERBOSE, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			sleep(rand(45,237));
                        $data = curl_exec($ch);
			curl_close($ch);
			
			$parsed_xml = simplexml_load_string($data);
		
			print("<br>currently shared to: " . $parsed_xml->count() . " groups<Br>");
                        if($parsed_xml->count()<40) {
                             $q="select * from groupxmldumps where songid='".$tracks_url_fragment."' order by length(grouplistxmldump) desc LIMIT 1";
				$r=mysql_query($q,$link);                      
				$row = mysql_fetch_array($r, MYSQL_ASSOC);
var_dump($row);
        print($r."<br>".$row."<br>".$link."<br>");
        print("<Br>Uhoh too low reset to previous<br><bR>");
                        $data = $row['grouplistxmldump'];
                        $parsed_xml = simplexml_load_string($data);
print('new count!!!!: '.$parsed_xml->count()."<br><br>");
                        }			

			print('<input type="checkbox" class="exapnder"><Br><div class="song-share">');

                        $q="insert into groupxmldumps (songid,grouplistxmldump) values (".$tracks_url_fragment.", '".mysql_real_escape_string($data)."')";
                        mysql_query($q,$link) || print('!!! '.mysql_real_escape_string($q).' ||||| '.mysql_error());

			foreach ($parsed_xml->children() as $group) {
				$group_id = "";
				//print("-------------------group:<br>");
				foreach($group->children() as $attr) {
					print($attr->getName()." ||| ". $attr. "<br>");
					if($attr->getName() == "id") {$group_id=$attr;}
				}
				if($group_id != "") {
					$group_unshare_url = $api_url . "groups/" . $group_id . "/contributions/" . $tracks_url_fragment . "?client_id=" . $client_id;
					$ch = curl_init($group_unshare_url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_VERBOSE, true);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $token));
				
					curl_setopt($ch, CURLOPT_HEADER, 0);
					$data = curl_exec($ch);
					curl_close($ch);
					print('!!!!!!!!!!!!!!UNSHARED' . var_dump($data) . "<br>");
					$total_unshares_counter++;
					$song_unshares_counter++;
					$group_unshare_url = $api_url . "groups/" . $group_id . "/contributions?client_id=" . $client_id;
					print('overwrite un->re<br>'.var_dump($group_unshare_url).'<br><br>');
                                        $ch = curl_init($group_unshare_url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_VERBOSE, true);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS    ,"track%5Bid%5D=".$tracks_url_fragment);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $token, 'Content-Length:'.strlen("track%5Bid%5D=".$tracks_url_fragment)));
				
					curl_setopt($ch, CURLOPT_HEADER, 0);
					$data = curl_exec($ch);
					curl_close($ch);
					print('!!!!!!!!!!!!!!RESHARED' . var_dump($data) . " || length: ".strlen("track%5Bid%5D=".$tracks_url_fragment)."<br>");
					$total_reshares_counter++;
					$song_reshares_counter++;
				}
			}
			print('</div>');
			print('<span>Song unshared from: '.$song_unshares_counter.' | reshared to: '.$song_reshares_counter.'</span><hr>');
		}
		print("total unshared: ". $total_unshares_counter . " ||| total reshared: " . $total_reshares_counter . " ||| total songs: " . $song_counter. "<br>");
		$loop_counter++;
		print('zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz for '.$sleep.'sec');sleep($sleep);
	}
	print('DONE RESHARING!!!!<br><br><br><bR><br><br>');

	$end_time = time();
	print("timers: ".$end_time." - ".$start_time." = this shit took: " . $end_time - $start_time . "ms == ". ($end_time - $start_time) / 1000 . " sec == " .  ($end_time - $start_time) / 1000 / 60 . " min <br> ");
mysql_close($link);

}
?>
<br>
<hr>
<h1>share monkey see share monkey do</h1>
<hr>
Reshare all songs to all currently shared groups.
<form method="GET" action="">
<input type="text" name="auth_token" placeholder="auth token"><input type="text" name="repeat" value="5"><input type="text" name="sleep" value="10">
<input type="hidden" name="reshareall" value="true">
<input type="submit" value="reshare">
</form>
<hr>
