<?php
  defined('__bbug') or die(); 
  class ToDo {
    var $db = 0;
    var $user = 0;
    function ToDo($dblink){
       $this->db=$dblink; 
       $this->user=new User();
    }
    
    function todolists(){
        if(isset($_POST['create']))
            $this->createList($_POST['title'], $_POST['project']);
            
        if(isset($_POST['delete'])){
        	$tdid = $this->db->clean($_POST['delete'], '', '');
        	$this->db->del("todo_main", "`id`='$tdid'", 1);
        	$this->db->del("todo_list", "`tid`='$tdid'");
        	Main::message("To-do deleted.");
        }
     ?>
     	<table width="90%" id="list" class="tablesorter" border="0" align="center" cellpadding="0" cellspacing="0">
	      <thead>
	      <tr>
			<td><div id="headings">To Do Lists</div></td>
		  </tr>
	      </thead>
	      <tbody>
	      <tr class="L2">
	           <td>
	           <h3>
	           <?php if(REGISTERED == 0 || $this->user->getUID() > 0){ ?>
	           <form name="" method="POST" action="">Create list: 
	           <input name="title" /> <select name="project">
	             <?php 
	             $tpr = $this->db->query("SELECT * FROM projects ORDER BY `name` ASC");
	             while($r = mysql_fetch_array($tpr))
	                echo '<option value="'.$r['id'].'">'.$r['name'].'</option>';
	           ?>
	    </select><input type="submit" name="create" value="Create" />
	           </form></h3>
          
	          <script>
	            function addTo(id){
	            <?php
	            	$addthis = "<div id='item_".$r["id"]."'> <input type='checkbox' name=''  onclick='finish(".$r["id"].");''> ";
	            ?>
	             var itemvalue = document.getElementById('title'+id).value;
	             $.post('ajax.php', { addtodo: '1', id: id, item: document.getElementById('title'+id).value,  username: '<?php echo $_SESSION["userName"];?>', password: '<?php echo $_SESSION["passWord"];?>' }, function(data){  $('#project'+id).append("<div id='item_"+data+"'><input type='checkbox' name='' onclick='finish("+data+");'> " + itemvalue + '</div>');  } );
	             document.getElementById('title'+id).value='';   
            
             
	            }
            
	            function finish(id){
	            	$('#item_'+id).fadeOut();
	            	$.post('ajax.php', { markfinish: '1', id: id, username: '<?php echo $_SESSION["userName"];?>', password: '<?php echo $_SESSION["passWord"];?>' } );
	            }
            
	            function deletetd(id){
	            	var c = confirm("Delete this To-Do list?");
	            	if(c)
	            		document.getElementById('delete'+id).submit();
	            }
	          </script> 
	           <?php } ?>
	           <br><br>
	           <div id="todo"> 
	           <?php
	             $todos = $this->db->query("SELECT * FROM todo_main ORDER BY id DESC");
	             while($r = mysql_fetch_array($todos)){
	              echo '<h3>'.$r['title'];
              
	              // check to see if they're admin
	              if($this->user->adminCheck()){
	              	echo "<form name='' style='margin: 0; padding: 0; float: left; ' method='post' action='' id='delete$r[id]'><input type='hidden' name='delete' value='$r[id]' /></form><input type='image' src='images/bin_closed.png' onclick='deletetd($r[id]);' name='delete' />";
	              }
                
	              echo '</h3>';
	              ?>
	              <?php if(REGISTERED == 0 || $this->user->getUID() > 0){ ?>
	               <div id="itemadd">
	               <input name="title" id="title<?php echo $r['id']; ?>" class="quick" style="width: 300px;" />
	               <input type="hidden" name="project" value="<?php echo $r['id']; ?>" />
	               <input type="submit" name="add"  style="width: 50px;" onclick="addTo(<?php echo $r['id']; ?>)" value="Add" />
	               </div>
	               <?php } ?>
	               <div id="project<?php echo $r['id']; ?>" style='margin-left: 65px;'>
	               <?php
	               	$tdlist = $this->db->query("SELECT * FROM todo_list WHERE tid='".$r["id"]."' AND status='0' ORDER BY id DESC");
	               	while($t = mysql_fetch_array($tdlist)){
	               		echo "<div id='item_".$t["id"]."'>";
	               		if(REGISTERED == 0 || $this->user->getUID() > 0)
		               		echo "<input type='checkbox' name=''  onclick='finish($t[id]);'> ";
	               		echo $t['content']."</div>";
	               	}
	               ?>
	               </div>
	              <?php
	              echo '<hr>'; 
	             }
	           ?>
	           </div>
	           </td>
	      </tr>     
	      </tbody>
	      </table>
	     <?php
    	}   # end to do function
	
    function createList($title, $project){
    	if($title=="") $title = "{Untitled}"; 
      		$this->db->query_insert('todo_main', array('id' => '', 'title' => $title, 'project' => $project ) );
    }  
  }
?>
