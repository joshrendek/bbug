<?php
session_start();
define('__bbug', 1);

$APIKEY = "13j09sdfj09j0j09j12093j0j";

include('includes/main.php');
include('config.php');
include('includes/db.php');

/* 
####### EXAMPLE WITHOUT HTACCESS
$post_data = array();
                       $post_data['apikey'] = '13j09sdfj09j0j09j12093j0j'; 
                       $post_data['project'] = '1';
                       $post_data['name'] = ' Request';
                       $post_data['report'] = "$_SERVER[SERVER_NAME] <br>".$_POST[content];
                       $post_data['by'] = $_SERVER[SERVER_NAME];
                       $post_data['priority'] = '3'; 
                       $post_data['type'] = '1'; 
                       $url = "http://domain.com/api.php";
                       $o="";
                       foreach ($post_data as $k=>$v)
                       {
                           $o.= "$k=".utf8_encode($v)."&";
                       }
                       $post_data=substr($o,0,-1);
                      
                       $ch = curl_init();
                       curl_setopt($ch, CURLOPT_POST, 1);
                       curl_setopt($ch, CURLOPT_HEADER, 0);
                       curl_setopt($ch, CURLOPT_URL, $url);   
                       curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                       $result = curl_exec($ch);
                       
####### EXAMPLE WITH HTACCESS    -- MUST HAVE PHP 5
$post_data = array();
                       $post_data['apikey'] = '13j09sdfj09j0j09j12093j0j'; 
                       $post_data['project'] = '1';
                       $post_data['name'] = 'MyGuildHost Request';
                       $post_data['report'] = "$_SERVER[SERVER_NAME] <br>".$_POST[content];
                       $post_data['by'] = $_SERVER[SERVER_NAME];
                       $post_data['priority'] = '3'; 
                       $post_data['type'] = '1'; 
                       $url = "http://myguildhost.com/dev/api.php";
                       $o="";
                       foreach ($post_data as $k=>$v)
                       {
                           $o.= "$k=".utf8_encode($v)."&";
                       }
                       $post_data=substr($o,0,-1);
                      
                       $ch = curl_init();
                       curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                       curl_setopt($ch, CURLOPT_USERPWD, 'mgh:mgh!@#'); 
                       curl_setopt($ch, CURLOPT_POST, 1);
                       curl_setopt($ch, CURLOPT_HEADER, 0);
                       curl_setopt($ch, CURLOPT_URL, $url);   
                       curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                       $result = curl_exec($ch);

*/
define('REGISTERED', $config[registered]);
$mydb = new Database($db['host'], $db['user'], $db['pass'], $db[db], '', 20);
$mydb->NewConnection();

if($_POST[apikey] == $APIKEY){
  $bugData = array('id' => 'null', 'project' => $_POST[project], 'parent' => 0, 'title' => $_POST[name], 
        'report' => mysql_escape_string($_POST[report]), 'status' => '1', 'by' => $reportedby, 'priority' => $_POST[priority], 
        'type' => $type, 'started' => time(), 'finished' => '', 'due' => '', 'assigned' => '');
        $mydb->query_insert("list", $bugData);
}else{
    echo "Fail";
}
    

?>