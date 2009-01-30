<?php
  defined('__bbug') or die(); 
  $commentid = $this->db->clean($_GET["commentid"], '', '');
  $parentid = $this->db->clean($_GET["parent"], '', '');
  
  $bugView = new View($this->db);
  
   
?>  
<?php 
// view original ticket 

$bugView->edit($parentid, $commentid);
?>
<div class="clear"></div>