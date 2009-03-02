<?php
  /* Main class */
  
  defined('__bbug') or die();
  
  define("BBPATH", "http://".substr($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],"/") ) );
  
  class Main { 
   var $db = 0;
   var $user = 0;
   function Main($db){
    $this->db=$db; 
    $this->user = new User();
   }
   
   function headStart(){
      ?>
        <html>
        <head> 
        <title>BlueBug - Tracking Software</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link type="text/css" href="bug.css" rel="stylesheet">
        <link type="text/css" href="js/jquery.wysiwyg.css" rel="stylesheet">
        <!--[if lt IE 8.]>
        <style type="text/css">
        #cLeft { margin-left: -5px; }
        #cRight { margin-right: -4px; }    
        #topWrapper { margin-left: -5px; }
        </style>>
        <![endif]-->

    <!--[if lt IE 7.]>
    <script defer type="text/javascript" src="js/pngfix.js"></script>
    <![endif]-->

        <script type="text/javascript" src="js/jquery-1.2.6.min.js"></script>
        <script type="text/javascript" src="js/jquery.wysiwyg.pack.js"></script>
        <script type="text/javascript" src="js/jq-sort.js"></script> 
        <script type="text/javascript" src="js/init.js"></script> 

         
        <script>$(document).ready(function(){$('#ProjTab').hide();
$("#ProjToggle").toggle(function () {$('#ProjTab').fadeIn();},function () {$('#ProjTab').hide();});
        });
      </script>
        </head>
        <body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"> 
        
        
        <table align="center" width="1000" cellspacing="0" cellpadding="0">
        	<tr>
        		<td><img src="images/bluebug_02.gif" width="259" height="71" alt=""></td>
        		<td id="bbtop" width="100%"> <div style='float: right;padding-right: 30px;'><? echo $this->user->loginForm();?></div></td>
       		</tr>
       		<tr>
       			<td colspan="2">
       			<table width="100%" cellspacing="0" cellpadding="0" align="center">
       			<tr>
       			<td align="left"><img src="images/bluebug_05.gif" width="35" height="29" alt=""></td>
       			<td id="bbbot" width="100%" valign="top" >
       			<table width="55%" style="padding-top: 6px;" align="left" cellspacing="0" cellpadding="0">
       				<tr>
       				<td><a href="?">Home</a></td>
                    <td><a href="?cmd=submit">New Ticket</a></td>
                    <?php
                    $counter = array();
                    $counter['open'] = $this->db->first("SELECT count(*) FROM list WHERE `status`='1' AND `parent`='0' ");
                    $counter['closed'] = $this->db->first("SELECT count(*) FROM list WHERE `status`='0' AND `parent`='0' ");
                    $counter['feature'] = $this->db->first("SELECT count(*) FROM list WHERE `type`='1' AND `parent`='0' ");
                    $counter['bug'] = $this->db->first("SELECT count(*) FROM list WHERE `type`='0' AND `parent`='0' ");
                    ?>
                    <td onmouseover="ticketMenuShow();"><a href="?">Tickets</a> <a href="#" style="margin-top: 2px; margin-left: 3px; position: absolute;" id="TICKETS" onclick=""><img src="images/arrow.png" border="0" width="10" height="10" /></a>
                    <div id="ticketMenu" onmouseout="ticketMenuHide();" style="display: none; visibility: hidden;">
                    	<div id="headings-small">Ticket List</div>
                    	<a href="?cmd=bugs">Bug List (<?php echo $counter['bug']; ?>)</a>
                    	<a href="?cmd=features">Feature List (<?php echo $counter['feature']; ?>)</a>
                    	<a href="?specialrefiner=all">Show All (<?php echo $counter['open']+$counter['closed']; ?>)</a>
                    	<a href="?specialrefiner=open">Show Open (<?php echo $counter['open']; ?>)</a>
                    	<a href="?specialrefiner=closed">Show Closed (<?php echo $counter['closed']; ?>)</a>
                    	<div id="headings-small">Projects</div>
                    	<?php 
             
             $tpr = $this->db->query("SELECT * FROM projects ORDER BY `name` ASC");
             while($r = mysql_fetch_array($tpr))
                echo '<a href="?specialrefiner='.$r['id'].'">'.$r['name'].'</a>';
           ?>
                    </div>
                    </td>
                    <td><a href="?cmd=todo">To-Do</a></td>
                    <td><a href="?cmd=reports">Reports</a></td>
                    <?php if(isset($_GET["id"]) && $_GET["cmd"]=="view" && $this->user->adminCheck()){ ?>
                    <td></td>
                    <td onmouseover="thisTicketShow();"><a href="?cmd=view&id=<?php echo $_GET["id"]; ?>">This Ticket</a> 
                    <a href="#" style="margin-top: 2px; margin-left: 3px; position: absolute;" id="TICKETMENUUNI" onclick="">
                    	<img src="images/arrow.png" border="0" width="10" height="10" />
                    </a>
                    <div id="ticketMenuUniq"  onmouseout="thisTicketHide();" style="display: none; visibility: hidden;">
                    	<div id="headings-small">Ticket Options</div>
                    	<a href="javascript:;" onclick="$('#status').empty();$('#status').append('Open');$.post('ajax.php', {openticket: 'true', tickid: '<?php echo $_GET["id"];?>', username:'<?php echo $_SESSION["userName"];?>', password: '<?php echo $_SESSION["passWord"];?>'}, function(data){ alert(data); });">Open</a>
                        <a href="javascript:;" onclick="$('#status').empty();$('#status').append('Closed');$.post('ajax.php', {closeticket: 'true', tickid: '<?php echo $_GET["id"];?>', username:'<?php echo $_SESSION["userName"];?>', password: '<?php echo $_SESSION["passWord"];?>', by: '<?php echo $r["by"];?>'}, function(data){ alert(data); });">Close</a>
                        <div id="headings-small">Assign To</div>
                        <span id="assign" align="center"><select id="assignto" style="font-size: 10px;" name="assign">                                  
                        <?php $qq = $this->db->query("SELECT * FROM users ORDER BY username;"); 
                        while($rr = $this->db->fetch_array($qq)){ ?> <option value="<?php echo $rr["id"];?>"><?php echo $rr["username"];?> (<?php echo $rr["email"];?>)</option> <?php } ?>
                        </select> <a href="javascript:;" onclick="$('#assto').empty();$.post('ajax.php', {assignto: document.getElementById('assignto').value, tickid: '<?php echo $_GET["id"];?>', username:'<?php echo $_SESSION["userName"];?>', password: '<?php echo $_SESSION["passWord"];?>'}, function(data){ alert('Assigned to: ' + data); });" >Change</a></span>
                        
                        <a href="?cmd=delete&id=<?php echo $_GET["id"]; ?>" style='color: red;'>Delete</a>
                        
                    </td>
                    <?php } ?>
                    
                    </tr>
                  </table>
                    
                    
                    
                    </td>
       			<td align="right"><img src="images/bluebug_08.gif" width="23" height="29" alt=""></td>
       			</tr>
       			</table>
       			</td>
       		</tr>
        </table>
        
        <div class="clear"></div>

        
        
          
    <?php
      }
      
   function body(){
   	if(isset($_GET["cmd"]))
    	$cmd = $_GET["cmd"];
    else
    	$cmd = "";
    
    if($cmd == "submit")
        include('submit.php');
    elseif($cmd == "delete"){
     //check if its parent ticket
     $bugid = $this->db->clean($_GET["id"], '', '');
     if($this->db->first("SELECT `parent` FROM list WHERE id='$bugid'") == 0){
        $this->db->del("list", "id='$bugid'");
        $this->db->del("list", "parent='$bugid'");
     }else
        $this->db->del("list", "id='$bugid'", '1');   
     
     $this->message("Record deleted."); 
     echo "<script>window.location='?';</script>";  
    }elseif($cmd == "reports")
        include('report.php');
    elseif($cmd == "edit")
    	include('edit.php');
    elseif($cmd == "bugs")
        include('bugs.php');
    elseif($cmd == "register")
        include('register.php');
    elseif($cmd == "features")
        include('features.php');
    elseif($cmd == "view")
        include('view.php');
    elseif($cmd == "todo")
        include('todo.php');
    elseif(isset($_GET["admin"]))
     include('admin/admin.php');       
    elseif($cmd == "" && !isset($_GET["admin"])){
     include('home.php');
    }
  }   
  
  function message($string){
    echo "
    <center>
    <div id='messageShow' style='display: none; width: 90%;' width='90%'>$string</div><script>$('#messageShow').fadeIn('1000'); setTimeout(\"$('#messageShow').fadeTo('slow', .33)\", 1000);  setTimeout(\"$('#messageShow').fadeOut()\", 3000); </script></center>";
  }
  
  
  
   
  function footStart(){
  ?>
  <center class="copyright">Powered by <a href="http://bluescripts.net/bluebug/" target="_blank">BlueBug</a>

  </center>
      </body>
</html>
    <?php
  } 
   
   /* end class */
  }
  
   
  
  
  
  
  
  
  
?>
