<?php
if (preg_match('/generate.php$/',$_SERVER['SCRIPT_FILENAME'])) {
    $path = '.';
    $display_nav = false;
}
else { 
    $path = '..';
    $display_nav = true; 
}
?>

<html>
<head>
<meta name=viewport content="width=device-width, initial-scale=1">
<script src="<?=$path;?>/lib/scripts/jquery-2.2.3.min.js"></script>
<link rel="stylesheet" href="<?=$path;?>/style.css" />

    <link href="<?=$path;?>/lib/themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />

<script src="<?=$path;?>/lib/scripts/jquery-ui.1.11.4.min.js"></script>
<script type="text/javascript">
     $(function() {
         $('#nav-buttons li').button();

         $('.date-field').datepicker();
     });
</script>
<body>
<?php
if ($display_nav) {
    print '<div id="wrapper">'.PHP_EOL;
    print '<div id="content">'.PHP_EOL;
    include ("nav.php");
}
?>

<h2>Get List as XML or Plain Text</h2>
<form action="<?=$path;?>/generate.php">
     <label for="first_date">First Date:</label> <input type="text" name="first_date" class="date-field" /><br />
     <label for="last_date">Last Date:</label> <input type="text" name="last_date" class="date-field" /><br />
     <label for="format">Format:</label>
     <select name="format">
     <option value="xmlIthaca">XML</option>
     <option value="text">Text</option>
     </select><br />
     <input type="hidden" name="action" value="getlist" />
     <input type="submit" />
</form>

     <h2>Get One Day as Plain Text</h2>
     <p><a href="<?=$path;?>/generate.php?action=oneday" class="button">Today</a> -- suitable for including on another page via curl or php include</p>
     <form action="<?=$path;?>/generate.php">
     <input type="hidden" name="action" value="oneday" />
     <label for="date">Choose date:</label>
     <input type="text" name="date" class="date-field" />
     <input type="submit" />
     </form>
<?php
    if ($display_nav) {
        print '</div><!--id=content-->'.PHP_EOL;
        print '<div id="footer">'.PHP_EOL;
        include ('../license.php');
        print '</div><!--id=footer-->'.PHP_EOL;
        print '</div><!--id=wrapper-->'.PHP_EOL;
    }
?>