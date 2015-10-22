<html>
  <head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/redmond/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script src="lib/scripts/jtable/jquery.jtable.js" type="text/javascript"></script>
	<link href="lib/scripts/jtable/themes/lightcolor/blue/jtable.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
<? include ("nav.php");  ?>

	<div id="ExceptionsTableContainer" style="width: 600px;"></div>
	<script type="text/javascript">

		$(document).ready(function () {
            $('#nav-buttons li').button();
		    //Prepare jTable
			$('#ExceptionsTableContainer').jtable({
				title: 'Exceptions Table',
                                          selecting: true, //Enable selecting
                                          multiselect: true, //Allow multiple selecting
                                          selectingCheckboxes: true, //Show checkboxes on first column

				actions: {
					listAction: 'handle_exceptions.php?action=list',
					createAction: 'handle_exceptions.php?action=create',
					updateAction: 'handle_exceptions.php?action=update',
					deleteAction: 'handle_exceptions.php?action=delete'
				},
                                          messages: {addNewRecord: '+ Add new Exception'},
				fields: {
                    except_id: {
                            key: true,
                                      create: false,
						edit: false,
						list: false

                                      },
					date: {
                            title: 'Date',
						type: 'date',
                                      width: '20%',
					},
					opentime: {
						title: 'Open Time',
						width: '22%'
					},
					closetime: {
						title: 'Close Time',
						width: '22%'
					},
					latenight: {
						title: 'Late Night',
						width: '20%',
                                          options: { 'N': 'No', 'Y': 'Yes'}
                                      },
					closed: {
						title: 'Closed',
						width: '15%',
                                          options: { 'N': 'No', 'Y': 'Yes'}
                                      }
                    }

                });

			//Load list from server
			$('#ExceptionsTableContainer').jtable('load');

            //Delete selected 
            $('#DeleteSelectedButton').button().click(function () {
                var $selectedRows = $('#ExceptionsTableContainer').jtable('selectedRows');
                $('#ExceptionsTableContainer').jtable('deleteRows', $selectedRows);
            });

		});

	</script>

<div id="DeleteSelectedButton">Delete Selected Rows</div>

     <?
     require_once('admin.class.php');
require_once('hours.class.php');
$hours = new Hours();
$admin = new HoursAdmin();
$times = $hours->GetTimeframesAndRanks();
$exceptions = $hours->getJSON('exceptions');
$graphJS = $admin->BuildGraphJS($times,$exceptions);
print $graphJS;
?>
<h2 style="text-align:center">Timeline of Date Settings by Rank</h2>
<div class="flot-container">
	<div id="placeholder" class="flot-placeholder"></div>
</div>

  </body>
</html>
