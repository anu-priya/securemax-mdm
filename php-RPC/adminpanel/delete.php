<?php
session_start();
if(isset($_GET['action'])) {
  
  $configValues = parse_ini_file('config.ini'); 
  require_once('autoload.php');
  //Check if the user exists
  $db = new Db();
  
  $action = $_GET['action'];
  switch($action) {
    case 'apt':
      $db -> query("DELETE FROM ".$configValues['AP']." where A_ID = ".$_GET['id']);
      echo $db -> affected_rows();
      exit;
    break;
    
    case 'pt':
      $db -> query("DELETE FROM ".$configValues['PT']." where ID = ".$_GET['id']);
      echo $db -> affected_rows();
      exit;
    break;
  
  }
} else {
  echo "0";
  exit;
}
?>