<?php
session_start();
$configValues = parse_ini_file('config.ini'); 
require_once('autoload.php');
//Check if the user exists
$db = new Db();
$caregivers = $db -> select("SELECT * from ".$configValues['CG']." order by id desc");
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
        
		<!-- JQuery engine script-->
		<script type="text/javascript" src="jquery-1.3.2.min.js"></script>
        
    <!-- JQuery tablesorter plugin script-->
		<script type="text/javascript" src="jquery.tablesorter.min.js"></script>
        
		<!-- JQuery pager plugin script for tablesorter tables -->
		<script type="text/javascript" src="jquery.tablesorter.pager.js"></script>
        
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
				})
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
                <li id="current"><a href="caregiver.php">Care Giver / Doctor</a></li>
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
      <div style="clear:both;"></div>
      <div class="grid_12">
        <div class="bottom-spacing">
          <!-- Button -->
          <div class="float-right">
            <a href="newcaregiver.php" class="button">
              <span>New Caregiver / Doctor <img src="plus-small.gif" width="12" height="9" alt="New patient" /></span>
            </a>
          </div>
          <br />
        </div>
        <!-- Example table -->
        <div class="module">
          <h2><span>Care Giver / Doctor</span></h2>
          <div class="module-table-body">
            <form action="">
              <table id="myTable">
                <thead>
                  <tr>
                    <th style="width:3%">#</th>
                    <th style="width:25%">Name</th>
                    <th style="width:10%">Phone</th>
                    <th style="width:20%">Email</th>
                    <th style="width:20%">Address</th>
                    <th style="width:10%">DOB</th>
                    <th style="width:5%">Role</th>
                    <th style="width:15%"></th>
                  </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach ($caregivers as $key => $value) {
                ?>
                  <tr>
                    <td class="align-center"><?php echo $i;?></td>
                    <td><?php echo $value['Name'];?></td>
                    <td><?php echo $value['PhoneNo'];?></td>
                    <td><?php echo $value['Email'];?></td>
                    <td><?php echo $value['Address'];?></td>
                    <td><?php echo $value['DOB'];?></td>
                    <td><?php echo $value['role'];?></td>
                    <td>
                      <a href="editcaregiver.php?cgid=<?php echo $value['ID'];?>"><img src="pencil.gif" width="16" height="16" alt="edit" /></a>
                      <a href="#" title="Delete" class='cgdel' data-id=<?php echo $value['ID'];?>><img src="bin.gif" width="16" height="16" alt="delete" /></a>
                    </td>
                  </tr>
                <?php
                $i++;
                }
                ?>
                </tbody>
              </table>
            </form>
            <div style="clear: both"></div>
          </div> <!-- End .module-table-body -->
        </div> <!-- End .module -->
      </div> <!-- End .grid_12 -->
      <div style="clear:both;"></div>
    </div> <!-- End .container_12 -->
		<!-- Footer -->
    <div id="footer">
      <div class="container_12">
        <div class="grid_12">
          <!-- You can change the copyright line for your own -->
          <p>&copy; <?php echo date('Y');?>. <a href="http://www.altencalsoftlabs.com" title="Alten CalsoftLabs">Alten CalsoftLabs</a></p>
        </div>
      </div>
      <div style="clear:both;"></div>
    </div> <!-- End #footer -->
	</body>
</html>