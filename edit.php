<html>
<head>
<meta name=viewport content="width=device-width, initial-scale=1">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<link rel="stylesheet" href="style.css" />
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">

    <link href="lib/themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript">
     function BindTimeframeFields() {
         $(function() {
             $('select[name="use_preset"]').change(function() {
                 $.ajax({url: 'ajax-admin.php?action=show-preset&id='+$(this).val(), success: function(result) {
                     $('#settings-placeholder').html(result);
                 },
                  complete: function () {
                               BindSettingsFields();
                  }
                  });
             });
             
             $('input[name="first_date"]').datepicker();
             $('input[name="last_date"]').datepicker();
         });
     }
     
     function BindSettingsFields() {
         $(function() {
             $('#edit-settings-button').click(function() {
                 var preset_id = $(this).attr('data-preset-id');
                 $('#edit-settings-button').hide();
                 $.ajax({url: 'ajax-admin.php?action=edit-preset&id='+preset_id, success: function (result) {
                     $('#show-or-edit-settings').html(result);
                 }
                  });

             });
         });

     }

     
           $(function() {
               $('#nav-buttons li').button();
             
               $('#timeframe-picker tr').click(function() {
                   $(this).parent().children().removeClass('highlight');
                   $(this).addClass('highlight');
                   $.ajax({url: 'ajax-admin.php?action=show-timeframe&id='+$(this).attr('data-preset-id')+'&timeframe_id='+$(this).attr('data-timeframe-id'), success: function(result) {
                       $('#preset-details').html(result);
                       BindTimeframeFields();
                       var usepreset = $('select[name="use_preset"]').val();
                 $.ajax({url: 'ajax-admin.php?action=show-preset&id='+usepreset, success: function(result) {
                       $('#settings-placeholder').html(result);
                       BindSettingsFields();
                 }});
                   }});
               });

               $('#new-timeframe-button').click(function() {
                   $.ajax({url: 'ajax-admin.php?action=new-timeframe', success: function(result) {
                       $('#preset-details').html(result);
                       BindTimeframeFields();
                   }});


               });
               
               $('.delete-timeframe-button').click(function(event) {
                   var setting_name = $(this).parent().parent().children(':first-child').text();
                   var r = confirm('Really delete this timeframe ('+setting_name+')?');
                   if (r == false) {
                       event.preventDefault();
                       event.stopPropagation();
                   }
               });
               
               $('#manage-exceptions-button').click(function() {
                   location.href = 'manage_exceptions.php';
               });

           });
</script>
</head>
<body id="edit">
<?
include ("hours.class.php");
include ("admin.class.php");
include ("nav.php");

$hours = new Hours();
$admin = new HoursAdmin(); 

if (is_array($_REQUEST['action'])) {
    if (in_array('delete_timeframe',$_REQUEST['action'])) {
        $hours->DeleteTimeframe($_REQUEST['timeframe_id']);
    }
    elseif (in_array('manage_exceptions',$_REQUEST['action'])) {
        $hours->ManageExceptions();
    }
    else {
        $preset_id = '';
        if (in_array('submit_new_preset', $_REQUEST['action'])) {
            $preset_id = $hours->UpdatePreset(json_encode($_REQUEST));
        }
        if (in_array('submit_settings_details',$_REQUEST['action'])) {
            $hours->UpdateSettings(json_encode($_REQUEST),$preset_id);
        }
        if (in_array('submit_timeframe_details', $_REQUEST['action'])) {
            $hours->UpdateTimeframe(json_encode($_REQUEST), $preset_id);
        }
        /*

        if (in_array('submit_preset_values',$action)) {

        }
        elseif (in_array('submit_new_preset', $action)) {
            $hours->UpdateTimeframe(json_encode($_REQUEST));
        }
        */
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
