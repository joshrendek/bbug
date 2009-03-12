<?php
define('__bbug', 1);
error_reporting(0);

// only allow from cron
if(isset($_SERVER['REMOTE_ADDR'])){
	die("cmd line only\n");
}

include('includes/main.php');
include('config.php');
include('includes/db.php');
include('includes/bug.php');
include('includes/user.php');
include('includes/todo.php');
include('includes/status.php');
include('integration/fsock.php');
include('integration/xml.php');

$mydb = new Database($db['host'], $db['user'], $db['pass'], $db['db'], '', 20);
$mydb->NewConnection();
$usr = new User($mydb);
$s = new Status($mydb, $usr);
$q = $mydb->query("SELECT * FROM `projects` WHERE `github`!='' ");
while($r = mysql_fetch_array($q)){
echo "...\n";
$url = $r["github"];
echo $url;
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
	$commiter = $github_result[$i]['children'][3]['children'][0]['tagData']."/".$github_result[$i]['children'][3]['children'][1]['tagData'];
	$unix_stamp = strtotime($date);
	echo "$message [$commit_id] was commited by $commiter on ".$unix_stamp." \n";
	if(strlen($mydb->first("SELECT `sum` FROM commits WHERE `sum`='$commit_id'")) == 0){
		$ar = array('id' => null, 'user' => $commiter, 'message' => $message, 'sum' => $commit_id, 'project' => $r['id']);
		$mydb->query_insert('commits', $ar);
		
		$LASTID = $mydb->lastID();  

		$s->n($LASTID, '', 'git', $r['id'], $unix_stamp);

	}
	//echo "<a href='#' onclick=\"insertAtCursor(document.reply.report, '[GIT]".$commit_id."[/GIT]');\" title=\".". $date . "\">" .$message . "</a><small>[" . $commit_id . "]</small>";

endfor;
}
?>
