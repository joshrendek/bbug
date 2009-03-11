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
    
  
  
  // do file uploads
  $target = "";
  $file_name = "";
  if(strlen($_FILES['attachment']['name'])>0){
  	$target = "uploads/" ;
  	
  	// get file ext
  	$file_ext = end(explode('.', $_FILES['attachment']['name']));
  	
  	// check against config values
  	$allowed_ext = explode(',', allowed_types);
  	
  	if(in_array($file_ext, $allowed_ext)){
  		$file_name = $_FILES['attachment']['name']."-".time().".".$file_ext;
  		if(move_uploaded_file($_FILES['attachment']['tmp_name'], $target.$file_name)){
  			//echo ""
  		}else{
  			echo "<center>Your ticket was submitted but the file was not attached. Ask the System Administrator to check file permissions on the upload directory.</center>";
  			$file_name = "";
  			$target = "";
  			
  		}
  	}else{
  		echo "Ticket was submitted but your attachment was denied. Not in allowed file types.";
  		$file_name = "";
  			$target = "";
  	}
  }else{
  	
  }
  
  
  $bugData = array('id' => 'null', 'project' => $_POST["project"], 'parent' => 0, 'title' => strip_tags($_POST["subject"]), 
        'report' => nl2br(strip_tags($_POST["report"])), 'status' => '1', 'by' => $reportedby, 'priority' => $_POST["priority"], 
        'type' => $type, 'started' => time(), 'finished' => '', 'due' => '', 'assigned' => '', 'attachment' => $target.$file_name);
                $this->db->query_insert('list', $bugData);
                $this->message("<center><h3>Report submitted.</h3></center>");
  $LASTID = $this->db->lastID();

  $this->s->n("<a href='?cmd=view&id=$LASTID'>\"{$_POST['subject']}\"</a> was created by ");
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
<form name="" method="POST" action="" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
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
			<tr>
				<td><label for="attachment">Attachment</label></td>
			</tr>
			<tr>
				<td><input type="file" name="attachment" /></td>
			</tr>
			<tr>
				<td>Shortcuts: <br>
				<b>[CE]ID[/CE]:</b> Link to this project's ClientExec.<br>
				<b>[GIT]Commit ID[/GIT]:</b> Link to a specific GitHub committ for this project.<br>
				</td>
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