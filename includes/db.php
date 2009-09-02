<?php
  defined('__bbug') or die();    
  

class Database {
       /* connection info */
    var $host   = ""; 
    var $user     = ""; 
    var $pass     = ""; 
    var $database = ""; 
    var $pre      = ""; 
    var $connection = 0;
        /* error vars */
    var $errordesc = "";
    var $errorno = "";
        /* query vars */
    var $queryid = 0;
    var $results = array();
    
        /* pagination var */
    var $pagenums = 10;
    function Database($host, $user, $pass, $database, $pre='', $pagenums){
        $this->host=$host;
        $this->user=$user;
        $this->pass=$pass;
        $this->database=$database;
        $this->pre=$pre;
        $this->pagenums=$pagenums;  
    }
    
    /* create the connection from the initialization */   
    function NewConnection(){ 
        $this->connection = @mysql_connect($this->host, $this->user, $this->pass );
      if(!$this->connection)
        $this->error("Couldn't connect to the server : ".$this->host."");
      
      if(!@mysql_select_db($this->database, $this->connection))
        $this->error("Couldn't connect to the database : ".$this->database);
      
    }
     /* return an array of a query */
    function query($sql){
     $this->queryid = @mysql_query($sql, $this->connection); 
     if(!$this->queryid)
        $this->error("Couldnt perform query: ".$sql);
    
    return $this->queryid;
    }
    /* return an array of results */
    function fetch_array($id=-1){
    if($id == -1)
      $res = @mysql_fetch_array($this->queryid);
    else
        $res = @mysql_fetch_array($id);  
     
     return $res;   
    }
    /* return first result in query */
    function first($sql){
        $r = @mysql_result(mysql_query($sql), 0, 0);   
        
        return $r;
    }
    
    function num_results(){
     $r = mysql_num_rows($this->queryid);
     if(!$r)
        $this->error();
     
     return $r;   
    }
    /* create pagination */
    function paginate($query){ 
       $per = $this->pagenums;
       $results = @mysql_num_rows(mysql_query($query));
      
        
       $pages = ceil($results/$per);
       //if($pages==null) $pages = 1;
       
       $links = "<div id='paginate'><b>Page:</b> ";
       $plinkpos = strpos($_SERVER["QUERY_STRING"], 'page=');
       if($_SERVER["QUERY_STRING"] == "cmd=bugs") $base = "?cmd=bugs&";
       elseif($_SERVER["QUERY_STRING"] == "cmd=features") $base = "?cmd=features&";
       else $base = "?";
       if(strlen($_SERVER["QUERY_STRING"]) > 0) $base .= "".substr($_SERVER["QUERY_STRING"], 0, $plinkpos);
       
       else $base = "?";
      // echo $results;
       for($i = 1; $i <= $pages; $i++)
        $links .= "<a href='".$base."page=$i' class='paginator'>$i</a>";
       
       echo $links;
       echo '</div>';
       //echo $this->num_results(); 
    }
    
    /* delete */
    function del($table, $where, $limit=-1){
     if($limit==-1) $rlimit = "";
     else $rlimit = "LIMIT $limit";
     $sql = "DELETE FROM $table WHERE $where $rlimit;";
     $q = @mysql_query($sql);   
     if(!$q)
        $this->error();
     return $q;
    }
    /* insert query from array */
     function query_insert($table, $data) {
        $q="INSERT INTO ".$table." ";
        $v=''; $n='';

        foreach($data as $key=>$val) {
            $n.="`$key`, ";
            if(strtolower($val)=='null') $v.="NULL, ";
            elseif(strtolower($val)=='time()') $v.=time().",";
            else $v.= "'".$this->escape($val)."', ";
        }

        $q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";
        
        $this->query($q);
        return mysql_insert_id();
    }
    
    /* update */
    function query_update($table, $data, $where='1') {
    $q="UPDATE `".$table."` SET ";

    foreach($data as $key=>$val) {
        if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
        elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
        else $q.= "`$key`='".$this->escape($val)."', ";
    }

    $q = rtrim($q, ', ') . ' WHERE '.$where.';';

    return $this->query($q);
    }
    
    /* escape */
    function escape($string){
        return mysql_escape_string($string);
    }
    /* handle errors */
    function error($str="") {
    $this->errordesc=mysql_error();
    $this->errorno=mysql_errno(); 
        ?>
          <table width="50%" align="center" cellspacing="2" style='border: 1px dashed #efefef; background-color: #FFFFFF; color: #000000; font-size: 12px;' cellpadding="2">
          <tr>
               <td><b>Error message:</b></td>
               <td><?php echo $str;?></td>
          </tr>
          <tr>
               <td><b>MySQL Error (#<? echo $this->errorno;?>):</b></td>
               <td><?php echo $this->errordesc; ?></td>
          </tr>
          </table>
        <?php
    }  
   /* return last result ID */
   function lastID($id=-1){
   if($id == -1)
      $res = @mysql_insert_id();
    else
        $res = @mysql_insert_id($id);  
     
     return $res;   
   }
   /* strips stuff */
  function scriptCheck($input){
       $search = array('@<script[^>]*?>.*?</script>@si');  // Strip out javascript
       $text = preg_replace($search, '', $input);
       return mysql_escape_string($text);   
  }
  /* strip a lot of stuff */
  function clean($input, $html, $type){   
    $cleaned = $this->scriptCheck(trim($input));
    $cleaned = stripslashes($cleaned);
    if(!is_numeric($cleaned)){
        $cleaned = $this->scriptCheck($cleaned);
        if($type == "alphanum"){
          $cleaned = $this->scriptCheck(preg_replace("/[^a-zA-Z0-9s]/", "", $cleaned));
        }elseif($type == "num"){
            if(is_numeric($input)){
             $cleaned = $input;
            }else{ $cleaned = 0; }   
        }
    }
    if($html == "editor"){
       $cleaned = trim($cleaned);   
    }
    return $cleaned;   
   }
   
   
   
       
   }
   
       
  
  ?>
