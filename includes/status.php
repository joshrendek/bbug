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
  			$displayedDates = array();
  			$q = $this->db->query("SELECT * FROM status_ups ORDER BY `time` DESC LIMIT 15");
  			while($r = mysql_fetch_array($q)){
  			$title = $this->db->first("SELECT `title` FROM list WHERE `id`='".$r['_id']."'");
  			$b = ""; # before ticket title
  			$a = ""; # after ticket title
  			
  			$date = date('l M Y', $r['time']);
  			$user_s = $this->user->uidToName($r["by"]);
  			$url = "?cmd=view&id=".$r['_id'];
  				$verb = "created";
  				if($r['type']=='update')
  					$verb = "updated";
  				elseif($r['type']=='closed'){
  					$verb = "closed"; $b = "<strike>"; $a = "</strike>";
  				}elseif($r['type']=='reopened')
  					$verb = "reopened";
  				elseif($r['type']=='edit')
  					$verb = 'edited';
  				elseif($r['type']=='git'){
  					$verb = "committed";
  					$title = $this->db->first("SELECT `message` FROM commits WHERE `id`='".$r['_id']."'");	
  					$user_s = str_replace('@', '(at)',$this->db->first("SELECT `user` FROM commits WHERE `id`='".$r['_id']."'"));
  					$url = "?commit=".$r['_id'];
  				}
  				
  				
  				
  			?>
  			<table width="95%" align="center" cellspacing="0" cellpadding="0" class="tableup">
  			  			<tr>
  			<td><?php 
  				if(!in_array($date, $displayedDates))
	  				echo "<div class='date'><b>".$date."</b></div>";
	  			else
	  				echo "<div class='date'>".date('h:mA', $r['time'])."</div>";
	
	  			 $displayedDates[] = $date;

  			?>
			</td>
			<td width="100%">
  			<div class='update' width="100%">
  			
			<?php echo $b; ?><a href='<?php echo $url; ?>'>"<?php echo $title; ?>"</a><?php echo $a; ?> was <?php echo $verb; ?> by 
			<?php echo $user_s;?>.</div>
			</td>
			<td>
  			<div class='statustype' id='statustype'><?php echo $r['type']; ?></div>
			</td>  
			</tr>
			<?php } ?>
			</table>
  		</td></tr></table>
  		<?php
  	}
  	
  	function n($id, $by, $type, $project, $time=null){ #new status
  		if($time == null)
  			$time = time();
  		$this->db->query_insert('status_ups', array('id' => 'null', '_id' => $id, 'by' => $by, 'type'=>$type, 'project' => $project, 'time' => $time ) );
  	}
  	
  }
  
?>