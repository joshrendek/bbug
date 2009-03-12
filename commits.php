<?php
defined('__bbug') or die();

include('integration/fsock.php');
include('integration/xml.php');

$id = $_GET['commit'];
if(!is_numeric($id))
	$id = $sum = $this->db->first("SELECT `id` FROM commits WHERE `sum`='$id'");
	
$project = $this->db->first("SELECT `project` FROM commits WHERE `id`='$id'");
$sum = $this->db->first("SELECT `sum` FROM commits WHERE `id`='$id'");

$url = $this->db->first("SELECT `github` FROM projects WHERE `id`='$project'");

?>

<table width="95%" align="center" id="scm" cellspacing="0" cellpadding="0">
  		<tr>
  		<td>
  			<h3>Diff's For <?php echo $sum; ?></h3>
<?php 
$x = explode('/', $url);
$results = "";
$user_proj = $x[3]."/".$x[4];
//Given: http://github.com/bluescripts/bbug/commits/master
//Need: http://github.com/api/v1/xml/bluescripts/bbug/commits/master
$fp = fsockopen("github.com", 80, $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    $out = "GET /api/v1/xml/".$user_proj."/commit/".$sum." HTTP/1.1\r\n";
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
$g = $arrOutput[0]['children'][3]['children'];
for($i = 0; $i<count($g); $i++){
	
	$file = $g[$i]['children'][1]['tagData'];
	$diff = $g[$i]['children'][0]['tagData'];
	$diff = str_replace('>', '&gt;', str_replace('<', '&lt;', $diff));
	# do highlighting
	$diff = preg_replace('/\+(.*)/', '<span id="add">$1</span>', $diff);
	$diff = preg_replace('/\-(.*)/', '<span id="remove">$1</span>', $diff);

	echo "<div id='file'>$file</div>";
	echo "<div id='diff'>".nl2br($diff)."</div><hr>";

}
//$arrOuptut = array_map()
//$diff = $arrOutput[]
//echo "<pre>";
	//print_r();
//echo "</nohtml>";

$github_result = $arrOutput[0]['children'];

?>
</td></tr></table>