<html>
<head>
<title>Documentation - Suma Retroactive Data Importer</title>
<link rel="stylesheet" type="text/css" href="../style.css">
<meta name=viewport content="width=device-width, initial-scale=1">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">

    <link href="../lib/themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript">
     $(function() {
         $('#nav-buttons li').button();
     });
</script>
</head>

<body>

<?php
     include ("nav.php");
$file =  file_get_contents("../README.md");
// crop the first line out so we can use customized header
$lines = explode("\n", $file);
//$file = implode("\n", array_slice($lines, 2));
print (RenderMarkdown($file));
?>
</body>
</html>

<?
function PostJSON ($url, $json) {
include ("../config.php");
if (function_exists('curl_version')) {
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json', 
        'Content-Length: ' . strlen($json),
        'User-Agent: LibraryHoursManager',
        )
                ); 
$result = curl_exec($ch);
return $result;
} //end if curl_version exists
else {
return "CURL is not available in this PHP installation";
}
} //end function PostJSON

function RenderMarkdown ($text) {
if (function_exists('curl_version')) {
    $api="https://api.github.com/markdown";
    $array = array ( "mode" => "markdown",
                          "text" => $text
    );
    $json = json_encode($array);
    $html = PostJSON($api, $json);
    return $html;
}
else { return "<pre>$text</pre>"; }
}

?>