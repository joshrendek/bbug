<?php
  defined('__bbug') or die();
  // thanks to PHPChess.com for the suggestions
  $viewurl = substr($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],"?") );
  //echo $viewurl;
  if((REGISTERED == 1 && $this->user->getUID() != 0) || !REGISTERED){      // if registered is set in config, check, otherwise if its not set its still public / havnt added line
  $reportedby = $this->user->getUID();
  $bugView = new View($this->db); 
  if(isset($_POST["submitReport"])){
  
  if($_POST["type"] == "bug")
    $type = 0;
  else
    $type = 1;
  $bugData = array('id' => 'null', 'project' => $_POST["project"], 'parent' => 0, 'title' => strip_tags($_POST["subject"]), 
        'report' => $_POST["report"], 'status' => '1', 'by' => $reportedby, 'priority' => $_POST["priority"], 
        'type' => $type, 'started' => time(), 'finished' => '', 'due' => '', 'assigned' => '');
                $this->db->query_insert('list', $bugData);
                $this->message("<center><h3>Report submitted.</h3></center>");
  
  $LASTID = $this->db->lastID();
  //echo $lastID;
  // send emails to administrators
  $this->db->query("SELECT * FROM users WHERE `acl`='0'");
  $message = "You have a new bug/feature in your tracking system. 
  
  Please visit http://".$viewurl."?cmd=view&id=$LASTID to view it.";
  while($r = $this->db->fetch_array()){
    $this->user->mailUser($r["id"], 'BlueBug', $message, 'New Bug/Feature in BlueBug');
  }
  
  }
?>

<link type="text/css" href="js/jquery.wysiwyg.css" rel="stylesheet">
<div id="submitForm" align="">
<form name="" method="POST" action="">
<table width="90%" cellspacing="2" align="center">
<tr>
<td colspan="2"><div id="headings">Create New A New Ticket</div>
</td>
</tr>

<tr>
	<td valign="top" width="50%">
		<table width="100%" cellspacing="2" align="center">
			<tr>
				<td><label for="subject" >Title</label></td>
			</tr>
			<tr>
				<td><input type="text" class="input" name="subject" /></td>
			</tr>
			<tr>
				<td><label for="subject" >Description of feature/issue</label></td>
			</tr>
			<tr>
				<td><textarea name="report" class="textarea"></textarea></td>
			</tr>
		</table>
	</td>
	<td valign="top" width="50%">
		<table width="100%" cellspacing="2" align="center">
			<tr>
				<td><label for="type">Ticket Type</label></td>
			</tr>
			<tr>
				<td><select class="select" name="type" ><option value="bug">Bug</option><option value="feature">Feature</option></select>
</td>
			</tr>
			<tr>
				<td><label for="priority">Priority</label></td>
			</tr>
			<tr>
				<td><select class="select" name="priority"  ><option value="3">Low</option>
					<option value="2">Moderate</option>
					<option value="1">Urgent</option></select>
				</td>
			</tr>
			<tr>
				<td><label for="project">Project</label></td>
			</tr>
			<tr>
				<td><select name="project" class="select"><? echo $bugView->listProjects();?></select></td>
			</tr>
		</table>
	</td>
</tr>

<tr>
	<td colspan="2">
	<div id="working"><img src="loader.gif" id="loader" /> <b>Working...</b></div> 
<div align="left">
<input type="submit" name="submitReport" value="Submit Report" onclick="$('#working').fadeIn(); document.getElementById('working').style.visibility='visible';"> or <a href="?">cancel</a></div>

	</td>
</tr>

</table>

</form><?php  ?>
<?php }else { echo "The administrator has required registration to submit bugs."; } ?>