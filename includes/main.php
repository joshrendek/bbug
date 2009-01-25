<?php
  /* Main class */
  
  defined('__bbug') or die();
  
  define("BBPATH", "http://".substr($_SERVER[SERVER_NAME].$_SERVER[REQUEST_URI], 0, strrpos($_SERVER[SERVER_NAME].$_SERVER[REQUEST_URI],"?") ) );
  
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
         
        <script>$(document).ready(function(){$('#ProjTab').hide();
$("#ProjToggle").toggle(function () {$('#ProjTab').fadeIn();},function () {$('#ProjTab').hide();});
        });
      </script>
        </head>
        <body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"> 
        
        
        <table align="center" width="1000" cellspacing="0" cellpadding="0">
        	<tr>
        		<td><img src="images/bluebug_02.gif" width="259" height="71" alt=""></td>
        		<td id="bbtop" width="100%"> <div style='float: right;padding-right: 30px;'><?=$this->user->loginForm();?></div></td>
       		</tr>
       		<tr>
       			<td colspan="2">
       			<table width="100%" cellspacing="0" cellpadding="0" align="center">
       			<tr>
       			<td align="left"><img src="images/bluebug_05.gif" width="35" height="29" alt=""></td>
       			<td id="bbbot" width="100%" valign="top" >
       				<a href="?">Home</a>
                    <a href="?cmd=submit">Submit a Bug/Feature</a>
                    <a href="?cmd=todo">To-Do</a>
                    <a href="?cmd=bugs">Bug List</a>
                    <a href="?cmd=features">Feature List</a>
                    <a href="?cmd=reports">Reports</a></td>
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
   
    $cmd = $_GET["cmd"];
    
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
    }elseif($cmd == "reports")
        include('report.php');
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
    elseif(isset($_GET[admin]))
     include('admin/admin.php');       
    elseif($cmd == "" && $_GET[admin] == ""){
     include('home.php');
    }
  }   
  
  function message($string){
    echo "
    <center>
    <div id='messageShow' style='display: none; width: 400px;'>$string</div><script>$('#messageShow').fadeIn('1000'); setTimeout(\"$('#messageShow').fadeTo('slow', .33)\", 1000);  setTimeout(\"$('#messageShow').fadeOut()\", 3000); </script></center>";
  }
  
  
  
   
  function footStart(){
  ?>
  <center class="copyright">Powered by <a href="http://bluescripts.net/bluebug/" target="_blank">BlueBug</a>
  <h4>The version you are currently viewing is in development and is not the downloadable version. If you want to see functionality of the downloadable version please look at the screen shots.</h4>
  </center>
      </body>
</html>
    <?php
  } 
   
   /* end class */
  }
  
   
  
  
  
  
  
  
  
?>
