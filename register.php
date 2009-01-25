<?php
    defined('__bbug') or die();
    
    
if($_POST[finish]){
            $this->db->query_insert("users", array('id' => null, 'username' => $_POST[username], 'password' => md5($_POST[password]), 'email' => $_POST[email], 'acl' => 99) );
            $this->message("New user account created.");
      }else{
   ?>
     <h3>New Account</h3>
     <form name="user" method="POST" action="" id="user" style="font-size: 11px;">
       
     <table width="400" align="center" cellspacing="2" cellpadding="0" border="0" style="font-size: 11px;">
             <tr>
            <td>Username:</td>
            <td><input name="username" class="register"></td>
            </tr>
            <tr>
            <td>Password:</td>
            <td><input name="password" class="register"></td>
            </tr>
            <tr>
            <td>Email:</td>
            <td><input name="email" class="register"></td>
            </tr>
            <tr>
                 <td colspan="2" align="center"><input type="submit" name="finish" value="Register"></td>
                 
            </tr>
     </table>
     </form>
   <?php
      }
      ?>
