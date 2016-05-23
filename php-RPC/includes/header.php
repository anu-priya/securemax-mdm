<?php 
session_start();
include(dirname(__FILE__) . '/config.php');
define ('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].'/projects/rpc/');
define ('TEMP_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR);
if(basename($_SERVER['PHP_SELF']) != 'login.html' && basename($_SERVER['PHP_SELF']) != 'forgotpass.html' && basename($_SERVER['PHP_SELF']) != 'resetPassword.html') {
  if(!isset($_SESSION['user']) || !is_numeric($_SESSION['user'])) {
    header('Location: '.BASE_URL.'pages/user/login.html');
  }
}
spl_autoload_register(function ($classname) {
    require (dirname(__FILE__) . "/classes/" . $classname . ".php");
});

function mainHeader() {
  $headerSection = '<header class="main-header">
                      <!-- Logo -->
                      <a href="'.BASE_URL.'index.html" class="logo">
                        <!-- mini logo for sidebar mini 50x50 pixels -->
                        <span class="logo-mini"><b>RPC</b></span>
                        <!-- logo for regular state and mobile devices -->
                        <span class="logo-lg">Remote Patient Care</span>
                      </a>
                      <!-- Header Navbar: style can be found in header.less -->
                      <nav class="navbar navbar-static-top" role="navigation">
                        <!-- Sidebar toggle button-->
                        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                          <span class="sr-only">Toggle navigation</span>
                        </a>
                        <div class="navbar-custom-menu">
                          <ul class="nav navbar-nav">
                            <!-- Messages: style can be found in dropdown.less-->
                            <li class="dropdown messages-menu">
                              <ul class="dropdown-menu">
                                <!-- inner menu: contains the actual data -->
                                  <ul class="menu">
                                    <li><!-- start message -->
                                      <a href="#">
                                        <div class="pull-left">
                                          <img src="dist/img/no-user.png" class="img-circle" alt="User Image">
                                        </div>
                                        <h4>
                                          Support Team
                                          <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                      </a>
                                    </li><!-- end message -->
                                    <li>
                                      <a href="#">
                                        <div class="pull-left">
                                          <img src="'.BASE_URL.'dist/img/user3-128x128.jpg" class="img-circle" alt="User Image">
                                        </div>
                                        <h4>
                                          AdminLTE Design Team
                                          <small><i class="fa fa-clock-o"></i> 2 hours</small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                      </a>
                                    </li>
                                    <li>
                                      <a href="#">
                                        <div class="pull-left">
                                          <img src="'.BASE_URL.'dist/img/user4-128x128.jpg" class="img-circle" alt="User Image">
                                        </div>
                                        <h4>
                                          Developers
                                          <small><i class="fa fa-clock-o"></i> Today</small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                      </a>
                                    </li>
                                    <li>
                                      <a href="#">
                                        <div class="pull-left">
                                          <img src="'.BASE_URL.'dist/img/user3-128x128.jpg" class="img-circle" alt="User Image">
                                        </div>
                                        <h4>
                                          Sales Department
                                          <small><i class="fa fa-clock-o"></i> Yesterday</small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                      </a>
                                    </li>
                                    <li>
                                      <a href="#">
                                        <div class="pull-left">
                                          <img src="'.BASE_URL.'dist/img/user4-128x128.jpg" class="img-circle" alt="User Image">
                                        </div>
                                        <h4>
                                          Reviewers
                                          <small><i class="fa fa-clock-o"></i> 2 days</small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                      </a>
                                    </li>
                                  </ul>
                                </li>
                                <li class="footer"><a href="#">See All Messages</a></li>
                              </ul>
                            </li>
                            <!-- Notifications: style can be found in dropdown.less -->
                            <li class="dropdown notifications-menu">
                              <ul class="dropdown-menu">
                                <li class="header">You have 10 notifications</li>
                                <li>
                                  <!-- inner menu: contains the actual data -->
                                  <ul class="menu">
                                    <li>
                                      <a href="#">
                                        <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                      </a>
                                    </li>
                                    <li>
                                      <a href="#">
                                        <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the page and may cause design problems
                                      </a>
                                    </li>
                                    <li>
                                      <a href="#">
                                        <i class="fa fa-users text-red"></i> 5 new members joined
                                      </a>
                                    </li>
                                    <li>
                                      <a href="#">
                                        <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                                      </a>
                                    </li>
                                    <li>
                                      <a href="#">
                                        <i class="fa fa-user text-red"></i> You changed your username
                                      </a>
                                    </li>
                                  </ul>
                                </li>
                                <li class="footer"><a href="#">View all</a></li>
                              </ul>
                            </li>
                            <!-- Tasks: style can be found in dropdown.less -->
                            <li class="dropdown tasks-menu">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                              <ul class="dropdown-menu">
                                <li class="header">You have 9 tasks</li>
                                <li>
                                  <!-- inner menu: contains the actual data -->
                                  <ul class="menu">
                                    <li><!-- Task item -->
                                      <a href="#">
                                        <h3>
                                          Design some buttons
                                          <small class="pull-right">20%</small>
                                        </h3>
                                        <div class="progress xs">
                                          <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                            <span class="sr-only">20% Complete</span>
                                          </div>
                                        </div>
                                      </a>
                                    </li><!-- end task item -->
                                    <li><!-- Task item -->
                                      <a href="#">
                                        <h3>
                                          Create a nice theme
                                          <small class="pull-right">40%</small>
                                        </h3>
                                        <div class="progress xs">
                                          <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                            <span class="sr-only">40% Complete</span>
                                          </div>
                                        </div>
                                      </a>
                                    </li><!-- end task item -->
                                    <li><!-- Task item -->
                                      <a href="#">
                                        <h3>
                                          Some task I need to do
                                          <small class="pull-right">60%</small>
                                        </h3>
                                        <div class="progress xs">
                                          <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                            <span class="sr-only">60% Complete</span>
                                          </div>
                                        </div>
                                      </a>
                                    </li><!-- end task item -->
                                    <li><!-- Task item -->
                                      <a href="#">
                                        <h3>
                                          Make beautiful transitions
                                          <small class="pull-right">80%</small>
                                        </h3>
                                        <div class="progress xs">
                                          <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                            <span class="sr-only">80% Complete</span>
                                          </div>
                                        </div>
                                      </a>
                                    </li><!-- end task item -->
                                  </ul>
                                </li>
                                <li class="footer">
                                  <a href="#">View all tasks</a>
                                </li>
                              </ul>
                            </li>
                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="'.BASE_URL.'dist/img/no-user.png" class="user-image" alt="User Image">
                                <span class="hidden-xs">'.ucfirst($_SESSION["firstname"]).'</span>
                              </a>
                              <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header">
                                  <img src="'.BASE_URL.'dist/img/no-user.png" class="img-circle" alt="User Image">
                                  <p>
                                    '.ucfirst($_SESSION["firstname"]).' '.ucfirst($_SESSION["lastname"]).'
                                    <!-- <small>Member since Nov. 2012</small> -->
                                  </p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                  <div class="pull-left">
                                    <a href="'.BASE_URL.'pages/user/profile.html" class="btn btn-info pull-right">Profile</a>
                                  </div>
                                  <div class="pull-right">
                                    <a href="'.BASE_URL.'logout.php" class="btn btn-info pull-right">Sign out</a>
                                  </div>
                                </li>
                              </ul>
                            </li>
                          </ul>
                        </div>
                      </nav>
                    </header>';
      
      return $headerSection;
}

function sideMenu() {
  $folderPath = $_SERVER["SCRIPT_FILENAME"];
  $part1 = '';
  $part2 = '';
  $columns = explode('pages/', ltrim($folderPath, '/'));
  $part1 = isset($columns[0]) ? $columns[0] : '';
  $part2 = isset($columns[1]) ? str_replace("-", "_", $columns[1]) : '';
  
  // echo "<pre>"; print_r($columns); echo "</pre>"; exit;
  $body_class = $part2;
  
  $menuSection = '<!-- Left side column. contains the logo and sidebar -->
                  <aside class="main-sidebar">
                    <!-- sidebar: style can be found in sidebar.less -->
                    <section class="sidebar">
                      <!-- Sidebar user panel -->
                      <div class="user-panel">
                        <div class="pull-left image">
                          <img src="'.BASE_URL.'dist/img/no-user.png" class="img-circle" alt="User Image">
                        </div>
                        <div class="pull-left info">
                          <p>'.ucfirst($_SESSION["firstname"]).' '.ucfirst($_SESSION["lastname"]).'</p>
                          <a href="#"></i> Administrator</a>
                        </div>
                      </div>
                      <!-- sidebar menu: : style can be found in sidebar.less -->
                      <ul class="sidebar-menu">
                        <li class="treeview '.getActivePath($part2, "patient").'">
                          <a href="#">
                            <i class="fa fa-bed"></i> <span>Patients</span> <i class="fa fa-angle-left pull-right"></i>
                          </a>
                          <ul class="treeview-menu">
                            <li '.getActivePath($part2, "patient", "patients").'><a href="'.BASE_URL.'pages/patient/patients.html"><i class="fa fa-circle-o"></i> View Patients</a></li>
                            <li '.getActivePath($part2, "patient", "addPatient").'><a href="'.BASE_URL.'pages/patient/addPatient.html"><i class="fa fa-circle-o"></i> Add Patient</a></li>
                            <li '.getActivePath($part2, "patient", "relations").'><a href="'.BASE_URL.'pages/patient/relations.html"><i class="fa fa-circle-o"></i> View Relation</a></li>
                            <li '.getActivePath($part2, "patient", "addRelation").'><a href="'.BASE_URL.'pages/patient/addRelation.html"><i class="fa fa-circle-o"></i> Add Relation</a></li>
                          </ul>
                        </li>
                        <!--
                        <li class="treeview '.getActivePath($part2, "caregiver").'">
                          <a href="#">
                            <i class="fa fa-stethoscope"></i> <span>Care-Givers</span> <i class="fa fa-angle-left pull-right"></i>
                          </a>
                          <ul class="treeview-menu">
                            <li><a href="#"><i class="fa fa-circle-o"></i> View Caregivers</a></li>
                            <li><a href="#"><i class="fa fa-circle-o"></i> Add Caregivers</a></li>
                          </ul>
                        </li>
                        -->
                        <li class="treeview '.getActivePath($part2, "user").'">
                          <a href="#">
                            <i class="fa fa-users"></i> <span>Users</span> <i class="fa fa-angle-left pull-right"></i>
                          </a>
                          <ul class="treeview-menu">
                            <li '.getActivePath($part2, "user", "listUser").'><a href="'.BASE_URL.'pages/user/listUser.html"><i class="fa fa-circle-o"></i> View Users</a></li>
                            <li '.getActivePath($part2, "user", "addUser").'><a href="'.BASE_URL.'pages/user/addUser.html"><i class="fa fa-circle-o"></i> Add User</a></li>
                            <li '.getActivePath($part2, "user", "userRole").'><a href="'.BASE_URL.'pages/user/userRole.html"><i class="fa fa-circle-o"></i> User Roles</a></li>
                            <li '.getActivePath($part2, "user", "addRole").'><a href="'.BASE_URL.'pages/user/addRole.html"><i class="fa fa-circle-o"></i> Add Role </a></li>
                          </ul>
                        </li> 
                        
                        <li class="treeview '.getActivePath($part2, "appointment").'">
                          <a href="#">
                            <i class="fa fa-pencil-square-o"></i> <span>Appointments</span> <i class="fa fa-angle-left pull-right"></i>
                          </a>
                          <ul class="treeview-menu">
                            <li '.getActivePath($part2, "appointment", "appointments").'><a href="'.BASE_URL.'pages/appointment/appointments.html"><i class="fa fa-circle-o"></i> View Appointments</a></li>
                            <li '.getActivePath($part2, "appointment", "addAppointment").'><a href="'.BASE_URL.'pages/appointment/addAppointment.html"><i class="fa fa-circle-o"></i> Schedule an Appointment</a></li>
                            <!--<li><a href="#"><i class="fa fa-circle-o"></i> Schedule an Appointment</a></li>-->
                          </ul>
                        </li>
                        
                        <li class="treeview '.getActivePath($part2, "task").'">
                          <a href="#">
                            <i class="fa fa-tasks"></i> <span>Tasks</span> <i class="fa fa-angle-left pull-right"></i>
                          </a>
                          <ul class="treeview-menu">
                            <li '.getActivePath($part2, "task", "tasks").'><a href="'.BASE_URL.'pages/task/tasks.html"><i class="fa fa-circle-o"></i> View Tasks</a></li>
                            <li '.getActivePath($part2, "task", "addTask").'><a href="'.BASE_URL.'pages/task/addTask.html"><i class="fa fa-circle-o"></i> Add Task</a></li>
                          </ul>
                        </li>
                      </ul>
                    </section>
                    <!-- /.sidebar -->
                  </aside>';
      
  return $menuSection;
}

function getActivePath($part2, $folder, $file = '') {
  if(strpos($part2, $folder) !== FALSE) {
    if($file == '') {
      return 'active';
    } else {
      if(strpos($part2, $file) !== FALSE) {
        return 'class = "active"';
      }
    }
  }
  return '';
}