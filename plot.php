<?
include ("hours.class.php");
include ("admin.class.php");

$hours = new Hours();
$times = $hours->GetTimeframesAndRanks();
$admin = new HoursAdmin(); 
$graphJS = $admin->BuildGraphJS($times);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Flot Examples: Time Axes</title>
                              <? print_r ($graphJS); ?>

</head>
<body>

	<div id="header">
		<h2>Time Axes</h2>
	</div>

	<div id="content">

		<div class="demo-container">
			<div id="placeholder" class="demo-placeholder"></div>
		</div>
</body>
</html>
