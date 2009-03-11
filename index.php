<?php
session_start();
define('__bbug', 1);

include('includes/main.php');
include('config.php');
include('includes/db.php');
include('includes/bug.php');
include('includes/user.php');
include('includes/todo.php');

define('REGISTERED', $config["registered"]);
$mydb = new Database($db['host'], $db['user'], $db['pass'], $db['db'], '', 20);
$mydb->NewConnection();

$main = new Main($mydb);    
$main->headStart();

?>

	
	<div class="clear"></div>
	<div align="center">
		
		
		<table width="100%" align="center" id="main_bb" cellspacing="0" cellpadding="0">
		<tr><td align="center"></td></tr>
			<tr><td id="mainbody"><?php $main->body(); ?></td>
			<td width="300" id="sidenav"><?php $main->nav(); ?></td>
			</tr>
			
		<tr><td align="center"></td></tr>
		</table>
		
		
	</div>
<?php

  $main->footStart();  
?>