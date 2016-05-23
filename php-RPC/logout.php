<?php
$status ='';
if(isset($_GET['status']) && !empty($_GET['status'] && ($_GET['status']=='success'))) {
  $status = "pages/user/login.html?status=success";
}
include 'includes/header.php';
WSModel::get_ws_service(BASE_WS_URL.$ws_url['logoutUser']);
unset($_SESSION);
session_destroy();
$_SESSION['message'] = "logout";
header('Location: '.BASE_URL.$status);
?>