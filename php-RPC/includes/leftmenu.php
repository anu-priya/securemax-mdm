      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="public/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
              <p><?php echo @$_SESSION['userdetails']['firstname'].' '.@$_SESSION['userdetails']['lastname'];?></p>
              <p class="small"><i><?php echo @$_SESSION['userdetails']['usertypename'];?></i></p>
            </div>
          </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="<?php if(basename($_SERVER['PHP_SELF']) == 'dashboard.php') echo "active"; ?> treeview">
              <a href="dashboard.php">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span> 
              </a>
            </li>
            <li class="<?php if(in_array(basename($_SERVER['PHP_SELF']), array("customerslist.php", "addcustomers.php", "editcustomers.php"))) echo "active"; ?> treeview">
              <a href="#">
                <i class="fa fa-th"></i> <span>Customers</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li class="<?php if(basename($_SERVER['PHP_SELF']) == 'customerslist.php') echo "active"; ?>"><a href="customerslist.php"><i class="fa fa-circle-o"></i> Customers List</a></li>
                <li class="<?php if(basename($_SERVER['PHP_SELF']) == 'addcustomers.php') echo "active"; ?>"><a href="addcustomers.php"><i class="fa fa-circle-o"></i> Add Customer</a></li>
              </ul>
            </li>
            <li class="<?php if(in_array(basename($_SERVER['PHP_SELF']), array("userlist.php", "useradd.php", "useredit.php"))) echo "active"; ?> treeview">
              <a href="#">
                <i class="fa fa-users"></i> <span>Users</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li class="<?php if(basename($_SERVER['PHP_SELF']) == 'userlist.php') echo "active"; ?>"><a href="userlist.php"><i class="fa fa-circle-o"></i> Users List</a></li>
                <li class="<?php if(basename($_SERVER['PHP_SELF']) == 'adduser.php') echo "active"; ?>"><a href="adduser.php"><i class="fa fa-circle-o"></i> Add User</a></li>
              </ul>
            </li>
            <!--<li class="<?php if(in_array(basename($_SERVER['PHP_SELF']), array("licenselist.php", "createlicense.php"))) echo "active"; ?> treeview">
              <a href="#">
                <i class="fa fa-th"></i> <span>Licenses</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?php echo $url; ?>admin/licenselist.php"><i class="fa fa-circle-o"></i> Licenses List</a></li>
                <li><a href="<?php echo $url; ?>admin/createlicense.php"><i class="fa fa-circle-o"></i> Create License</a></li>
              </ul>
            </li>-->
            
            
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>