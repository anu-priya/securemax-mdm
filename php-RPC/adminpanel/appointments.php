<?php
session_start();
$configValues = parse_ini_file('config.ini'); 
require_once('autoload.php');
//Check if the user exists
$db = new Db();

/*
$user = $db -> select("SELECT au.`name`, al.visitedtime, al.ip FROM ".$configValues['AU']." au left join ".$configValues['AL']." al on au.id = al.aid WHERE au.id='".$_SESSION['aid']."'");

$lastVisited = date('jS F, Y H:i:s', strtotime($user[0]['visitedtime']));
$ip = $user[0]['ip'];
*/

$appointments = $db -> select("SELECT ap.id, ap.A_ID, ap.A_Date, pt.PatientName, pt.Phone, cg.Name, cg.PhoneNo from ".$configValues['AP']." ap JOIN ".$configValues['PT']." pt on ap.P_ID = pt.P_ID JOIN ".$configValues['CG']." cg on ap.AssignedTo = cg.ID where ap.status=0 order by ap.A_Date desc");

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
		<script type="text/javascript" src="jquery-1.12.0.min.js"></script>
        
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
        
        $('.aptdel').click(function() {
          var delID = $(this).attr('data-id');
          
          var checkstr =  confirm('Are you sure you want to delete this Appointment?');
          if(checkstr == true){
            $.ajax({
              method: 'GET',
              url: "delete.php",
              data: {action: "apt", id: delID}
            }).done(function( data ) {
              if(data > 0) {
                $('#tr_'+delID).remove();
                alert("Appointment deleted successfully");
              }
            });
          }else{
            return false;
          }
        });
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
      <!-- Dashboard icons -->
      <!--<div class="grid_12">
        <a href="" class="dashboard-module">
          <img src="Crystal_Clear_write.gif" width="64" height="64" alt="edit" />
          <span>New article</span>
        </a>
        <a href="" class="dashboard-module">
          <img src="Crystal_Clear_file.gif" width="64" height="64" alt="edit" />
          <span>Upload file</span>
        </a>
        <a href="" class="dashboard-module">
          <img src="Crystal_Clear_files.gif" width="64" height="64" alt="edit" />
          <span>Articles</span>
        </a>
        <a href="" class="dashboard-module">
          <img src="Crystal_Clear_calendar.gif" width="64" height="64" alt="edit" />
          <span>Calendar</span>
        </a>
        <a href="" class="dashboard-module">
          <img src="Crystal_Clear_user.gif" width="64" height="64" alt="edit" />
          <span>My profile</span>
        </a>
        <a href="" class="dashboard-module">
          <img src="Crystal_Clear_stats.gif" width="64" height="64" alt="edit" />
          <span>Stats</span>
        </a>
        <a href="" class="dashboard-module">
          <img src="Crystal_Clear_settings.gif" width="64" height="64" alt="edit" />
          <span>Settings</span>
        </a>
        <div style="clear: both"></div>
      </div>--> <!-- End .grid_12 -->
            
      <!-- Account overview -->
      <!--<div class="grid_5">
        <div class="module">
          <h2><span>Account overview</span></h2>
          <div class="module-body">
            <p>
              <strong>User: </strong><?php //echo $user[0]['name'];?><br />
              <strong>Your last visit was on: </strong><?php //echo $lastVisited;?><br />
              <strong>From IP: </strong><?php //echo $ip;?>
            </p>
          </div>
        </div>
        <div style="clear:both;"></div>
      </div>--> <!-- End .grid_5 -->
      
      <div style="clear:both;"></div>
      
      <div class="grid_12">
        <div class="bottom-spacing">
          <!-- Button -->
          <div class="float-right">
            <a href="newappointment.php" class="button">
              <span>New Appointment <img src="plus-small.gif" width="12" height="9" alt="New article" /></span>
            </a>
          </div>
          <br />
        </div>
                
                
                <!-- Example table -->
                <div class="module">
                	<h2><span>Appointments</span></h2>
                    
                    <div class="module-table-body">

                        <table id="myTable">
                        	<thead>
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th style="width:20%">Patient Name</th>
                                    <th style="width:15%">Phone</th>
                                    <th style="width:18%">Appointment Date</th>
                                    <th style="width:18%">Assigned To</th>
                                    <th style="width:15%">Caregiver Phone</th>
                                    <th style="width:9%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($appointments as $key => $value) {
                                ?>
                                  <tr id="<?php echo "tr_".$value['A_ID'];?>">
                                      <td class="align-center"><?php echo $i;?></td>
                                      <td><?php echo $value['PatientName'];?></td>
                                      <td><?php echo $value['Phone'];?></td>
                                      <td><?php echo $value['A_Date'];?></td>
                                      <td><?php echo $value['Name'];?></td>
                                      <td><?php echo $value['PhoneNo'];?></td>
                                      <td>
                                        <a href="editappointment.php?aptid=<?php echo $value['A_ID'];?>"><img src="pencil.gif" width="16" height="16" alt="edit" /></a>
                                        <a href="#" title="Delete" class='aptdel' data-id=<?php echo $value['A_ID'];?>><img src="bin.gif" width="16" height="16" alt="delete" /></a>
                                      </td>
                                  </tr>
                                <?php
                                  $i++;
                                }
                                ?>
                            </tbody>
                        </table>

                        <!--<div class="pager" id="pager">
                            <form action="">
                                <div>
                                <img class="first" src="arrow-stop-180.gif" tppabs="http://www.xooom.pl/work/magicadmin/images/arrow-stop-180.gif" alt="first"/>
                                <img class="prev" src="arrow-180.gif" tppabs="http://www.xooom.pl/work/magicadmin/images/arrow-180.gif" alt="prev"/> 
                                <input type="text" class="pagedisplay input-short align-center"/>
                                <img class="next" src="arrow.gif" tppabs="http://www.xooom.pl/work/magicadmin/images/arrow.gif" alt="next"/>
                                <img class="last" src="arrow-stop.gif" tppabs="http://www.xooom.pl/work/magicadmin/images/arrow-stop.gif" alt="last"/> 
                                <select class="pagesize input-short align-center">
                                    <option value="10" selected="selected">10</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="40">40</option>
                                </select>
                                </div>
                            </form>
                        </div>-->
                        
                        <div style="clear: both"></div>
                     </div> <!-- End .module-table-body -->
                </div> <!-- End .module -->
                
                
                     <!--<div class="pagination">           
                		<a href="" class="button"><span><img src="arrow-stop-180-small.gif" tppabs="http://www.xooom.pl/work/magicadmin/images/arrow-stop-180-small.gif" height="9" width="12" alt="First" /> First</span></a> 
                        <a href="" class="button"><span><img src="arrow-180-small.gif" tppabs="http://www.xooom.pl/work/magicadmin/images/arrow-180-small.gif" height="9" width="12" alt="Previous" /> Prev</span></a>
                        <div class="numbers">
                            <span>Page:</span> 
                            <a href="">1</a> 
                            <span>|</span> 
                            <a href="">2</a> 
                            <span>|</span> 
                            <span class="current">3</span> 
                            <span>|</span> 
                            <a href="">4</a> 
                            <span>|</span> 
                            <a href="">5</a> 
                            <span>|</span> 
                            <a href="">6</a> 
                            <span>|</span> 
                            <a href="">7</a> 
                            <span>|</span> 
                            <span>...</span> 
                            <span>|</span> 
                            <a href="">99</a>
                        </div> 
                        <a href="" class="button"><span>Next <img src="arrow-000-small.gif" tppabs="http://www.xooom.pl/work/magicadmin/images/arrow-000-small.gif" height="9" width="12" alt="Next" /></span></a> 
                        <a href="" class="button last"><span>Last <img src="arrow-stop-000-small.gif" tppabs="http://www.xooom.pl/work/magicadmin/images/arrow-stop-000-small.gif" height="9" width="12" alt="Last" /></span></a>
                        <div style="clear: both;"></div> 
                     </div>-->
                
                

                
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