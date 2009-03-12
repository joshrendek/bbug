<?php

include('fsock.php');
include('xml.php');

$url = $_GET["url"];
$x = explode('/', $url);
$results = "";
$user_proj = $x[3]."/".$x[4];
//Given: http://github.com/bluescripts/bbug/commits/master
//Need: http://github.com/api/v1/xml/bluescripts/bbug/commits/master
$fp = fsockopen("github.com", 80, $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    $out = "GET /api/v1/xml/".$user_proj."/commits/master HTTP/1.1\r\n";
    $out .= "Host: google.com \r\n";
    $out .= "Connection: Close\r\n\r\n";
    fwrite($fp, $out);
    while (!feof($fp)) {
        $results .= fgets($fp, 128);
    }
    fclose($fp);
}

$post_results = parseHttpResponse($results);


$objXML = new xml2Array();
$arrOutput = $objXML->parse($post_results);

$github_result = $arrOutput[0]['children'];

for($i = 0; $i<count($github_result); $i++):

	$commit_id = $github_result[$i]['children'][4]['tagData'];
	$message = $github_result[$i]['children'][0]['tagData'];
	$date = str_replace('T', ' T ', $github_result[$i]['children'][5]['tagData']);
	echo "<a href='#' title=\".". $date . "\">" .$message . "</a><small>[" . $commit_id . "]</small>";

endfor;

?>