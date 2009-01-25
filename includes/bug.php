<?php
  defined('__bbug') or die(); 
  class Bugs {
    var $db = 0;
    var $user = 0;
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
              <th>ID</th>
              <th>Type</th> 
              <th width="45%">Title</th>
              <th width="25">Status</th>
              <th>By</th>
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
        
        if($_POST['specialrefiner']){
         $_SESSION['esql'] = "";
         $srf = $_POST['specialrefiner'];
         if($srf == "open")
             $_SESSION['esql'] = "AND `status`='1'";
         elseif($srf == "closed")
             $_SESSION['esql'] = "AND `status`='0'";
         elseif($srf == "all")
            $_SESSION['esql'] = "";
         elseif(is_numeric($srf))
            $_SESSION['esql'] = "AND `project`='".$this->db->clean($srf, '', 'num')."'";
        }
        /* Handle paging */
        if(is_numeric($_GET[page])){
            $page = intval($_GET[page]);
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
        <td align="center"><?php echo $r[id];?></td>
        <td width="16" align="center"><div style='position: relative;'><img src="<?php echo $this->img($r[type]);?>" style='' />
        <?php if($r[status] == 0){ ?><img src="images/cancel.png" style='position: absolute; float: left; margin-left: -16px; opacity: .7;' /><?php } ?></div></td>
        <td><a href="?cmd=view&id=<?php echo $r[id];?>"><?php if($r[title] == "") echo "[No Title]"; else echo $r[title];?></a></td>
        <td align="center"><?php if($r[status] == 1)
                                echo "Open";
                                else
                                    echo "Closed"; ?></td>
        <td><?php
        
        if($r[by] == 0) echo "Anonymous";
        else echo $this->user->uidToName($r[by]);
        
        ?></td>
        <td align="center"><?php echo $this->ProjectIDtoName($r['project']); ?></td>
        <td align="center" class="pri<?php echo $r[priority];?>" id="<?php echo $r[priority];?>"><?php echo $this->adminPriHover($r[id], $r[priority]);?></td>
        <td align="center"><?php echo $this->the_date($r[started]);?></td>
        <td><?php
        
        if($r[finished] == 0)
            echo "Never";
        else echo $this->the_date($r[finished]);
        
        ?></td>
        </tr><?php 
      }
       ?>
         
       </tbody>
       <?php 
       if(REGISTERED == 0 || $_SESSION[userName]){
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
     <div style="float: right">
           <form name="refiner" method="POST" action=""><select name="specialrefiner" onchange="document.refiner.submit();">
           
           <option value="">Show only....</option>
           <option value="">-- Projects --</option>
           <?php 
             $tpr = $this->db->query("SELECT * FROM projects ORDER BY `name` ASC");
             while($r = mysql_fetch_array($tpr))
                echo '<option value="'.$r['id'].'">'.$r['name'].'</option>';
           ?>
           <option value="">-- Other --</option>
           <option value="open">Open Tickets</option>
           <option value="closed">Closed Tickets</option>
           <option value="all">Show All</option>
           </select>
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
      <a href="javascript:;" onclick="$.post('ajax.php', { changepri: '1', id: '<?php echo $id;?>', username: '<?php echo $_SESSION[userName];?>', password: '<?php echo $_SESSION[passWord];?>' } ); $('#<?php echo $id;?>PriB').empty(); $('#<?php echo $id;?>PriB').append('High');">High</a>
      <a href="javascript:;" onclick="$.post('ajax.php', { changepri: '2', id: '<?php echo $id;?>', username: '<?php echo $_SESSION[userName];?>', password: '<?php echo $_SESSION[passWord];?>' } ); $('#<?php echo $id;?>PriB').empty(); $('#<?php echo $id;?>PriB').append('Moderate');">Moderate</a>
      <a href="javascript:;" onclick="$.post('ajax.php', { changepri: '3', id: '<?php echo $id;?>', username: '<?php echo $_SESSION[userName];?>', password: '<?php echo $_SESSION[passWord];?>' } ); $('#<?php echo $id;?>PriB').empty(); $('#<?php echo $id;?>PriB').append('Low');">Low</a></div> 
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
       if($_POST[quickadd]){
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
    function make_clickable($text)
    {
       
        if (ereg("[\"|'][[:alpha:]]+://",$text) == false)
        {
            $text = ereg_replace('([[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/])', '<a target=\"_new\" href="\\1">\\1</a>', $text);
        }
        return($text);
    }
    function original($bugid){
    $q = $this->db->query("SELECT * FROM list WHERE `id`='$bugid'");

        while($r = $this->db->fetch_array()){
         ?>
         <?php if( $this->user->adminCheck() ){
         	?>
         	<script>
         		function saveTitle(){
         			$.post('ajax.php', {titlechange: document.getElementById('title').value, tickid: '<?php echo $r[id];?>', username:'<?php echo $_SESSION[userName];?>', password: '<?php echo $_SESSION[passWord];?>'}, function(data){ alert(data); } );
         			
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
           <table width="90%" class="bugreport" align="center" style='border: 1px solid #efefef;'>
                   <tr>
                        <td  colspan="2" align="right" style='background-color: #e8e8e8;'><?php if($this->user->adminCheck()) { ?><a href="javascript:;" id="assignlink">Assign</a>
                        <span id="assign"><select id="assignto" name="assign">                                  
                        <?php $qq = $this->db->query("SELECT * FROM users ORDER BY username;"); 
                        while($rr = $this->db->fetch_array($qq)){ ?> <option value="<?php echo $rr[id];?>"><?php echo $rr[username];?> (<?php echo $rr[email];?>)</option> <?php } ?>
                        </select> <a href="javascript:;" onclick="$('#assto').empty();$.post('ajax.php', {assignto: document.getElementById('assignto').value, tickid: '<?php echo $r[id];?>', username:'<?php echo $_SESSION[userName];?>', password: '<?php echo $_SESSION[passWord];?>'}, function(data){ $('#assto').append(data); });" style='padding: 0;'><small>Change</small></a></span>
                        <?php //if($r[status] == 1) { ?>
                        <a href="javascript:;" onclick="$('#status').empty();$('#status').append('Closed');$.post('ajax.php', {closeticket: 'true', tickid: '<?php echo $r[id];?>', username:'<?php echo $_SESSION[userName];?>', password: '<?php echo $_SESSION[passWord];?>', by: '<?php echo $r[by];?>'}, function(data){ alert(data); });">Close Ticket</a>
                        <?php //}else{ ?>
                        <a href="javascript:;" onclick="$('#status').empty();$('#status').append('Open');$.post('ajax.php', {openticket: 'true', tickid: '<?php echo $r[id];?>', username:'<?php echo $_SESSION[userName];?>', password: '<?php echo $_SESSION[passWord];?>'}, function(data){ alert(data); });">Open Ticket</a>
                        <?php //} ?>
                        <?php }else { echo '&nbsp;'; } ?> </td>
                   </tr>
                   <tr>
                   <td width="150" align="center" valign="top">
                        <img src="<?php echo $this->img($r[type]);?>" style='' /> <hr style='border: 0;'>
                       <b> Assigned to:</b> <br /><span id="assto"><?php echo $this->user->assigned($r[assigned]);?></span>  <hr  style='border: 0;'>
                        <b>Priority:</b> <br /><?php echo $this->adminPriHover($r[id], $r[priority]);?>  <br>     <br>
                        <b>Status</b>: <span id="status"><?php if($r[status] == 1) echo "Open";
                                        else  echo "Closed"; ?></span>
                   </td>
                        <td valign="top">
                        
                        <h3 style='border-bottom: 1px solid #ACACAC;'>
                       <?php if($this->user->adminCheck() ){ ?> <img src="images/page_edit.png" onclick="editTitle();"> <?php } ?>
                        <span id="tickettitle"><?php echo $r[title];?> </span>
                        <small>by <?php echo $this->user->uidToName($r[by]);?></small></h3>
                        <?php echo stripslashes( $this->make_clickable($r[report]) );?>
                        
                        </td>
                        
                   </tr>
                   <tr>
                        <td colspan="2" align="right"style='background-color: #e8e8e8;'>
                        <?php if($this->user->adminCheck()) { ?><a href="?cmd=delete&id=<?php echo $r[id];?>" style='color: red;'>Delete</a><?php } ?>
                        <a href="javascript:;" id="reply">Reply</a></td>
                   </tr>
           </table>
           <script>
        $(document).ready(function() { $('#replyForm').hide();
        $('#reply').toggle(function () {$('#replyForm').slideDown();},function () {$('#replyForm').slideUp();});
         $('#assignlink').toggle(function () {$('#assign').show();},function () {$('#assign').hide();}); $('#assign').hide(); } );</script>
         <?php   
        }

    }
    function responses($bugid){
     $q = $this->db->query("SELECT * FROM list WHERE `parent`='$bugid' ORDER BY `id` ASC");
        $counter = 1;
        while($r = $this->db->fetch_array()){
            $counter++;
         ?>
           <table width="90%" class="bugreport" align="center" style='border: 1px solid #efefef;'>
                  
                   <tr>
                   <td width="150" align="center" valign="top">
                    <h1><?php echo $counter;?></h1>    
                   </td>
                        <td valign="top">
                        
                        <h3 style='border-bottom: 1px solid #ACACAC;'><?php echo $r[title];?> <small>by <?php echo $this->user->uidToName($r[by]);?></small></h3>
                        <?php echo stripslashes($this->make_clickable($r[report]));?>
                        
                        </td>
                        
                   </tr>
                   <tr>
                        <td colspan="2" align="right"style='background-color: #e8e8e8;'>
                        <?php if($this->user->adminCheck()) { ?><a href="?cmd=delete&id=<?php echo $r[id];?>" style='color: red;'>Delete</a><?php } ?></td>
                   </tr>
           </table>
         <?php   
        }
    }
    
    function reply($bugid){
     ?>
            <div id="replyForm" width="70%">
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
                    else echo $this->user->uidToName($this->user->getUID());
            ?>
            <br/><small>IP Address: <?php echo $_SERVER[REMOTE_ADDR];?></small>
            </td>
            </tr>
            <tr>
                 <td colspan="2"><b>Submission Date:</b><br /><?php echo date("D, F d Y h:m:s A T");?></td>
                 
            </tr>
            </table>                                                 
             <center><div id="editor"><img src="/loader.gif" id="loader" /> <br /><b>Editor loading...</b> </div> </center>
            </div><table align="center" width="600"><tr><td>
            <link type="text/css" href="/js/jquery.wysiwyg.css" rel="stylesheet"> 
            <textarea name="report" id="report" style="width: 400px; " cols="80" rows="10"></textarea><script>
            $(document).ready(function() { $('#editor').hide(); $('#report').wysiwyg(); } );</script>  </td></tr></table>
            <div class="clear"></div>


            <div id="working"><img src="/loader.gif" id="loader" /> <b>Working...</b></div> 

            </div> <input type="submit" name="submitReport" value="Submit Report" onclick="$('#working').fadeIn(); document.getElementById('working').style.visibility='visible';">
            </div> </form>  
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
     <h2><?php echo $r[name];?> <small><?php echo $r[mini];?></small></h2>
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
        <tr>
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
