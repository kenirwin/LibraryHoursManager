<html>
<head>
<meta name=viewport content="width=device-width, initial-scale=1">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<link rel="stylesheet" href="style.css" />
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">

    <link href="lib/themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript">
     $(function() {
         $('#nav-buttons li').button();

         $('.date-field').datepicker();
     });
</script>
<?
include ("nav.php");
?>
<form action="generate.php">
     First Date: <input type="text" name="first_date" class="date-field"><br />
     Last Date: <input type="text" name="last_date" class="date-field"><br />
     Format: 
     <select name="format">
     <option value="xmlIthaca">XML</option>
     <option value="text">Text</option>
     </select><br />
     <input type="hidden" name="action" value="getlist">
     <input type="submit">
</form>