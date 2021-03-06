<html>
  <head>
   <script src="../lib/scripts/jquery-2.2.3.min.js"></script>
   <link href="../lib/themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />
   <script src="../lib/scripts/jquery-ui.1.11.4.min.js"></script>
   <script src="../lib/scripts/jtable-2.4/jquery.jtable.js" type="text/javascript"></script>
   <link href="../lib/scripts/jtable-2.4/themes/lightcolor/blue/jtable.css" rel="stylesheet" type="text/css" />
   <link rel="stylesheet" href="../style.css" />
  </head>
  <body>
<div id="wrapper">
<div id="content">
<?php include ("nav.php");  ?>

	<div id="ExceptionsTableContainer" style="width: 700px;"></div>
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
						width: '25%'
					},
					closetime: {
						title: 'Close Time',
						width: '25%'
					},
					latenight: {
						title: 'Late Night',
						width: '25%',
                                          options: { 'N': 'No', 'Y': 'Yes'}
                                      },
					closed: {
						title: 'Closed',
						width: '5%',
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

     <?php
     require_once('admin.class.php');
require_once('../hours.class.php');
$hours = new Hours();
$admin = new HoursAdmin();
$times = $hours->GetTimeframesAndRanks();
$exceptions = $hours->getJSON('exceptions');
$graphJS = $admin->BuildGraphJS($times,$exceptions);
print '<!--begin graphJS-->'.PHP_EOL;
print $graphJS;
print '<!--end graphJS-->'.PHP_EOL;
?>
<h2 style="text-align:center">Timeline of Date Settings by Rank</h2>
<div class="flot-container">
	<div id="placeholder" class="flot-placeholder"></div>
</div>


</div><!--id=content-->
<div id="footer">
<?php include('../license.php'); ?>
</div><!--id=footer>
</div><!--id=wrapper-->

  </body>
</html>
