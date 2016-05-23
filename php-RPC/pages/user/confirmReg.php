<?php
include '../../includes/header.php';

if(isset($_GET) && !empty($_GET)) {
    $postData = array(
      'token' => $_GET['token']
  );
  // echo "<pre>"; print_r($_GET); exit;
  $regConf = WSModel::get_ws_service(BASE_WS_URL.$ws_url['regConf'],$postData);
  if(isset($regConf) && $regConf->status == 1) {
    $_SESSION['message'] = 'Your account has been activated. Please login.';
    header('Location: '.BASE_URL.'pages/user/login.html');
  } else {
    $_SESSION['message'] = 'Token Expired';
  }
}
?>