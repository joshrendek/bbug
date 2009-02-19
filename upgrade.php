<?php
define('__bbug', 1);
  // installer
  include('config.php');
  include('includes/db.php');
  include('includes/main.php');
  include('includes/user.php');
  $mydb = new Database($db['host'], $db['user'], $db['pass'], $db[db], '', 20);
  $mydb->NewConnection();
  $main = new Main($mydb); 
  ?>
 <html>
        <head> 
        <title>BlueBug - Tracking Software</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link type="text/css" href="/bug.css" rel="stylesheet">
        <!--[if lt IE 8.]>
        <style type="text/css">
        #cLeft { margin-left: -5px; }
        #cRight { margin-right: -4px; }    
        #topWrapper { margin-left: -5px; }
        </style>>
        <![endif]-->

    <!--[if lt IE 7.]>
    <script defer type="text/javascript" src="/js/pngfix.js"></script>
    <![endif]-->                

        <script type="text/javascript" src="/js/jquery-1.2.6.min.js"></script>
        <script type="text/javascript" src="/js/jquery.wysiwyg.pack.js"></script>
         <script type="text/javascript" src="/js/jq-sort.js"></script> 
         

        </head>
        <body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"> 
       
<div class="clear"></div>
    <div align="center">
        <div id="topContent" width="90%">
            <img src="/images/index_09.gif" id="cLeft" />
            <img src="/images/index_11.gif" id="cRight" />        
        </div>
        
        <div id="contentArea" width="91%">
        <h1>Installing BlueBug...</h1>
        
        <?php
       
          


$todoSQL = "CREATE TABLE `todo_main` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`title` VARCHAR( 100 ) NOT NULL ,
`project` INT( 11 ) NOT NULL ,
INDEX ( `id` )
) ENGINE = MYISAM; ";
$mydb->query($todoSQL);
if(strlen($mydb->errorno) == 0)
echo "<b>ToDo_main table created....</b><br/>";

$todoLSQL = " CREATE TABLE `db41812_bb`.`todo_list` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`content` TEXT NOT NULL ,
`status` INT( 2 ) NOT NULL ,
INDEX ( `id` )
) ENGINE = MYISAM;";
$mydb->query($todoLSQL);
if(strlen($mydb->errorno) == 0)
echo "<b>ToDo_List table created....</b><br/>";

$projModSQL = "ALTER TABLE `projects` ADD `client_exec` VARCHAR( 255 ) NOT NULL ;ALTER TABLE `projects` ADD `github` VARCHAR( 255 ) NOT NULL ;";
$mydb->query($projModSQL);

$mydb->query("ALTER TABLE `list` ADD `attachment` VARCHAR( 100 ) NOT NULL ;");

        
        $main->message("BlueBug tables updated.");
        ?>
        
        
        
        <?php  ?>
        <div style="clear: both; height: 100px;"></div>
        </div>
        
        
        
        
        <div id="bottomContent" width="90%">
            <img src="/images/index_15.gif" id="cLeft"  />
            <img src="/images/index_18.gif" id="cRight"  />        
        </div>
        
    </div>
    
    </body>
    </html>