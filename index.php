<?php
error_reporting(E_ALL);
 ini_set("display_errors", 1);

print('hello human, this is the realm of the robot. beware. -----<br>');
include('sc-inc.php');

$scHelper = new sc;

if(isset($_GET['randomswap']) && $_GET['randomswap']>0) {
    print('random swappin '.$_GET['randomswap'].' many shares<br><br>');
    $scHelper->randomSwapOneTrack($_GET['randomswap']);
    print('<br><br>yeaboi3! swapin random!');
    exit();
}
if(isset($_GET['swapspit1']) && $_GET['swapspit1']=='true') {
    print('spitswappin!');
   $scHelper->spitSwapRun1();
    print('yeaboi!');
    exit();
}
if(isset($_GET['tothetop']) && $_GET['tothetop']=='true') {
    print('tothetop!');
   $scHelper->randomSwapToTheTop();
    print('tothetop DONE!');
    exit();
}
else if(isset($_GET['run']) && $_GET['run']!="") {
    print('run ! '.$_GET['run']);
    /*$scHelper->spitSwapRun1();*/
    print('yeaboi! r.u.n.');
    exit();
}
else if(isset($_GET['getgroups']) && $_GET['getgroups']=="true") {
    print('run getgroups! '.$_GET['getgroups']);
    $scHelper->initGroups();
    print('yeaboi! r.getgroups.n.');
    exit();
}
 
else {

    $sc_stats = $scHelper->getStats();
    $counters = $scHelper->getCounters();   
    var_dump($counters);
    print('<br><br>stats heea:');
    var_dump($sc_stats);
?>
<html>
    <head>
        <script type="text/javascript" src="js/app.js"></script>
    </head>

    <body>
        <h1>s</h1>
        ThingS: <br>
        All songs are currently shared to: <?php print($sc_stats['groupStats']['groups_total']); ?> groups;<br>
        Like this:<br>
        <p style="padding:20px;">
            <?php
                if($sc_stats) {
                    foreach($sc_stats['groupStats']['groups_counts'] as $t=>$k) {
                        print($t.' to '.$k.'<br>');
                    }
                }
            ?>
        </p>
        Counters:<br>
        <p style="color:green;margin:30px;">
            Group Polls: <?php print($counters['GROUP_POLL_COUNTER']);?><br>
            Shares Today: <?php print($counters['SHARE_COUNTER']);?><br>
            Unshares Today: <?php print($counters['UNSHARE_COUNTER']);?><br>
        </p>
        
<?php
}
?>
</body>
    
</html>
