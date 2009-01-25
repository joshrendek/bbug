<?php
session_start();
define('__bbug', 1);

include('includes/main.php');
include('config.php');
include('includes/db.php');
include('includes/bug.php');
include('includes/user.php');
include('includes/todo.php');

define('REGISTERED', $config[registered]);
$mydb = new Database($db['host'], $db['user'], $db['pass'], $db[db], '', 20);
$mydb->NewConnection();

$main = new Main($mydb);    
$main->headStart();


?>

	
	<div class="clear"></div>
	<div align="center">
		
		
		<div id="contentArea" width="91%"><?php $main->body(); ?></div>
		
		
	</div>
<?php

  $main->footStart();  
?>