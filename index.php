<?php
error_reporting(E_ALL);
 ini_set("display_errors", 1);

print('start-----<br>');
include('sc-inc.php');

$scHelper = new sc;

$scHelper->init();
$scHelper->spitSwapRun1();
$sc_stats = $scHelper->getStats();

?>
<html>
    <head>
    <script type="text/javascript" src="js/app.js"></script>
    </head>

    <body>
        <h1>stay inside the box</h1>
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


<?php



?>
</body>
    
</html>
