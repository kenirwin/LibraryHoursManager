<html>
<head>
<meta name=viewport content="width=device-width, initial-scale=1">
<script src="./lib/scripts/jquery-2.2.3.min.js"></script>
<link rel="stylesheet" href="style.css" />

    <link href="./lib/themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />

<script src="./lib/scripts/jquery-ui.1.11.4.min.js"></script>
<script type="text/javascript">
     $(function() {
         $('#nav-buttons li').button();

         $('.date-field').datepicker();
     });
</script>
<?
include ("nav.php");
?>

<h2>Get List as XML or Plain Text</h2>
<form action="generate.php">
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
     <p><a href="generate.php?action=oneday" class="button">Today</a> -- suitable for including on another page via curl or php include</p>
     <form action="generate.php">
     <input type="hidden" name="action" value="oneday" />
     <label for="date">Choose date:</label>
     <input type="text" name="date" class="date-field" />
     <input type="submit" />
     </form>
