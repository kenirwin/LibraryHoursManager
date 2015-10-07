<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript">
           $(function() {
               $('#presets-picker tr').click(function() {
                   $.ajax({url: 'ajax-admin.php?action=show-preset&id='+$(this).attr('data-preset-id'), success: function(result) {
                       $('#preset-details').html(result);
                   }});
               });
           });
</script>
<?
include ("hours.class.php");
include ("admin.class.php");

$hours = new Hours();
$admin = new HoursAdmin(); 

if (isset($_REQUEST['action'])) {
    switch ($_REQUEST['action']) {
    case ('submit_preset_values'):
        print_r($_REQUEST);
        break;
    }
}
else { 
    $times = $hours->GetTimeframesAndRanks();
    //$presets = $hours->getJSON('presets');
    $exceptions = $hours->getJSON('exceptions');
    $admin->PresetsPicker($times);
}
?>
<div id="preset-details"></div>

