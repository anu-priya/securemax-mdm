    <?php if(basename($_SERVER['PHP_SELF']) == 'index.php') {?>
    </div><!-- /.login-box -->
    
    <div class="text-center">
      <img src="public/dist/img/powered%20by.png" alt="Powered By">
      <br><br>
      <span style="color:#aaa">Copyright &copy; 2015-2016. Code Corporation. All rights reserved.</span>
    </div>

    <!-- jQuery 2.1.4 -->
    <script src="public/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="public/bootstrap/js/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script src="public/plugins/iCheck/icheck.min.js"></script>
    <script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
      });
    </script>
    <?php } else {?>
      <footer class="main-footer">
        <strong>Copyright &copy; 2015-2016 <a href="#">Alten Calsoftlabs</a>.</strong> All rights reserved.
      </footer>
    </div><!-- ./wrapper -->
    
    <!-- jQuery 2.1.4 -->
    <script src="public/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="public/bootstrap/js/bootstrap.min.js"></script>
    
    <?php if(in_array(basename($_SERVER['PHP_SELF']), array('customerslist.php', 'userlist.php', 'licenselist.php'))) {?>
    <!-- DataTables -->
    <script src="public/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="public/plugins/datatables/dataTables.bootstrap.min.js"></script>
    <?php }?>
    
    <!-- SlimScroll 1.3.0 -->
    <script src="public/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="public/plugins/fastclick/fastclick.min.js"></script>
    <!-- AdminLTE App -->
    <script src="public/dist/js/app.min.js"></script>
    
    <?php if(basename($_SERVER['PHP_SELF']) == 'dashboard.php') {?>
    <!-- Sparkline -->
    <script src="public/plugins/sparkline/jquery.sparkline.min.js"></script>
    <!-- jvectormap -->
    <script src="public/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="public/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!-- ChartJS 1.0.1 -->
    <script src="public/plugins/chartjs/Chart.min.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="public/dist/js/pages/dashboard2.js"></script>
    <?php }?>
    <!-- AdminLTE for demo purposes -->
    <script src="public/dist/js/demo.js"></script>
    <?php if(in_array(basename($_SERVER['PHP_SELF']), array('customerslist.php', 'userlist.php', 'licenselist.php'))) {?>
    <script src="public/js/jquery.confirm.min.js"></script>
    <!-- page script -->
    <script>
      $(function () {
        $("#example1").DataTable();
        //$('#user-list').DataTable();
        //$(".confirm").confirm();
      });
      $('#editcancel').click( function(){
    		window.location = site_url + 'admin/user/list.php';
    		return false;
    	});
    </script>
    <?php }?>
    <?php if(in_array(basename($_SERVER['PHP_SELF']), array('adduser.php'))) {?>
    <!-- page script -->
    <script>
      $(function () {
        $('#editcancel').click( function() {
          window.location = 'userlist.php';
          return false;
        });
      });
    </script>
    <?php }?>
    <?php if(in_array(basename($_SERVER['PHP_SELF']), array('createlicense.php'))) {?>
    <script>
    function toggleBoxes(licenceType) {
      if(licenceType == "Trial") {
        document.forms[0].licenseLevel.disabled = true;
        document.forms[0].validityType.disabled = true;
        document.forms[0].validity.disabled = true;
        document.forms[0].licenseLevelId.value = "1";
        document.forms[0].validityTypeId.value = "minute";
        document.forms[0].validityValue.value = "1";
      } else if (licenceType == "LifeTime") {
        document.forms[0].licenseLevel.disabled = false;
        document.forms[0].validityType.disabled = true;
        document.forms[0].validity.disabled = true;
        document.forms[0].validityTypeId.value = "minute";
        document.forms[0].validityValue.value = "1";
      }
      else {
        document.forms[0].licenseLevel.disabled = false;
        document.forms[0].validityType.disabled = false;
        document.forms[0].validity.disabled = false;
      }
    } // end of toggle boxes
    
    $('#addcancel').click( function() {
      var uuid= document.forms[0].uuid.value ;
      //alert(uuid);
      //var newurl ="userlicense.php?uuid="+uuid;
      var newurl ="licenselist.php?uuid="+uuid;
      window.location.href=newurl;
    	//window.history.back();
    	return false;
    });
    </script>
    <script src="public/js/license.js"></script>
    <?php }?>
    <?php }?>
    <script src="public/js/jquery.validate.min.js"></script>
    <script src="public/js/user.js"></script>
  </body>
</html>