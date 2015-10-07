<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Flot Examples: Time Axes</title>
	<link href="./flot/examples.css" rel="stylesheet" type="text/css">
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="./flot/excanvas.min.js"></script><![endif]-->
	<script language="javascript" type="text/javascript" src="./flot/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="./flot/jquery.flot.js"></script>
	<script language="javascript" type="text/javascript" src="./flot/jquery.flot.time.js"></script>
	<script type="text/javascript">

<?
                              $times = array (
                                  array("start"=> "2015-08-14",
                                        "end"  => "2015-12-15",
                                        "rank" => "1",
                                        "name" => "Semester"
                                  ),
                                  array("start"=> "2015-10-14",
                                        "end"  => "2015-10-17",
                                        "rank" => "2",
                                        "name" => "Break"
                                  ),
                                  array("start"=> "2015-11-01",
                                        "end"  => "2015-11-15",
                                        "rank" => "2",
                                        "name" => "Elsewhat"
                                  )
                              );

$details = array();
foreach ($times as $a) {
    array_push($details,FlotVar($a));
    $all_ids = array();
}

$return['ids'] = '['.join(',',$all_ids).']';


function FlotVar($a) {
    $return = array();
    $return['label'] = $a['name'];
    $return['id'] = preg_replace("/ +/","_",$a['name']);
    array_push($all_ids,$return['id']);

    $return['var'] = 'var '.$return['id'].' = [['.jsTime($a['start']).','. $a['rank']. '], ';
    $return['var'].= '['.jsTime($a['end']).','. $a['rank']. ']];'.PHP_EOL;

    return $return;
}

function jsTime($str) {
    return strtotime($str)*1000;
}
?>


	$(function() {

        //		var d = [[-373597200000, 315.71], [-370918800000, 317.45], [-368326800000, 317.50], [-363056400000, 315.86], [-360378000000, 314.93], [-357699600000, 313.19], [-352429200000, 313.34], [-349837200000, 314.67], [-347158800000, 315.58], [-344480400000, 316.47]];
        
        <? 
        foreach ($details as $a) {
            print ($a['var']).PHP_EOL;
            print '$.plot("#placeholder", ['.$a['id'].'], {'.PHP_EOL;
            print ' xaxis: { mode: "time" }'.PHP_EOL;
            print '});'.PHP_EOL;
        }
        ?>
        
        

		$("#footer").prepend("Flot " + $.plot.version + " &ndash; ");
	});

	</script>
</head>
<body>

	<div id="header">
		<h2>Time Axes</h2>
	</div>

	<div id="content">

		<div class="demo-container">
			<div id="placeholder" class="demo-placeholder"></div>
		</div>

    <?
    print_r($details);
    ?>
</body>
</html>
