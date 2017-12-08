<html>
<head>
<meta name=viewport content="width=device-width, initial-scale=1">
<script src="../lib/scripts/jquery-2.2.3.min.js"></script>
<script src="../lib/scripts/moment.js"></script>
<link rel="stylesheet" href="../style.css" />

    <link href="../lib/themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />

<script src="../lib/scripts/jquery-ui.1.11.4.min.js"></script>
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
             $('input[name="last_date"]').datepicker({dateFormat: "yy-mm-dd"});
             $('input[name="first_date"]').datepicker({
                     dateFormat: "yy-mm-dd",
                                          //                     onSelect: function (date) { SetLastDate(date); }
                                          });

             $('#plus-one-year').click(function() {
                 ChangeOneYear('first_date','plus');
                 ChangeOneYear('last_date','plus');
             });
             $('#minus-one-year').click(function() {
                 ChangeOneYear('first_date','minus');
                 ChangeOneYear('last_date','minus');
             });
         });
         function SetLastDate(date) { 
             $('input[name="last_date"]').datepicker("destroy").datepicker({defaultDate: date});
         }
     }
     
     function ChangeOneYear(fieldName,direction) {
         if (direction == 'plus') { increment = 1; }
         if (direction == 'minus') { increment = -1; } 
         var field = $('input[name='+fieldName+']');
         var value = $(field).val();
         var format = 'YYYY-MM-DD';
         var newDate = moment(value, format).add(increment, 'year').format(format);
         $(field).val(newDate);
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
                   $.ajax({url: 'ajax-admin.php?action=show-timeframe&preset_id='+$(this).attr('data-preset-id')+'&timeframe_id='+$(this).attr('data-timeframe-id'), success: function(result) {
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

               if (! $('.debug').length ) {
                   $('#toggle-wrapper').hide();
               }
               else {
                   $('#toggle-wrapper').click(function() {
                       $('.debug').toggle();
                   });
               }
           });
</script>
</head>
<body id="edit">
<div id="wrapper">
<div id="content">
<?
include ("../hours.class.php");
include ("admin.class.php");
include ("nav.php");

$hours = new Hours();
$admin = new HoursAdmin(); 

if (is_array($_REQUEST['action'])) {
    if (in_array('delete_timeframe',$_REQUEST['action'])) {
        if ($hours->DeleteTimeframe($_REQUEST['timeframe_id'])) {
            print '<h2>Success: Timeframe deleted</h2>';
        }
        else {
            print '<h2 class="error">Error: Could not delete timeframe</h2>';
        }
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
        if (in_array('submit_preset_values',$_REQUEST['action'])) {
            $hours->UpdatePreset(json_encode($_REQUEST));
        }
    }
    print '<div id="toggle-wrapper" class="button-wrapper"><div id="toggle-debug-button" class="button">Show/Hide Debugging Information</div></div>'.PHP_EOL;
}
else { 
    $times = $hours->GetTimeframesAndRanks();
    //$presets = $hours->getJSON('presets');
    $exceptions = $hours->getJSON('exceptions');
    $admin->TimeframePicker($times);
    $graphJS = $admin->BuildGraphJS($times,$exceptions);
?>
<div id="preset-details"></div>
<?php 
    print $graphJS;
?>
<h2 style="text-align:center">Timeline of Date Settings by Rank</h2>
<div class="flot-container">
	<div id="placeholder" class="flot-placeholder"></div>
</div>
<?php
    }
?>

<?php 
    if (sizeof($_REQUEST) > 0) {
        print '<hr>'.PHP_EOL;
        print '<a href="'.$_SERVER['SCRIPT_NAME'].'">Clear</a>'.PHP_EOL;
    }
?>
</div><!--id=content-->
<div id="footer">
<?php include('../license.php'); ?>
</div><!--id=footer>
</div><!--id=wrapper-->
</body>
