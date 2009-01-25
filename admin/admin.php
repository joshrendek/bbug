<?php
  defined('__bbug') or die();
  // security check
                $userName = $_SESSION[userName];
                $passWord = $_SESSION[passWord];
                $adminCheck = $this->db->first("SELECT `acl` FROM `users` WHERE `username`='$userName' AND `password`='".md5($passWord)."';");
                if($adminCheck != 0 || $adminCheck == "")
                die("Forbidden");
  // end
?>
<div id="adminContainer">&nbsp;
<div id="adminLeft">
<h3>Projects</h3>
   <a href="?admin&adm=addproject">Add Project</a>
   <a href="?admin&adm=listprojects">List Projects</a>
<h3>Users</h3>
    <a href="?admin&adm=listusers">List Users </a>
</div>
<div id="adminContent">
    <?php $adm = $_GET[adm];
        if($adm == "listusers"){
            
            if(is_numeric($_GET[delete])){
              $uid = $this->db->clean($_GET[delete], '', '');
              $this->db->del('users', "id='$uid'", 1); 
              $this->message("<center><h3>User Deleted.</h3></center>");  
            }
            ?>
              <h3>List Users</h3>
              <table width="550" border="0" cellspacing="2" align="center">
                      <tr style='font-weight: bold;'>
                        <td>ID</td>
                        <td>Username</td>
                        <td>Email</td>
                        <td>Options</td>
                      </tr>
              <?php
                $this->db->query("SELECT * FROM `users` ORDER BY `id` ASC");
                while($r = $this->db->fetch_array()){
                 ?>
                   <tr>
                        <td><?=$r[id];?></td>
                        <td><?=$r[username];?></td>
                        <td><?=$r[email];?></td>
                        <td><a href="?admin&adm=listusers&delete=<?=$r[id];?>">Delete</a></td>
                      </tr>
                 <?php   
                }
              
              ?>
              </table>
            <?php
        }elseif($adm == "addproject"){
            if($_POST[add_project]){
                // name mini description
                $bugData = array('id' => 'null', 'name' => $_POST[name], 'mini' => $_POST[mini], 'description' => $_POST[description]);
                $this->db->query_insert('projects', $bugData);
                $this->message("<center><h3>Project addded.</h3></center>");
            }
            ?>
              <h3>Add Project</h3>
              <form name="" method="POST">
              <table width="400" border="0" cellspacing="2" align="center">
                <tr>
                     <td valign="top">Name:</td>
                     <td valign="top"><input name="name" id="name" onclick="this.form.name.select();" class="input" value="{Untitled}" /></td>
                </tr>
                 <tr>
                     <td valign="top">Mini-version:</td>
                     <td valign="top"><input name="mini" class="input"  value="1.0" /> <br /><small>(eg: 1.1.2 or .9b)</small></td>
                </tr>
                
                <tr>
                     <td valign="top">Short Description:</td>
                     <td valign="top"><textarea class="textinput" name="description"></textarea></td>
                </tr>
                <tr> <td colspan="2" align="center"><input type="submit" style='width: 200px;' name="add_project" onclick="$('#working').fadeIn();" value="+ Add Project"></td></tr>
              </table>    
              </form>                                  
             <div id="working"><img src="/loader.gif" id="loader" /> <b>Working...</b></div> 
            <?php
        }
        
    elseif($adm == "listprojects"){
     ?>
       <h3>Projects</h3>
       <table width="500" align="center" cellspacing="2" cellpadding="2" border="0">
       <tr>
       <td><b>Project</b></td>
       <td><b>Version</b></td>
       <td><b>Options</b></td>
       </tr>
       <?
       if(is_numeric($_GET[delete])){
        $delid = (int)$_GET[delete];
        $this->db->del("projects", "id='$delid'", 1);
        $this->db->del("list", "project='$delid'");
        $this->message("Project deleted.");
       }
       $this->db->query("SELECT * FROM projects ORDER BY name, mini ASC");
       while($r = $this->db->fetch_array()){
        echo "<tr>";
        echo "<td>$r[name]</td>";
        echo "<td>$r[mini]</td>";
        echo "<td><a href='?admin&adm=listprojects&delete=$r[id]'>Delete</a></td>";
        echo "</tr>";
       }   
        
       ?></table><?php
    }
    ?>
</div>

</div>
 <div class="clear"></div>