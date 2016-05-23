<?php
session_start();
$configValues = parse_ini_file('config.ini'); 
require_once('autoload.php');

//Check if the user exists
$db = new Db();

$ErrorMessage = '';

if(isset($_POST['AddCareGiver'])) {
  $name      = $db->quote($_POST['cgname']);
  $username  = $db->quote($_POST['username']);
  $password  = $db->quote($_POST['password']);
  $lock      = 0; //$db->quote($_POST['lock']);
  $dob       = date("d-m-Y", strtotime($db->quote($_POST['dob'])));
  $email     = $db->quote($_POST['email']);
  $phone     = $db->quote($_POST['phone']);
  $address   = $db->quote($_POST['address']);
  $vid       = $db->quote($_POST['vid']);
  $role      = $db->quote($_POST['role']);
  
  $existingCG = $db -> select("select count(*) num from ".$configValues['CG']." where Username = '".$username."'");
  if($existingCG[0]['num'] > 0) {
    $ErrorMessage = 'Sorry This Username already exists.';
  }
  else {
    $db->query("INSERT INTO ".$configValues['CG']." (Name, Username, Password, IsLocked, DOB, Email, PhoneNo, Address, videocall_id, role) values('".$name."', '".$username."', '".$password."', '".$lock."', '".$dob."', '".$email."', '".$phone."', '".$address."', '".$vid."', '".$role."')");
    $cgid = $db->insert_id();
    
    header("Location:caregiver.php");
    exit;
  }
}
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
        
    <!-- Table sorter stylesheet -->
    <link rel="stylesheet" type="text/css" href="tablesorter.css" media="screen" />
    
    <!-- Themes. Below are several color themes. Uncomment the line of your choice to switch to different color. All styles commented out means blue theme. -->
    <link rel="stylesheet" type="text/css" href="theme-blue.css" media="screen" />
    <!--<link rel="stylesheet" type="text/css" href="css/theme-red.css" media="screen" />-->
    <!--<link rel="stylesheet" type="text/css" href="css/theme-yellow.css" media="screen" />-->
    <!--<link rel="stylesheet" type="text/css" href="css/theme-green.css" media="screen" />-->
    <!--<link rel="stylesheet" type="text/css" href="css/theme-graphite.css" media="screen" />-->
    
    <link rel="stylesheet" type="text/css" href="jquery.datetimepicker.css"/>
		
    <!-- JQuery engine script-->
    <link rel="stylesheet" href="jquery-ui.css">
    <script type="text/javascript" src="jquery-1.12.0.min.js"></script>
    <script src="jquery-ui.js"></script>
        
    <!-- JQuery tablesorter plugin script-->
		<script type="text/javascript" src="jquery.tablesorter.min.js"></script>
        
		<!-- JQuery pager plugin script for tablesorter tables -->
		<script type="text/javascript" src="jquery.tablesorter.pager.js"></script>
        
    <script type="text/javascript" src="jquery.datetimepicker.full.js"></script>

    <!-- Initiate WYIWYG text area -->
		<script type="text/javascript">
			$(function()
			{
        $( "#datetimepicker" ).datepicker({
          showOn: "button",
          changeYear:true,
          yearRange : 'c-100:c',
          buttonImage: "images/calendar.gif",
          buttonImageOnly: true,
          buttonText: "Select date"
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
				});
        //.tablesorterPager({container: $("#pager")}); 
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
                <li id="current"><a href="caregiver.php">Care Giver</a></li>
                <li><a href="appointments.php">Appointments</a></li>
              </ul>
            </div><!-- End. #Logo -->
          </div><!-- End. .grid_12-->
          <div style="clear: both;"></div>
        </div><!-- End. .container_12 -->
      </div> <!-- End #header-main -->
      <div style="clear: both;"></div>
            
    </div> <!-- End #header -->
    
		<div class="container_12">
      <form action="" method="post">
      <div class="grid_12">
        <?php if($ErrorMessage != '') { ?>
          <span class="notification n-error"><?php echo $ErrorMessage;?></span>
        <?php
        }
        ?>
        <div class="module">
          <h2><span>New Care Giver / Doctor</span></h2>
          <div class="module-body">
            <p>
              <label>Name</label>
              <input type="text" name="cgname" id="cgname" maxlength=100 required autocomplete=off value="<?php echo @$_POST['cgname'];?>"/>
            </p>
            <p>
              <label>Username</label>
              <input type="text" name="username" id="username" maxlength=25 required autocomplete=off/>
            </p>
            <p>
              <label>Password</label>
              <input type="password" name="password" id="password" maxlength=8 required autocomplete=off/>
            </p>
            <p>
              <label>Date of Birth</label>
              <input type="text" name="dob" id="datetimepicker" autocomplete=off value="<?php echo @$_POST['dob'];?>"/>
            </p>
            <p>
              <label>Email</label>
              <input type="email" name="email" id="email" maxlength=100 required autocomplete=off value="<?php echo @$_POST['email'];?>"/>
            </p>
            <p>
              <label>Phone No</label>
              <input type="phone" name="phone" id="phone" maxlength=15 required autocomplete=off value="<?php echo @$_POST['phone'];?>"/>
            </p>
            <p>
              <label>Address</label>
              <textarea rows="5" class="input-long" name='address' id='address' maxlength="200"><?php echo @$_POST['address'];?></textarea>
            </p>
            <p>
              <label>Video Call ID</label>
              <input type="text" name="vid" id="vid" maxlength=10 required autocomplete=off value="<?php echo @$_POST['vid'];?>"/>
            </p>
            <p>
              <fieldset>
                <legend>Role</legend>
                <ul>
                  <li><label><input name="role" checked="checked" type="radio" value='CG'> Care Giver</label></li>
                  <li><label><input name="role" type="radio" value='DR'> Doctor</label></li>
                </ul>
              </fieldset>
            </p>
            <fieldset>
              <input class="submit-green" type="submit" name="AddCareGiver" value="Submit" /> 
              <input class="submit-gray" type="button" onClick="location.href='caregiver.php'" value="Cancel" />
            </fieldset>
          </div> <!-- End .module-body -->
        </div>  <!-- End .module -->
        <div style="clear:both;"></div>
      </div> <!-- End .grid_12 -->
      <div style="clear:both;"></div>
      <?php if($ErrorMessage != '') { 
        echo "<script>document.getElementById('username').focus();</script>";
      }
      ?>
      </form>
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