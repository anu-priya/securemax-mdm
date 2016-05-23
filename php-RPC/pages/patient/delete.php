<?php

include '../../includes/header.php';

if(isset($_GET) && !empty($_GET)) {
    $postData = array(
      'id' => $_GET['id']
  );
  // echo "<pre>"; print_r($_GET); exit;
  $patientInfoDel = WSModel::get_ws_service(BASE_WS_URL.$ws_url['patientInfoDel'].'/'.$_GET['id'],$postData);
  echo "<pre>"; print_r($patientInfoDel); exit;
  if(isset($patientInfoDel) && $patientInfoDel->status == 1) {
    $_SESSION['message'] = 'Your account has been activated. Please login.';
    header('Location: '.BASE_URL.'pages/user/login.html');
  } else {
    $_SESSION['message'] = 'Token Expired';
  }
}
?>