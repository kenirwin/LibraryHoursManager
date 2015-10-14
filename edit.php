<html>
<head>
<meta name=viewport content="width=device-width, initial-scale=1">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<link rel="stylesheet" href="style.css" />
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript">
     function BindTimeframeFields() {
         $(function() {
             $('select[name="use_preset"]').change(function() {
                 $.ajax({url: 'ajax-admin.php?action=show-preset&id='+$(this).val(), success: function(result) {
                       $('#settings-placeholder').html(result);
                   }});
             });


             $('input[name="first_date"]').datepicker();
             $('input[name="last_date"]').datepicker();
         });
     }
     
           $(function() {
               $('#timeframe-picker tr').click(function() {
                   $(this).parent().children().removeClass('highlight');
                   $(this).addClass('highlight');
                   $.ajax({url: 'ajax-admin.php?action=show-timeframe&id='+$(this).attr('data-preset-id'), success: function(result) {
                       $('#preset-details').html(result);
                       BindTimeframeFields();
                   }});
               });

               $('#new-timeframe-button').click(function() {
                   $.ajax({url: 'ajax-admin.php?action=new-timeframe', success: function(result) {
                       $('#preset-details').html(result);
                       BindTimeframeFields();
                   }});


               });
               
               $('.delete-button').click(function(event) {
                   var setting_name = $(this).parent().parent().children(':first-child').text();
                   var r = confirm('Really delete this preset ('+setting_name+') and all its settings?');
                   if (r == false) {
                       event.preventDefault();
                       event.stopPropagation();
                   }
               });

           });
</script>
</head>
<body id="edit">
<?
include ("hours.class.php");
include ("admin.class.php");

$hours = new Hours();
$admin = new HoursAdmin(); 

if (isset($_REQUEST['action'])) {
    switch ($_REQUEST['action']) {
    case ('submit_preset_values'):
        $hours->UpdateTimeframe(json_encode($_REQUEST));        
        break;
    case ('submit_new_preset'):
        $hours->UpdateTimeframe(json_encode($_REQUEST));
        break;
    case ('delete_preset'):
        $hours->DeleteTimeframe($_REQUEST['preset_id']);
        break;
    }
}
else { 
    $times = $hours->GetTimeframesAndRanks();
    //$presets = $hours->getJSON('presets');
    $exceptions = $hours->getJSON('exceptions');
    $admin->TimeframePicker($times);
    $graphJS = $admin->BuildGraphJS($times,$exceptions);
}
?>
<div id="preset-details"></div>
<? print $graphJS; ?>
<h2 style="text-align:center">Timeline of Date Settings by Rank</h2>
<div class="demo-container">
	<div id="placeholder" class="demo-placeholder"></div>
</div>


<? 
    if (sizeof($_REQUEST) > 0) {
        print '<hr>'.PHP_EOL;
        print '<a href="'.$_SERVER['SCRIPT_NAME'].'">Clear</a>'.PHP_EOL;
    }
?>
</body>
