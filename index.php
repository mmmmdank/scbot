<?php
error_reporting(E_ALL);
 ini_set("display_errors", 1);

print('hello human, this is the realm of the robot. beware. -----<br>');
include('sc-inc.php');

$scHelper = new sc;

if(isset($_GET['swapspit1']) && $_GET['swapspit1']=='true') {
    print('spitswappin!');
   $scHelper->spitSwapRun1();*/
    print('yeaboi!');
    exit();
}
else if(isset($_GET['run']) && $_GET['run']!="") {
    print('run ! '.$_GET['run']);
    /*$scHelper->spitSwapRun1();*/
    print('yeaboi! r.u.n.');
    exit();
}
 
else {

    $sc_stats = $scHelper->getStats();
    /*$counters = $scHelper->getCounters();*/

    $scHelper->testCounters();
    
?>
<html>
    <head>
        <script type="text/javascript" src="js/app.js"></script>
    </head>

    <body>
        <h1>stay inside the box earthling</h1>
        ThingS: <br>
        All songs are currently shared to: <?php print($sc_stats['groupStats']['groups_total']); ?> groups;<br>
        Like this:<br>
        <p style="padding:20px;">
            <?php
                foreach($sc_stats['groupStats']['groups_counts'] as $t=>$k) {
                    print($t.' to '.$k.'<br>');
                }
            ?>
        </p>
        Counters:<br>
<?php
}
?>
</body>
    
</html>
