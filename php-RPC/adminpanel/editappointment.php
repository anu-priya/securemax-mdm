<?php
session_start();
$configValues = parse_ini_file('config.ini'); 
require_once('autoload.php');
require_once('apns.php');
//Check if the user exists
$db = new Db();
$user = $db -> select("SELECT au.`name`, al.visitedtime, al.ip FROM ".$configValues['AU']." au left join ".$configValues['AL']." al on au.id = al.aid WHERE au.id='".$_SESSION['aid']."'");

$lastVisited = date('jS F, Y H:i:s', strtotime($user[0]['visitedtime']));
$ip = $user[0]['ip'];

if(isset($_GET['aptid'])) {
  $appointments = $db -> select("SELECT ap.id, ap.A_ID, ap.P_ID, ap.A_Date, ap.AssignedTo from ".$configValues['AP']." ap where ap.id=".$_GET['aptid']);
//  echo "SELECT TaskID, Task from ".$configValues['TL']." ap where A_ID = ".$_GET['aptid'];
  $apttasklist = $db -> select("SELECT TaskID, Task from ".$configValues['TL']." ap where A_ID = ".$_GET['aptid']);
//  print "<pre>";
//  print_r($apttasklist);
}

if(isset($_POST['UpdateAppointment'])) {
  $caregiver = $db->quote($_POST['caregiver']);
  $patient = $db->quote($_POST['patients']);
  $aptdate = date("Y-m-d H:i:s", strtotime($db->quote($_POST['aptdate'])));
  $aptdate = str_replace(" ", "T", $aptdate);
  
  $db->query("UPDATE ".$configValues['AP']." SET P_ID = '".$patient."', A_Date = '".$aptdate."', AssignedTo = '".$caregiver."' WHERE A_ID = ".$_GET['aptid']);
  $aptid = $_GET['aptid'];
  
  $db->query("DELETE FROM ".$configValues['TL']." WHERE A_ID = ".$aptid);
  
  if(isset($_POST['tasks'])) {
    $tasks = $_POST['tasks'];
    foreach($tasks as $key=>$value) {
      $valuesplit = explode("|", $value);
      $db->query("INSERT INTO ".$configValues['TL']." (A_ID, TaskID, Task, Status) values('".$aptid."', '".$valuesplit[0]."', '".$valuesplit[1]."', 0)");
    }
  }
  
  pushNotification($caregiver, $db, 'edit');
  
  header("Location:appointments.php");
  exit;
}

$caregivers = $db -> select("SELECT * from ".$configValues['CG']." WHERE role = 'CG'");

$patients = $db -> select("SELECT * from ".$configValues['PT']."");

$tasks = $db -> select("SELECT * from ".$configValues['TK']."");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>Remote Patient Cate - Admin Panel</title>
    
    <!-- CSS Reset -->
		<link rel="stylesheet" type="text/css" href="reset.css" media="screen" />
    
    <!-- Fluid 960 Grid System - CSS framework -->
		<link rel="stylesheet" type="text/css" href="grid.css" media="screen" />
		
    <!-- IE Hacks for the Fluid 960 Grid System -->
    <!--[if IE 6]><link rel="stylesheet" type="text/css" href="ie6.css" media="screen" /><![endif]-->
		<!--[if IE 7]><link rel="stylesheet" type="text/css" href="ie.css" media="screen" /><![endif]-->
    
    <!-- Main stylesheet -->
    <link rel="stylesheet" type="text/css" href="styles.css" media="screen" />
        
    <!-- WYSIWYG editor stylesheet -->
    <link rel="stylesheet" type="text/css" href="jquery.wysiwyg.css" media="screen" />
        
    <!-- Table sorter stylesheet -->
    <link rel="stylesheet" type="text/css" href="tablesorter.css" media="screen" />
    
    <!-- Thickbox stylesheet -->
    <link rel="stylesheet" type="text/css" href="thickbox.css" media="screen" />
        
    <!-- Themes. Below are several color themes. Uncomment the line of your choice to switch to different color. All styles commented out means blue theme. -->
    <link rel="stylesheet" type="text/css" href="theme-blue.css" media="screen" />
    <!--<link rel="stylesheet" type="text/css" href="css/theme-red.css" media="screen" />-->
    <!--<link rel="stylesheet" type="text/css" href="css/theme-yellow.css" media="screen" />-->
    <!--<link rel="stylesheet" type="text/css" href="css/theme-green.css" media="screen" />-->
    <!--<link rel="stylesheet" type="text/css" href="css/theme-graphite.css" media="screen" />-->
    
    <link rel="stylesheet" type="text/css" href="./jquery.datetimepicker.css"/>
		<!-- JQuery engine script-->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <link rel="stylesheet" href="/resources/demos/style.css">
        
		<!-- JQuery WYSIWYG plugin script -->
		<script type="text/javascript" src="jquery.wysiwyg.js"></script>
        
    <!-- JQuery tablesorter plugin script-->
		<script type="text/javascript" src="jquery.tablesorter.min.js"></script>
        
		<!-- JQuery pager plugin script for tablesorter tables -->
		<script type="text/javascript" src="jquery.tablesorter.pager.js"></script>
        
		<!-- JQuery password strength plugin script -->
		<script type="text/javascript" src="jquery.pstrength-min.1.2.js"></script>
        
		<!-- JQuery thickbox plugin script -->
		<script type="text/javascript" src="thickbox.js"></script>
    
    <script type="text/javascript" src="jquery.datetimepicker.full.js"></script>
    
    <!-- Initiate WYIWYG text area -->
		<script type="text/javascript">
			$(function()
			{
        $('#wysiwyg').wysiwyg(
        {
          controls : {
            separator01 : { visible : true },
            separator03 : { visible : true },
            separator04 : { visible : true },
            separator00 : { visible : true },
            separator07 : { visible : false },
            separator02 : { visible : false },
            separator08 : { visible : false },
            insertOrderedList : { visible : true },
            insertUnorderedList : { visible : true },
            undo: { visible : true },
            redo: { visible : true },
            justifyLeft: { visible : true },
            justifyCenter: { visible : true },
            justifyRight: { visible : true },
            justifyFull: { visible : true },
            subscript: { visible : true },
            superscript: { visible : true },
            underline: { visible : true },
                  increaseFontSize : { visible : false },
                  decreaseFontSize : { visible : false }
          }
        } );
        
        $.datetimepicker.setLocale('en');
        
        //$( "#datetimepicker" ).datepicker();
        $('#datetimepicker').datetimepicker({
          dayOfWeekStart : 1,
          lang:'en',
          step:10
          /*startDate:	'2015/01/01'*/
        });
  
			});
    </script>
        
    <!-- Initiate tablesorter script -->
    <script type="text/javascript">
      $(document).ready(function() { 
        $("#myTable") 
				.tablesorter({
					// zebra coloring
					widgets: ['zebra'],
					// pass the headers argument and assing a object 
					headers: { 
						// assign the sixth column (we start counting zero) 
						6: { 
							// disable it by setting the property sorter to false 
							sorter: false 
						} 
					}
				}).tablesorterPager({container: $("#pager")}); 
      }); 
		</script>
        
    <!-- Initiate password strength script -->
		<script type="text/javascript">
      $(function() {
        $('.password').pstrength();
			});
    </script>
	</head>
	<body>
    <!-- Header -->
    <div id="header">
      <!-- Header. Status part -->
      <div id="header-status">
        <div class="container_12">
          <div class="grid_8">
            <h1>Remote Patient Care - Admin Panel</h1>
          </div>
          <div class="grid_4">
            <a href="logout.php" id="logout">Logout</a>
          </div>
        </div>
        <div style="clear:both;"></div>
      </div> <!-- End #header-status -->
            
      <!-- Header. Main part -->
      <div id="header-main">
        <div class="container_12">
          <div class="grid_12">
            <div id="logo">
              <ul id="nav">
                <li><a href="patients.php">Patients</a></li>
                <li><a href="caregiver.php">Care Giver / Doctor</a></li>
                <li id="current"><a href="appointments.php">Appointments</a></li>
              </ul>
            </div><!-- End. #Logo -->
          </div><!-- End. .grid_12-->
          <div style="clear: both;"></div>
        </div><!-- End. .container_12 -->
      </div> <!-- End #header-main -->
      <div style="clear: both;"></div>
            
    </div> <!-- End #header -->
        
		<div class="container_12">
      <div class="grid_12">
        <div class="module">
          <h2><span>Edit Appointment</span></h2>
          <div class="module-body">
            <form action="" method="post">
              <!--<div>
                <span class="notification n-success">Success notification.</span>
              </div>-->
              <p>
                <label>Care Giver</label>
                <select class="input-short" name="caregiver" id="caregiver">
                  <?php 
                  foreach ($caregivers as $key => $value) {
                  ?>
                    <option <?php if($value['ID'] == $appointments[0]['AssignedTo']) echo "selected";?> value="<?php echo $value['ID'];?>"><?php echo $value['Name'];?></option>
                  <?php
                  }
                  ?>
                </select>
              </p>
              <p>
                <label>Patient</label>
                <select class="input-short" name="patients" id="patients">
                  <?php 
                  foreach ($patients as $key => $value) {
                  ?>
                    <option <?php if($value['P_ID'] == $appointments[0]['P_ID']) echo "selected";?> value="<?php echo $value['P_ID'];?>"><?php echo $value['PatientName'];?></option>
                  <?php
                  }
                  ?>
                </select>
              </p>
              <p>
                <label>Appointment Date</label>
                <input type="text" name="aptdate" id="datetimepicker" value="<?php echo date('Y/m/d H:i', (strtotime($appointments[0]['A_Date'])));?>" />
              </p>
              <fieldset>
                <legend>Tasks</legend>
                <ul>
                  <?php 
                  foreach($tasks as $key => $value) {
                    $checked='';  
                    foreach($apttasklist as $key1 => $value1) {
                      if($value1['Task'] == $value['taskname']) {
                        $checked = 'checked';
                        break;
                      }
                    }
                  ?>
                  <li><label><input <?php echo $checked;?> type="checkbox" name="tasks[]" id="ck_task<?php echo $value['id'];?>" value="<?php echo $value['id']."|".$value['taskname'];?>"/>&nbsp;<?php echo $value['taskname'];?></label></li>
                  <?php } ?>
                </ul>
              </fieldset>
              <fieldset>
                <input class="submit-green" type="submit" name="UpdateAppointment" value="Submit" /> 
                <input class="submit-gray" type="button" onClick="location.href='appointments.php'" value="Cancel" />
              </fieldset>
            </form>
          </div> <!-- End .module-body -->
        </div>  <!-- End .module -->
        <div style="clear:both;"></div>
      </div> <!-- End .grid_12 -->
      <div style="clear:both;"></div>
    </div> <!-- End .container_12 -->
    <!-- Footer -->
    <div id="footer">
      <div class="container_12">
        <div class="grid_12">
          <!-- You can change the copyright line for your own -->
          <p>&copy; 2010. <a href="http://www.altencalsoftlabs.com" title="Alten CalsoftLabs">Alten CalsoftLabs</a></p>
        </div>
      </div>
      <div style="clear:both;"></div>
    </div> <!-- End #footer -->
	</body>
</html>