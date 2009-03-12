<?php
  defined('__bbug') or die(); 
  
  class Status { 
  	
  	var $db = null;
  	var $user = null;
  	function Status($db, $user){ $this->db=$db; $this->user=$user;} 
  	
  	function display() {
  		?>
  		<table width="95%" align="center" id="status" cellspacing="0" cellpadding="0">
  		<tr>
  		<td>
  			<h3>Status Updates</h3>
  			<?php
  			$q = $this->db->query("SELECT * FROM status_ups ORDER BY `id` DESC LIMIT 10");
  			while($r = mysql_fetch_array($q)){
  			$title = $this->db->first("SELECT `title` FROM list WHERE `id`='".$r['_id']."'");
  			$b = ""; # before ticket title
  			$a = ""; # after ticket title
  			
  				$verb = "created";
  				if($r['type']=='update')
  					$verb = "updated";
  				elseif($r['type']=='closed'){
  					$verb = "closed"; $b = "<strike>"; $a = "</strike>";
  				}	
  				
  				
  				
  			?>
  			<div class='update'>
			<?php echo $b; ?><a href='?cmd=view&id=<?php echo $r['_id']; ?>'>"<?php echo $title; ?>"</a><?php echo $a; ?> was <?php echo $verb; ?> by 
			<?php echo $this->user->uidToName($r["by"]);?>.
  			<div class='statustype' id='statustype'><?php echo $r['type']; ?></div>
  			</div>
  			<div style="clear: both;"></div>
  			<?php } ?>
  		</td></tr></table>
  		<?php
  	}
  	
  	function n($id, $by, $type, $project){ #new status
  		$this->db->query_insert('status_ups', array('id' => 'null', '_id' => $id, 'by' => $by, 'type'=>$type, 'project' => $project, 'time' => time() ) );
  	}
  	
  }
  
?>