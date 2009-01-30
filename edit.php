<?php
  defined('__bbug') or die(); 
  $commentid = $this->db->clean($_GET["commentid"], '', '');
  $parentid = $this->db->clean($_GET["parent"], '', '');
  
  $bugView = new View($this->db);
  
   if(isset($_POST["submitReport"])){ 
  $bugData = array('id' => 'null', 'project' => $this->db->first("SELECT `project` FROM list WHERE `id`='$bugid'", 0, 0),
  'parent' => $bugid, 'title' => $_POST["subject"], 
        'report' => nl2br($_POST["report"]), 'status' => '', 'by' => $reportedby, 'priority' => 0, 
        'type' => 0, 'started' => time(), 'finished' => '', 'due' => '', 'assigned' => '');
                $this->db->query_insert('list', $bugData);
                $this->message("<center><h3>Reply added.</h3></center>");
  
  }
?>  
<?php 
// view original ticket 

$bugView->edit($parentid, $commentid);
?>
<div class="clear"></div>