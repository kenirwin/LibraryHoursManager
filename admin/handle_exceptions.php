<?
require_once('../hours.class.php');
$hours = new Hours();
switch($_REQUEST['action']) {
case 'list':
    print ($hours->ExceptionsList());
    break;
case 'create':
    print ($hours->ExceptionsCreate($_REQUEST));
    break;
case 'update':
    print ($hours->ExceptionsUpdate($_REQUEST));
    break;
case 'delete':
    print ($hours->ExceptionsDelete($_REQUEST));
    break;
}
?>