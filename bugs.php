<?php
  defined('__bbug') or die();
  
?>

<?php
$list = new Bugs($this->db);
$list->tableHeader();
$list->bblist(0); 
?>
