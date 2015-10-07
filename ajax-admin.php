<?
include ('hours.class.php');
include ('admin.class.php');
$hours = new Hours;
$admin = new HoursAdmin;
if (isset($_REQUEST['action'])) {
    switch ($_REQUEST['action']) {
    case 'show-preset':
        $details = $hours->GetPresetDetails($_REQUEST['id']);
        print ($admin->EditPresetDetails($details));
    }
}
?>