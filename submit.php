<?php
  defined('__bbug') or die();
  // thanks to PHPChess.com for the suggestions
  $viewurl = substr($_SERVER[SERVER_NAME].$_SERVER[REQUEST_URI], 0, strrpos($_SERVER[SERVER_NAME].$_SERVER[REQUEST_URI],"?") );
  //echo $viewurl;
  if((REGISTERED == 1 && $this->user->getUID() != 0) || !REGISTERED){      // if registered is set in config, check, otherwise if its not set its still public / havnt added line
  $reportedby = $this->user->getUID();
  $bugView = new View($this->db); 
  if($_POST[submitReport]){
  
  if($_POST[type] == "bug")
    $type = 0;
  else
    $type = 1;
  $bugData = array('id' => 'null', 'project' => $_POST[project], 'parent' => 0, 'title' => strip_tags($_POST[subject]), 
        'report' => $_POST[report], 'status' => '1', 'by' => $reportedby, 'priority' => $_POST[priority], 
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
    $this->user->mailUser($r[id], 'BlueBug', $message, 'New Bug/Feature in BlueBug');
  }
  
  }
?>

<link type="text/css" href="js/jquery.wysiwyg.css" rel="stylesheet">
<div id="submitForm" align="">
<form name="" method="POST" action="">
<div style="width: 400px;">
<table width="100%" cellspacing="2">
<tr>
<td>
<b>Subject:</b>
</td><td>
<input type="input" name="subject" />
</td><tr>
<td><b>Reported by: </b></td>
<td>
<?php
  if($_SESSION[userName] == "") echo "Anonymous";
        else echo $this->user->uidToName($reportedby);
?>
<br/><small>IP Address: <?=$_SERVER[REMOTE_ADDR];?></small>
</td>
</tr>
<tr>
<td width=""><b>Type: </b> 
<select name="type" ><option value="bug">Bug</option><option value="feature">Feature</option></select>
</td><td>  <b>Priority:</b> 
<select name="priority"  ><option value="3">Low</option>
<option value="2">Moderate</option>
<option value="1">Urgent</option></select>
</td>
</tr>
<tr>
     <td><b>Submission Date:</b><br /><?=date("D, F d Y h:m:s A T");?></td>
     <td><b>Project:</b>
     <select name="project"><?=$bugView->listProjects();?></select></td>
</tr>
</table>          
 <!-- tinyMCE --> 
 <!-- mainly for FF , ie seems to cache better -->
 <center><div id="editor"><img src="loader.gif" id="loader" /> <br /><b>Editor loading...</b> </div> </center>
</div><table align="center" width="600"><tr><td>
<link type="text/css" href="js/jquery.wysiwyg.css" rel="stylesheet"> 
<textarea name="report" id="report" style="width: 600px; " cols="80" rows="20"><? /*htmlspecialchars("<p><b>Summary:</b>

<br>
<br>
<br>
 </p>  
<p><b>Steps to Reproduce:</b> <br><br><br>    </p>  
 
<p><b>Additional Information:</b><br><br><br>  </p>
 
"); */?>


</textarea><script>
$(document).ready(function() { $('#editor').hide(); $('#report').wysiwyg(); } );</script>  </td></tr></table>
<div class="clear"></div>

</div>
<div id="working"><img src="loader.gif" id="loader" /> <b>Working...</b></div> 
<input type="submit" name="submitReport" value="Submit Report" onclick="$('#working').fadeIn(); document.getElementById('working').style.visibility='visible';">
</form><?php  ?>
<?php }else { echo "The administrator has required registration to submit bugs."; } ?>