<?php
session_start();
$configValues = parse_ini_file('config.ini'); 
require_once('autoload.php');

//Check if the user exists
$db = new Db();
$user = $db -> select("SELECT au.`name`, al.visitedtime, al.ip FROM ".$configValues['AU']." au left join ".$configValues['AL']." al on au.id = al.aid WHERE au.id='".$_SESSION['aid']."'");

$lastVisited = date('jS F, Y H:i:s', strtotime($user[0]['visitedtime']));
$ip = $user[0]['ip'];

if(isset($_GET['pid'])) {
  $patients = $db -> select("SELECT P_ID, PatientName, Gender, DOB, Phone, Email, Address, Location from ".$configValues['PT']." where id=".$_GET['pid']);
  $location = explode(",", $patients[0]['Location']);
  $lat = trim($location[0]);
  $long = trim($location[1]);
}

if(isset($_POST['UpdatePatient'])) {
  
  $patientname = $db->quote($_POST['patientname']);
  $gender = $db->quote($_POST['gender']);
  $dob = date("d-m-Y", strtotime($db->quote($_POST['dob'])));
  $phone = $db->quote($_POST['phone']);
  $email = $db->quote($_POST['email']);
  $address = $db->quote($_POST['address']);
  $location = $db->quote($_POST['latitude']).', '.$db->quote($_POST['longitude']);
  
  $db->query("UPDATE ".$configValues['PT']." SET PatientName = '".$patientname."', Gender = '".$gender."', DOB = '".$dob."', Phone = '".$phone."', Email = '".$email."', Address = '".$address."', Location = '".$location."' WHERE ID = ".$_GET['pid']);
  
  header("Location:patients.php");
  exit;
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
    <script type="text/javascript" src='http://maps.google.com/maps/api/js?sensor=false&libraries=places'></script>
    <script src="locationpicker.jquery.min.js"></script>
    
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
        
        $( "#datetimepicker" ).datepicker({
          showOn: "button",
          changeYear:true,
          yearRange : 'c-100:c+0',
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
                <li id="current"><a href="patients.php">Patients</a></li>
                <li><a href="caregiver.php">Care Giver / Doctor</a></li>
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
      <div class="grid_6">
        <div class="module">
          <h2><span>Update Patient</span></h2>
          <div class="module-body">
            <!--<div>
              <span class="notification n-success">Success notification.</span>
            </div>-->
            <p>
              <label>Patient Name</label>
              <input type="text" name="patientname" id="patientname" value="<?php echo $patients[0]['PatientName'];?>" />
            </p>
            <p>
              <label>Gender</label>
              <select class="input-short" name="gender" id="gender" >
                <option value="M" <?php if($patients[0]['Gender'] == 'M') echo "selected"; ?>>Male</option>
                <option value="F" <?php if($patients[0]['Gender'] == 'F') echo "selected"; ?>>Female</option>
              </select>
            </p>
            <p>
              <label>Date of Birth</label>
              <input type="text" name="dob" id="datetimepicker" value="<?php echo date('m/d/Y', (strtotime($patients[0]['DOB'])));?>" />
            </p>
            <p>
              <label>Phone</label>
              <input type="text" name="phone" id="phone" maxlength=10 value="<?php echo $patients[0]['Phone'];?>" />
            </p>
            <p>
              <label>Email</label>
              <input type="text" name="email" id="email" value="<?php echo $patients[0]['Email'];?>"/>
            </p>
            <p>
              <label>Address</label>
              <textarea rows="5" class="input-long" name='address' id='address' maxlength="200"><?php echo $patients[0]['Address'];?></textarea>
            </p>
            <fieldset>
              <input class="submit-green" type="submit" name="UpdatePatient" value="Update" /> 
              <input class="submit-gray" type="button" onClick="location.href='patients.php'" value="Cancel" />
            </fieldset>
          </div> <!-- End .module-body -->
        </div>  <!-- End .module -->
        <div style="clear:both;"></div>
      </div> <!-- End .grid_8 -->
      <div class="grid_6">
        <div class="module">
          <h2><span>Patient Location</span></h2>
          <div class="module-body">
            <div class="form-group">
              <label class="col-sm-2 control-label">Location:</label>
              <div class="col-sm-10"><input type="text" name='maplocation' class="input-long" id="us3-address"/></div>
            </div>
            <input type="hidden" class="input-long" id="us3-radius"/>
            <div style="clear:both;"></div>
            <br />
            <div id="us3" style="width: 550px; height: 300px;"></div>
            <div class="clearfix">&nbsp;</div>
            <div>
              <p>
                <label>Latitude</label>
                <input type="text" name='latitude' class="input-short" style="width: 110px" id="us3-lat"/>
              </p>
              <p>
                <label>Longitude</label>
                <input type="text" name='longitude' class="input-short" style="width: 110px" id="us3-lon"/>
              </p>
            </div>
            <div class="clearfix"></div>
            <script>$('#us3').locationpicker({
                  location: {latitude: <?php echo $lat;?>, longitude: <?php echo $long;?>},
                  radius: 50,
                  scrollwheel: false,
                  inputBinding: {
                      latitudeInput: $('#us3-lat'),
                      longitudeInput: $('#us3-lon'),
                      radiusInput: $('#us3-radius'),
                      locationNameInput: $('#us3-address')
                  },
                  enableAutocomplete: true,
                  onchanged: function (currentLocation, radius, isMarkerDropped) {
                      // Uncomment line below to show alert on each Location Changed event
                      //alert("Location changed. New location (" + currentLocation.latitude + ", " + currentLocation.longitude + ")");
                  }
            });</script>
          </div> <!-- End .module-body -->
        </div>  <!-- End .module -->
        <div style="clear:both;"></div>
      </div><!-- End .grid_4 -->
      <div style="clear:both;"></div>
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