<?php
  defined('__bbug') or die(); 
  class Bugs {
    var $db = 0;
    var $user = 0;
    var $git;
    var $clientexec; 
    function Bugs($dblink){
       $this->db=$dblink; 
       $this->user=new User();
    }
      
    function tableHeader(){
     ?>
     <script>$(document).ready(function() { $('#list').tablesorter(); } ); </script>
     <table width="95%" id="list" class="tablesorter" border="0" align="center" cellpadding="0" cellspacing="0">
      <thead>
      <tr>
              <th>#</th>
              <th>Type</th> 
              <th width="45%">Title</th>
              <th width="25">Status</th>
              <?php /* taken out to reduce clutter <th>By</th> */ ?>
              <th>Project</th>
              <th width="25">Priority</th>
              <th width="150">Started</th>
              <th>Finished</th>
              </tr>
      </thead>
      <tbody>
      
     <?php    
    }
    /* userid to name */
    function uidToName($uid){
       $name = $this->db->first("SELECT `username` FROM `users` WHERE `id`='$uid'"); 
       if($name == "") return "Anonymous";
       else return $name;   
    }
     /* take a pid and get its name */
    function ProjectIDtoName($id){
        return $this->db->first("SELECT `name` FROM projects WHERE `id`='$id'");
    }
    
    /* create the bug list */
    function bblist($type=-1){
        if(isset($_GET["page"]))
        	$page = $_GET["page"];
        else
        	$page = "";
        if(isset($_GET['specialrefiner'])){
         $_SESSION['esql'] = "";
         $srf = $_GET['specialrefiner'];
         if($srf == "open")
             $_SESSION['esql'] = "AND `status`='1'";
         elseif($srf == "closed")
             $_SESSION['esql'] = "AND `status`='0'";
         elseif($srf == "all")
            $_SESSION['esql'] = "";
         elseif(is_numeric($srf))
            $_SESSION['esql'] = "AND `project`='".$this->db->clean($srf, '', 'num')."'";
        }
        
        if(!isset($_SESSION['esql']))
        	$_SESSION['esql'] = "";
        /* Handle paging */
      /*  if( isset($_GET["page"]) )
        	$_GET["page"]=$_GET["page"];
        else
        	$_GET["page"] = 1; */
        if(isset($_GET["page"])){
            $page = ($_GET["page"]);
            $lower = ($page * $this->db->pagenums)-$this->db->pagenums;
            $limit = "LIMIT $lower,".$this->db->pagenums;
        }else $limit = "LIMIT 0,".$this->db->pagenums;
     
     
     if($page == 1) 
        $limit = "LIMIT 0,".$this->db->pagenums;
        if($type == 0)
            $this->db->query("SELECT * FROM list WHERE `type`='0' AND `parent`='0' ".$_SESSION['esql']." ORDER BY `id` DESC $limit;");
        if($type == 1)
            $this->db->query("SELECT * FROM list WHERE `type`='1' AND `parent`='0' ".$_SESSION['esql']." ORDER BY `id` DESC $limit;");
        if($type == -1)
            $this->db->query("SELECT * FROM list WHERE `parent`='0' ".$_SESSION['esql']." ORDER BY `id`  DESC $limit;"); 
       $cssclass = "L1";
      while($r = $this->db->fetch_array()){
          
          if($cssclass == "L1") 
            $cssclass = "L2";
          elseif($cssclass == "L2") 
          $cssclass = "L1";
       ?>   
         <tr class="<?php echo $cssclass;?>">
        <td align="center"><?php echo $r["id"];?></td>
        <td width="16" align="center"><div style='position: relative;'><img src="<?php echo $this->img($r["type"]);?>" style='' />
        <?php if($r["status"] == 0){ ?><img src="images/cancel.png" style='position: absolute; float: left; margin-left: -16px; opacity: .7;' /><?php } ?></div></td>
        <td><a href="?cmd=view&id=<?php echo $r["id"];?>"><?php if($r["title"] == "") echo "[No Title]"; else echo $r["title"];?></a></td>
        <td align="center"><?php if($r["status"] == 1)
                                echo "Open";
                                else
                                    echo "Closed"; ?></td>
       <?php
        /* <td> Taken out to reduce clutter 
        if($r[by] == 0) echo "Anonymous";
        else echo $this->user->uidToName($r[by]);
        </td>
        */
        ?>
        <td align="center"><?php echo $this->ProjectIDtoName($r['project']); ?></td>
        <td align="center" class="pri<?php echo $r["priority"];?>" id="<?php echo $r["priority"];?>"><?php echo $this->adminPriHover($r["id"], $r["priority"]);?></td>
        <td align="center"><?php echo $this->the_date($r["started"]);?></td>
        <td><?php
        
        if($r["finished"] == 0)
            echo "Never";
        else echo $this->the_date($r["finished"]);
        
        ?></td>
        </tr><?php 
      }
       ?>
         
       </tbody>
       <?php 
       if(REGISTERED == 0 || $_SESSION["userName"]){
       $this->quickadd(); } ?>
       </table>
       
     <div style="width: 90%;" width="90%" id="subnav">
     <div style="float: left;"> 
     <?php
     
     
     if($type == 0)
            $this->db->paginate("SELECT * FROM list WHERE `type`='0' AND `parent`='0' ".$_SESSION['esql']." ORDER BY `id` DESC;");
     if($type == 1)
            $this->db->paginate("SELECT * FROM list WHERE `type`='1' AND `parent`='0' ".$_SESSION['esql']." ORDER BY `id` DESC;");
     if($type == -1)
            $this->db->paginate("SELECT * FROM list WHERE `parent`='0' ".$_SESSION['esql']." ORDER BY `id`  DESC;"); 
            
     ?>
     </div>
     </div>
     
     <div style="clear:both;"/></div>
     <?php 
     
    }  
  /* returns date from time() */
  function the_date($timestamp){
   return date('m/d/y h:m:s A', $timestamp);
  } 
  
  /* hover menu */
   function adminPriHover($id, $current){
      $adminCheck = $this->user->adminCheck();
      if($current == 1) $current = "High";
      elseif($current == 2) $current = "Moderate";
      elseif($current == 3) $current = "Low";
   ?>
   <a href="javascript:void(<?php echo $current;?>);" id="<?php echo $id;?>PriB"><?php echo $current;?></a>
   <?php if($adminCheck == true) { ?>     
      <script>$(document).ready(function(){$('#<?php echo $id;?>Pri').hide();
$("#<?php echo $id;?>PriB").toggle(function () {$('#<?php echo $id;?>Pri').fadeIn();},function () {$('#<?php echo $id;?>Pri').hide();});
        });
      </script>
      <div id="<?php echo $id;?>Pri" class="PriMenu" style='position: absolute; z-index: 99; border: 1px dotted #ababab; margin-left: -5px; background-color: #EFEFEF; width: 50px; margin-top: -55px;'>
      <a href="javascript:;" onclick="$.post('ajax.php', { changepri: '1', id: '<?php echo $id;?>', username: '<?php echo $_SESSION["userName"];?>', password: '<?php echo $_SESSION["passWord"];?>' } ); $('#<?php echo $id;?>PriB').empty(); $('#<?php echo $id;?>PriB').append('High');">High</a>
      <a href="javascript:;" onclick="$.post('ajax.php', { changepri: '2', id: '<?php echo $id;?>', username: '<?php echo $_SESSION["userName"];?>', password: '<?php echo $_SESSION["passWord"];?>' } ); $('#<?php echo $id;?>PriB').empty(); $('#<?php echo $id;?>PriB').append('Moderate');">Moderate</a>
      <a href="javascript:;" onclick="$.post('ajax.php', { changepri: '3', id: '<?php echo $id;?>', username: '<?php echo $_SESSION["userName"];?>', password: '<?php echo $_SESSION["passWord"];?>' } ); $('#<?php echo $id;?>PriB').empty(); $('#<?php echo $id;?>PriB').append('Low');">Low</a></div> 
      <?php } ?>
    
    
   <?php   
  }
  /* returns image for type */ 
  function img($num){
    /* 0 bug; 1 feature */
    if($num == 0) return "images/smbug.png";
    if($num == 1) return  "images/feature.png";
  }  
   function quickadd(){
       if(isset($_POST["quickadd"])){
        $bugData = array('id' => 'null', 'project' => $_POST[project], 'parent' => 0, 'title' => strip_tags($_POST[title]), 
        'report' => $_POST[report], 'status' => '1', 'by' => $reportedby, 'priority' => 3, 
        'type' => $_POST[type], 'started' => time(), 'finished' => '', 'due' => '', 'assigned' => '');
        $this->db->query_insert("list", $bugData);
         // send emails to administrators
          $viewurl = substr($_SERVER[SERVER_NAME].$_SERVER[REQUEST_URI], 0, strrpos($_SERVER[SERVER_NAME].$_SERVER[REQUEST_URI],"?") );
          $LASTID = $this->db->lastID();
		  $this->db->query("SELECT * FROM users WHERE `acl`='0'");
 		  $message = "You have a new bug/feature in your tracking system. \r\n Please visit http://".$viewurl."?cmd=view&id=$LASTID to view it."; 
       		while($r = $this->db->fetch_array()){
    			$this->user->mailUser($r[id], 'BlueBug', $message, 'New Bug/Feature in BlueBug');
  			}
       	   echo "<script>window.location='index.php';</script>";   
              
       }
   ?>
   <script> 
   function quickAdd(){
    var ch = document.getElementById('title').value;
    if(ch == "Quickly add a bug... ")
        document.getElementById('title').value = '';
   }
   function quickAddU(){
       var ch = document.getElementById('title').value;
     if(ch == "")
        document.getElementById('title').value = 'Quickly add a bug... ';
   } 
   </script>
     <form name="" method="POST" action="">
     <tr class="L1">
     <td colspan="9" align="center">
    <img src="images/smbug.png" id="smbug" onclick="document.getElementById('smbug').style.borderBottom='2px solid black'; document.getElementById('feat').style.borderBottom='0px solid black';document.getElementById('type').value='0';">
    <img id="feat" src="images/feature.png" onclick="document.getElementById('smbug').style.borderBottom='0px solid black'; document.getElementById('feat').style.borderBottom='2px solid black';document.getElementById('type').value='1';">
    
    <input type="hidden" name="type" id="type" value="">
    <select name="project">
             <?php 
             $tpr = $this->db->query("SELECT * FROM projects ORDER BY `name` ASC");
             while($r = mysql_fetch_array($tpr))
                echo '<option value="'.$r['id'].'">'.$r['name'].'</option>';
           ?>
    </select>
    <input name="title" id='title' class="quick" style="width: 400px;" onfocus="quickAdd()" onblur="quickAddU()" value="Quickly add a bug... " />
     
     <input type="submit" name="quickadd" value="Add">
     </td>
     </tr>
     </form>
   <?php   
  }
}
  
class View extends Bugs {
    var $db = 0;
    function View($db){
        $this->db=$db;
        $this->user=new User();
    }
    
    /* from php.net or something -- regex to convert text-links to html links */
    function make_clickable($text, $ce, $git)
    {
       
        if (ereg("[\"|'][[:alpha:]]+://",$text) == false)
        {
            $text = ereg_replace('([[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/])', '<a target=\"_new\" href="\\1">\\1</a>', $text);
        }
        $patterns = array('#\[ce\](.*?)\[/ce\]#is', 
        					'#\[git\](.*?)\[/git\]#is');
        $replacements = array(
        			'<a href="'.$ce.'index.php?fuse=support&view=ViewTicketDetails&ticketID=$1" target="blank">\[CE-Ticket \#$1\]</a>', 
        			'<a href="'.$git.'$1">\[GitHub: $1\]</a>'); 
       // $text = preg_replace('#\[ce\](.*?)\[/ce\]#is', '<a href="'.$ce.'index.php?fuse=support&view=ViewTicketDetails&ticketID=$1" target="blank">\[CE-Ticket \#$1\]</a>', $text);
       $text = preg_replace($patterns, $replacements, $text);
        //'#\[wow\](.*?)\[/wow\]#is'
        return($text);
    }
    
    function edit($parent, $commentid){
    	
    	$report = $this->db->first("SELECT `report` FROM list WHERE `id`='$commentid'");
    	$title = $this->db->first("SELECT `title` FROM list WHERE `id`='$commentid'");
     if( $this->user->adminCheck() ){ 
    	if(isset($_POST["save"])){
    		$this->db->query_update("list", array('report' => nl2br($_POST["report"]), 'title' => $_POST["subject"]), "`id`='$commentid' LIMIT 1");
    		echo '<script>window.location="?cmd=view&id='.$parent.'";</script>';
    	}
    	?>
    	
    	<form name="" method="POST" action="">
<table width="90%" cellspacing="2" align="center">
<tr>
<td colspan="2"><div id="headings">Edit Comment #<?php echo $commentid; ?></div>
</td>
</tr>

<tr>
	<td valign="top" width="50%">
		<table width="100%" cellspacing="2" align="center">
			<tr>
				<td><label for="subject" >Title</label></td>
			</tr>
			<tr>
				<td><input type="text" value="<?php echo $title; ?>"class="input" name="subject" /></td>
			</tr>
			<tr>
				<td><label for="subject" >Comment</label></td>
			</tr>
			<tr>
				<td><textarea name="report" class="textarea"><?php echo str_replace('<br />', '', $report); ?></textarea></td>
			</tr>
			<tr>
				<td><div id="working"><img src="/loader.gif" id="loader" /> <b>Working...</b></div> 

            </div> <input type="submit" name="save" value="Save" onclick="$('#working').fadeIn(); document.getElementById('working').style.visibility='visible';"></td>
			</tr>
		</table>
	</td>
	</tr>
	</table>
</td></tr>
</table>
    	<?php
    	}else{
    		echo "Forbidden";
    	}
    }
    
    function original($bugid){
    $q = $this->db->query("SELECT * FROM list WHERE `id`='$bugid'");
	

        while($r = $this->db->fetch_array()){
        	$this->clientexec = $this->db->first("SELECT client_exec FROM projects WHERE `id`='".$r["project"]."' ");
			$this->git = $this->db->first("SELECT github FROM projects WHERE `id`='".$r["project"]."' ");
         ?>
         <?php if( $this->user->adminCheck() ){ ?>
         	<script>
         		function saveTitle(){
         			$.post('ajax.php', {titlechange: document.getElementById('title').value, tickid: '<?php echo $r["id"];?>', username:'<?php echo $_SESSION["userName"];?>', password: '<?php echo $_SESSION["passWord"];?>'}, function(data){ alert(data); } );
         			
         			$('#tickettitle').replaceWith("<span id='ticketitle'>" + document.getElementById('title').value + "</span>");	
         		}
         		function editTitle(){
         			//$('#tickettitle').empty();
         			$('#tickettitle').replaceWith("<span id='tickettitle'><input name='edittitle' id='title' onblur='saveTitle()' value='" + $('#tickettitle').text() + "' /></span>");
         		}
         	</script>
         	<?php
         }
         ?>
         <table class="bugreport alt1" align="center">
         	<tr>
         		<td><div id="headings" style="float: left;" class="dark">
         		<img src="<?php echo $this->img($r["type"]);?>" style='' /> <?php echo $r["title"];?></div>
         		<?php if( $this->user->adminCheck() ){ ?><div style="float: right;"><small class="small"><a href="?cmd=edit&parent=<?php echo $_GET["id"]; ?>&commentid=<?php echo $r["id"]; ?>">edit</a></small></div><?php } ?>
         		</td>
         	</tr>
         	<tr>
         		<td><div id="subheading" >Reported by <?php echo $this->user->uidToName($r["by"]);?> | <?php echo date("M, d Y H:m:A",$r["started"]); ?></div>
         		
         		</td>
         	</tr>
         	<tr>
         	<td id="reportarea"><?php echo stripslashes($this->make_clickable($r["report"], $this->clientexec, $this->git )); ?></td>
         	</tr>
         	<?php
         	
         	if(strlen($r["attachment"]) > 0){
         		$fn = strpos($r["attachment"], '-');
         		
         		
         		?>
         			<tr>
         			<td><div id="headings-small">Attachment</div></td>
         			</tr>
         			<tr><td><a href="<?php echo $r["attachment"]; ?>" target="_blank"><?php echo str_replace('uploads/', '',substr($r["attachment"], 0, $fn)); ?></a></td></tr>
         		<?php
         	}
         	
         	?>
         </table>
         
           <?php /*<table width="90%" class="bugreport" align="center">
                   <tr>
                        </td>
                   </tr>
                   <tr>
                   <td width="150" align="center" valign="top">
                        <img src="<?php echo $this->img($r["type"]);?>" style='' /> <hr style='border: 0;'>
                       <b> Assigned to:</b> <br /><span id="assto"><?php echo $this->user->assigned($r["assigned"]);?></span>  <hr  style='border: 0;'>
                        <b>Priority:</b> <br /><?php echo $this->adminPriHover($r["id"], $r["priority"]);?>  <br>     <br>
                        <b>Status</b>: <span id="status"><?php if($r["status"] == 1) echo "Open";
                                        else  echo "Closed"; ?></span>
                   </td>
                        <td valign="top">
                        
                        <h3 style='border-bottom: 1px solid #ACACAC;'>
                       <?php if($this->user->adminCheck() ){ ?> <img src="images/page_edit.png" onclick="editTitle();"> <?php } ?>
                        <span id="tickettitle"><?php echo $r["title"];?> </span>
                        <small>by <?php echo $this->user->uidToName($r["by"]);?></small></h3>
                        <?php echo stripslashes( $this->make_clickable($r["report"]) );?>
                        
                        </td>
                        
                   </tr>
                   <tr>
                        <td colspan="2" align="right">
                        <?php if($this->user->adminCheck()) { ?><a href="?cmd=delete&id=<?php echo $r["id"];?>" style='color: red;'>Delete</a><?php } ?>
                        <a href="javascript:;" id="reply">Reply</a></td>
                   </tr>
           </table>
           <script>
        $(document).ready(function() { $('#replyForm').hide();
        $('#reply').toggle(function () {$('#replyForm').slideDown();},function () {$('#replyForm').slideUp();});
         $('#assignlink').toggle(function () {$('#assign').show();},function () {$('#assign').hide();}); $('#assign').hide(); } );</script>
       */ ?>  <?php   
        }

    }
    function responses($bugid){
     $q = $this->db->query("SELECT * FROM list WHERE `parent`='$bugid' ORDER BY `id` ASC");
        $counter = 1;
        $cssclass = "alt1";
        while($r = $this->db->fetch_array()){
            $counter++;
            if($cssclass == "alt1")
            	$cssclass = "alt2";
            elseif($cssclass == "alt2")
            	$cssclass = "alt1";
         ?>
         <table width="80%" class="bugreport <?php echo $cssclass; ?>" align="center" onmouseover="$('#<?php echo $r["id"];?>edit').css('visibility','visible');$('#<?php echo $r["id"];?>edit').css('display','block'); " onmouseout="$('#<?php echo $r["id"];?>edit').css('visibility','hidden');$('#<?php echo $r["id"];?>edit').css('display','none'); ">
         	<tr>
         		<td><div id="headings" style="float: left; "class="dark"><?php echo $r["title"];?></div>
         		<?php if( $this->user->adminCheck() ){ ?><div style="float: right; visibility: hidden; display: none;" id="<?php echo $r["id"];?>edit"><small class="small"><a href="?cmd=edit&parent=<?php echo $_GET["id"]; ?>&commentid=<?php echo $r["id"]; ?>">edit</a></small></div><?php } ?>

         		</td>
         	</tr>
         	<tr>
         		<td><div id="subheading">Reported by <?php echo $this->user->uidToName($r["by"]);?> | <?php echo date("M, d Y H:m:A",$r["started"]); ?></div>
         		
         		</td>
         	</tr>
         	<tr>
         	<td id="reportarea" ><?php echo stripslashes($this->make_clickable($r["report"], $this->clientexec, $this->git )); ?></td>
         	</tr>
         	<?php
         	
         	if(strlen($r["attachment"]) > 0){
         		$fn = strpos($r["attachment"], '-');
         		
         		
         		?>
         			<tr>
         			<td><div id="headings-small">Attachment</div></td>
         			</tr>
         			<tr><td><a href="<?php echo $r["attachment"]; ?>" target="_blank"><?php echo str_replace('uploads/', '',substr($r["attachment"], 0, $fn)); ?></a></td></tr>
         		<?php
         	}
         	
         	?>
         </table>
		<?php /*
           <table width="90%" class="bugreport" align="center" style='border: 1px solid #efefef;'>
                  
                   <tr>
                   <td width="150" align="center" valign="top">
                    <h1><?php echo $counter;?></h1>    
                   </td>
                        <td valign="top">
                        
                        <h3 style='border-bottom: 1px solid #ACACAC;'><?php echo $r["title"];?> <small>by <?php echo $this->user->uidToName($r["by"]);?></small></h3>
                        <?php echo stripslashes($this->make_clickable($r["report"]));?>
                        
                        </td>
                        
                   </tr>
                   <tr>
                        <td colspan="2" align="right"style='background-color: #e8e8e8;'>
                        <?php if($this->user->adminCheck()) { ?><a href="?cmd=delete&id=<?php echo $r["id"];?>" style='color: red;'>Delete</a><?php } ?></td>
                   </tr>
           </table>
           */ ?>
         <?php   
        }
    }
    
    function reply($bugid){
    
     ?>
<form name="" method="POST" action="" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
<table width="90%" cellspacing="2" align="center">
<tr>
<td colspan="2"><div id="headings">Add a Comment</div>
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
				<td><label for="subject" >Comment</label></td>
			</tr>
			<tr>
				<td><textarea name="report" class="textarea"></textarea></td>
			</tr>
			<tr>
				<td><label for="attachment">Attachment</label></td>
			</tr>
			<tr>
				<td><input type="file" name="attachment" /></td>
			</tr>
			<tr>
				<td><div id="working"><img src="/loader.gif" id="loader" /> <b>Working...</b></div> 

            </div> <input type="submit" name="submitReport" value="Submit Report" onclick="$('#working').fadeIn(); document.getElementById('working').style.visibility='visible';"></td>
			</tr>
		</table>
	</td>
	</tr>
	</table>

</form>
              
    <?php
    } 
   
    function reports(){
      ?>  
     <div class="clear"></div>
<div id="reports">
<?php 
// get projects
  $q = $this->db->query("SELECT * FROM `projects` ORDER BY `name` AND `mini` ASC");
  while($r = $this->db->fetch_array()){
      $unfinished = $this->db->first("SELECT count(*) FROM `list` WHERE `parent`='0' AND `project`='$r[id]' AND `status`='1' AND `type`='0'");
      $finished = $this->db->first("SELECT count(*) FROM `list` WHERE `parent`='0' AND `project`='$r[id]' AND `status`='0' AND `type`='0'");
        if($unfinished == 0) $unfinished = $finished; 
      $percent = @round(($finished/$unfinished), 2);
      $backgroundpos = 300-($percent * 100 * 3);
   ?>
     <h2><?php echo $r["name"];?> <small><?php echo $r["mini"];?></small></h2>
     <div>
        <h4>Bugs</h4>
        <table cellspacing="0" cellpadding="0" border="0" width="300">
        <tr>
            <td align="center" style="background-image: url('<?php echo BBPATH; ?>/images/bar.gif'); background-position: -<?php echo $backgroundpos;?>px 0px; background-repeat: no-repeat;  color: black;">
            <?php echo $finished;?>/<?php echo $unfinished;?>
            </td>
        <tr>
        </table> 
     </div>
      <?php 
      $unfinished = $this->db->first("SELECT count(*) FROM `list` WHERE `parent`='0' AND `project`='$r[id]' AND `status`='1' AND `type`='1'");
      $finished = $this->db->first("SELECT count(*) FROM `list` WHERE `parent`='0' AND `project`='$r[id]' AND `status`='0' AND `type`='1'");
        if($unfinished == 0) $unfinished = $finished; 
      $percent = @round(($finished/$unfinished), 2);
      $backgroundpos = 300-($percent*100*3);
      //echo ;
      ?>
     <div>
        <h4>Features</h4>
        <table cellspacing="0" cellpadding="0" border="0" width="300">
        <tr>
            <td align="center" style="background-image: url('<?php echo BBPATH; ?>/images/bar.gif'); background-position: -<?php echo $backgroundpos;?>px 0px; background-repeat: no-repeat; color: black;">
            <?php echo $finished;?>/<?php echo $unfinished;?>
            </td>
        </tr>
        </table> 
     </div>
     <div style="padding-top: 20px;"></div>
   <?php   
  }
?>
</div>
<div class="clear"></div>
<?php   
    }
    
    
  function listProjects($current = null){
   $q = $this->db->query("SELECT `id`, `name`, `mini` FROM `projects` ORDER BY `name` ASC");
   while($r = $this->db->fetch_array())
    echo "<option value='$r[id]'>$r[name] ($r[mini])</option>";
    
  }
  
  
 
}  
?>
