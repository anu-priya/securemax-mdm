<?php
session_start();
$configValues = parse_ini_file('config.ini'); 
require_once('autoload.php');
if(isset($_SESSION['aid']) && $_SESSION['aid']!='') {
  header("Location:patients.php"); 
  exit;
}
if((isset($_POST['email']) && $_POST['email'] != '') || (isset($_POST['password']) && $_POST['password'] != '')) {
  // Our database object
  try {
    $db = new Db();
  } catch (Exception $e) {
    echo $e->getMessage(), "\n";
    die();
  }
  // Quote and escape form submitted values
  $email = $db -> quote($_POST['email']);
  $pass = $db -> quote($_POST['password']);
  
  //Check if the user exists
  $db = new Db();
  $rows = $db -> select("SELECT au.`id`, au.`password` FROM ".$configValues['AU']." au WHERE au.email='".$email."'");
  
  if(empty($rows)) {
    $error = "Sorry!. You are not authenticated. Please contact administrator";
  } else {
    $hashPassword = $rows[0]['password'];
    if(password_verify($pass, $hashPassword)) {
      /******************** CREATE SESSION *************************/
      $_SESSION['authorized'] = true;
      $_SESSION['email'] = $email;
      $_SESSION['aid'] = $rows[0]['id'];
      /******************** CREATE SESSION *************************/
      header("Location:patients.php");
      exit;
    } else {
      $error = "Sorry!. You are not authenticated. Please contact administrator";
    }
    
  }
} else {
  if(isset($_POST['email']) && $_POST['email'] == '') {
    $error = "Please enter your Email";
  }
  if(isset($_POST['password']) && $_POST['password'] == '') {
    $error = "Please enter your password";
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Remote Patient Care - Admin Panel</title>
    <link href="css/960.css" rel="stylesheet" type="text/css" media="all" />
    <link href="css/reset.css" rel="stylesheet" type="text/css" media="all" />
    <link href="css/text.css" rel="stylesheet" type="text/css" media="all" />
    <link href="css/login.css" rel="stylesheet" type="text/css" media="all" />
  </head>
  <body>
    <div class="container_16">
      <div class="grid_6 prefix_5 suffix_5">
        <h1>Remote Patient Care <br /> Admin Panel - Login</h1>
        <div id="login">
          <p class="tip">Please enter your login credentials!</p>
          <?php if($error != '') {?>
            <p class="error"><?php echo $error;?></p>
          <?php } ?>
          <form id="form1" name="form1" method="post" action="">
            <p>
              <label>
                <strong>Username</strong>
                <input type="text" name="email" class="inputText" id="email" autofocus='autofocus' placeholder='Your Email ID'  />
              </label>
            </p>
            <p>
              <label>
                <strong>Password</strong>
                <input type="password" name="password" class="inputText" id="password" />
              </label>
            </p>
            <p>
              <label>
                  <!--<span class="black_button"><span onClick="document.getElementById('form1').submit();">Authenticate</span></span>-->
                  <input type='submit' class="black_button" name='authenticate' id='authenticate' value='Authenticate' />
              </label>
            </p>
            <!--<label><input type="checkbox" name="checkbox" id="checkbox" />Remember me</label>-->
          </form>
          <br clear="all" />
        </div>
        <div id="forgot">
          <a href="#" class="forgotlink"><span>Forgot your username or password?</span></a>
        </div>
      </div>
    </div>
    <br clear="all" />
  </body>
</html>