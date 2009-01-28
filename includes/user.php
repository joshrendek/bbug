<?php
  defined('__bbug') or die();  
  
  Class User extends Database {
   
   function User(){
     
    //get_parent_class($this);
   // parent::Database($this->host, $this->user, $this->pass, $this->database, '');
   } 
   
   /* user id to name */
   function uidToName($uid){ 
    $x = $this->first("SELECT username FROM users WHERE id='$uid';");
    if($x == "")
        return "Anonymous";
    else
        return $x;
   }  
   /* get a users id */
   function getUID(){
     if($_SESSION[userName] && $_SESSION["passWord"]){
              $userName = $_SESSION["userName"];
              $passWord = $_SESSION["passWord"];
               
                $uid = $this->first("SELECT `id` FROM `users` WHERE `username`='$userName' AND `password`='".md5($passWord)."';");
                return $uid;
   }else{
    return 0;
   }   
  }
  
  function assigned($var){
      if($var == "0")
        return "Not Assigned";
      else
        return $this->first("SELECT username FROM users WHERE id='".$this->clean($var, '', '')."'");
  }
  
   function loginForm(){
      //print_r($_SESSION);
      if($_GET["logout"]){
        unset($_SESSION["userName"]);
        unset($_SESSION["passWord"]);
        echo "<script>document.location='?';</script>";
      }
       
      
          if($_POST["login"] && $_POST["username"] && $_POST["password"]){
          $userName = $this->clean($_POST["username"], '', '');
          $passWord = $this->clean($_POST["password"], '', '');
          
          // see if they exist in the db and if theyre PWs match, otherwise error out.
            $result = $this->first("SELECT count(*) FROM `users` WHERE `username`='$userName' AND `password`='".md5($passWord)."';");
            if($result == 1){ // success
               $_SESSION["userName"] = $userName;
               $_SESSION["passWord"] = $passWord; 
            }else{
                echo "<script>alert('Incorrect user/password');</script>";
            }
          }
          if($_SESSION["userName"] && $_SESSION["passWord"]){
              $userName = $_SESSION["userName"];
              $passWord = $_SESSION["passWord"];
               
                $adminCheck = $this->first("SELECT `acl` FROM `users` WHERE `username`='$userName' AND `password`='".md5($passWord)."';");
                if($adminCheck == 0)
                    $admin = "<a href='?admin'><small>(admin)</small></a>";
                else
                    $admin = "";
               echo "<div id='loggedin'>$admin Welcome back ".$_SESSION["userName"]."! <a href='?logout=1'> <img src='images/logout.png' border='0' style='position: relative; top: 3px;'/>  logout</a></div>";
          } 
          if(!$_SESSION["userName"]){
        ?>
          <form name="" method="POST" action="">
                 <input class="loginForm" style='width: 75px;' id='Lusername' name="username" />
                 <input class="loginForm" style='width: 75px;' id='Lpassword' type="password" name="password"  />
                 <input type="submit" name="login" value="Login" />
                 <div sytle='position: relative; top: 30px;'><small>username <span style='padding-left: 37px;'>password</span>
                 <span style='padding-left: 42px;'><a href="?cmd=register">Register</a></span>
                 </small></div>
          </form>
        <?php
        }      
  }
     
  /* check admin */
  function adminCheck(){
   if($_SESSION["userName"] && $_SESSION["passWord"]){
              $userName = mysql_escape_string($_SESSION["userName"]);
              $passWord = mysql_escape_string($_SESSION["passWord"]);
               
                $adminCheck = $this->first("SELECT `acl` FROM `users` WHERE `username`='$userName' AND `password`='".md5($passWord)."';");
                if($adminCheck == 0)
                    return true;
   }else{
    return false;
   }   
  }
  
  function mailUser($uid, $from, $message, $subject){
   
            // Construct email
            //$unique_sep = md5(uniqid(time()));

            $headers .= "From: $from\n";
            
            
            // find user ID's email
            $userEmail = $this->first("SELECT `email` FROM `users` WHERE `id`='$uid'");
            mail($userEmail, $subject, $message, $headers);
  }
  
     
  }
?>
