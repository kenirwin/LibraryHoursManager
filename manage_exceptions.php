<html>
  <head>

    <link href="lib/themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />
	<link href="lib/scripts/jtable/themes/lightcolor/blue/jtable.css" rel="stylesheet" type="text/css" />
	
	<script src="lib/scripts/jquery-1.6.4.min.js" type="text/javascript"></script>
    <script src="lib/scripts/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
    <script src="lib/scripts/jtable/jquery.jtable.js" type="text/javascript"></script>
	
  </head>
  <body>
	<div id="ExceptionsTableContainer" style="width: 600px;"></div>
	<script type="text/javascript">

		$(document).ready(function () {
		    //Prepare jTable
			$('#ExceptionsTableContainer').jtable({
				title: 'Exceptions Table',
				actions: {
					listAction: 'handle_exceptions.php?action=list',
					createAction: 'handle_exceptions.php?action=create',
					updateAction: 'handle_exceptions.php?action=update',
					deleteAction: 'handle_exceptions.php?action=delete'
				},

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
                                          options: { 'Y': 'Yes', 'N':'No'}
                                      },
					closed: {
						title: 'Closed',
						width: '15%',
                                          options: { 'Y': 'Yes', 'N':'No'}
                                      }
                    }

                });

			//Load person list from server
			$('#ExceptionsTableContainer').jtable('load');

		});

	</script>
 
  </body>
</html>
