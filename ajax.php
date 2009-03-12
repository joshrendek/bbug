<?php
 define('__bbug', 1);
include('includes/main.php');
include('config.php');
include('includes/db.php');
include('includes/status.php');
include('includes/bug.php');
include('includes/user.php');
$mydb = new Database($db['host'], $db['user'], $db['pass'], $db['db'], '', 20);
   $mydb->NewConnection();
$main = new Main($mydb);
$user = new User();
$s = new Status($mydb, $user);
//print_r($_POST);
// test
$userName = $_POST["username"];
$passWord = $_POST["password"];
$_uid = $mydb->first("SELECT `id` FROM `users` WHERE `username`='$userName'");         
                $adminCheck = $mydb->first("SELECT `acl` FROM `users` WHERE `username`='$userName' AND `password`='".md5($passWord)."';");
                if($adminCheck === "0")
                    $isadmin = 1;
                elseif(!$adminCheck || $adminCheck== "")
                    $isadmin = 0;
                else
                    $isadmin = 0;
                    
                    //echo $adminCheck;
                
if(isset($_POST["assignto"]) && $isadmin == 1){
 $assignto = $mydb->clean($_POST["assignto"], '', '');
 $assignedname = $mydb->first("SELECT username FROM users WHERE id='$assignto'");
 $tickid = $mydb->clean($_POST["tickid"], '', '');
 $mydb->query_update('list', array('assigned' => $assignto), "id='$tickid'"); 
// echo $isadmin;
 echo $assignedname;  
 
 // send emails to assignee
  $mydb->query("SELECT * FROM users WHERE `id`='$assignto'");
  $message = "You have been assigned a new ticket.
  
  Please visit ".BBPATH."/?cmd=view&id=$tickid to view it.";
  while($r = $mydb->fetch_array()){
    $user->mailUser($r["id"], 'BlueBug', $message, 'You have been assigned to a ticket');
  }
}

if(isset($_POST["titlechange"]) && $isadmin == 1){
	$tickid = $mydb->clean($_POST["tickid"], '', '');
	$mydb->query_update("list", array('title' => $_POST["titlechange"]), "id='$tickid'");
	echo "Title changed.";
}

if(isset($_POST["closeticket"]) && $isadmin == 1){
 $closeticket = $mydb->clean($_POST["tickid"], '', '');
 $mydb->query_update('list', array('status' => 0, 'finished' => time() ), "id='$closeticket'");
 echo "Ticket Closed";   
 
 $projectID = $mydb->first("SELECT `project` FROM list WHERE `id`='".$_POST['tickid']."'");
 $s->n($_POST["tickid"], $_uid, 'closed', $projectID);

 
 // send emails to author
      $authorID = $mydb->clean($_POST["by"], '', '');
     // echo "UID: $authorID";
      $mydb->query("SELECT * FROM users WHERE `id`='$authorID';");
      $message = "Your ticket has been resolved.
      
      Please visit http://".$_SERVER["SERVER_NAME"]."/?cmd=view&id=$closeticket to view it.";
      while($r = $mydb->fetch_array()){
        $user->mailUser($r["id"], 'BlueBug', $message, 'Your ticket has been resolved.');
         //echo "Mailed"; 
      }
     
  
}
if(isset($_POST["openticket"]) && $isadmin == 1){
 $closeticket = $mydb->clean($_POST["tickid"], '', '');
 $mydb->query_update('list', array('status' => 1, 'finished' => ''), "id='$closeticket'");
 echo "Ticket Opened";   
}
if(isset($_POST["changepri"]) && $isadmin == 1){
 $changepri = $mydb->clean($_POST["changepri"], '', '');
 $id = $mydb->clean($_POST["id"], '', '');
 $mydb->query_update('list', array('priority' => $changepri), "id='$id'");
 //print_r($_POST);   
}

// adds to do items
if(isset($_POST["addtodo"])){
	$id = $mydb->clean($_POST["id"], '', '');
	$mydb->query_insert('todo_list', array('id' => 'null', 'tid' => $_POST['id'], 'content' => $_POST['item'], 'status' => 0) );
	echo $mydb->lastID();
}
if(isset($_POST["markfinish"])){
	$mydb->query_update('todo_list', array('status' => 1), "id='".$mydb->clean($_POST["id"], '', '')."'");
}


//print_r($_POST);
?>
