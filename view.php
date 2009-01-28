<?php
  defined('__bbug') or die(); 
  $bugid = $this->db->clean($_GET["id"], '', '');
  $reportedby = $this->user->getUID();
  
  $bugView = new View($this->db);
  
   if(isset($_POST["submitReport"])){ 
  $bugData = array('id' => 'null', 'project' => $this->db->first("SELECT `project` FROM list WHERE `id`='$bugid'", 0, 0),
  'parent' => $bugid, 'title' => $_POST["subject"], 
        'report' => $_POST["report"], 'status' => '', 'by' => $reportedby, 'priority' => 0, 
        'type' => 0, 'started' => time(), 'finished' => '', 'due' => '', 'assigned' => '');
                $this->db->query_insert('list', $bugData);
                $this->message("<center><h3>Reply added.</h3></center>");
  
  }
?>  
<?php 
// view original ticket 

$bugView->original($bugid);

$bugView->responses($bugid);

$bugView->reply($bugid);
?>
<div class="clear"></div>