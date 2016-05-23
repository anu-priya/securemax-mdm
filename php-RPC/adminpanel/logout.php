<?php
session_start();
$configValues = parse_ini_file('config.ini'); 
require_once('autoload.php');
$ipAddress = getIP();

$db = new Db();
$rows = $db -> select("SELECT count(*) num FROM ".$configValues['AL']." WHERE aid='".$_SESSION['aid']."'");

if($rows[0]['num'] > 0) {
  $db -> query("UPDATE ".$configValues['AL']." SET visitedtime = now(), ip = '".$ipAddress."' where aid='".$_SESSION['aid']."'");
} else {
  $db -> query("INSERT INTO ".$configValues['AL']."(`aid`, `visitedtime`, `ip`) VALUES(".$_SESSION['aid'].", now(), '".$ipAddress."')" );
}

session_destroy();
header("Location:index.php");
exit;
?>