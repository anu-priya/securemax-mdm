<?php
define('ADMIN_USERNAME','bala'); 	// Admin Username
define('ADMIN_PASSWORD','password');  	// Admin Password

///////////////// Password protect ////////////////////////////////////////////////////////////////
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
           $_SERVER['PHP_AUTH_USER'] != ADMIN_USERNAME ||$_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD) {
			Header("WWW-Authenticate: Basic realm=\"Walmart Login\"");
			Header("HTTP/1.0 401 Unauthorized");

			echo <<<EOB
				<html><body>
				<h1>Rejected!</h1>
				<big>Wrong Username or Password!</big>
				</body></html>
EOB;
			exit;
}
error_reporting(E_ALL); 
ini_set('display_errors', 1);

if($_SERVER['HTTP_HOST'] == 'localhost') {
  $hostname="localhost"; //local server name default localhost
  $username="root";  //mysql username default is root.
  $password="";       //blank if no password is set for mysql.
} else {
  $hostname="localhost"; //local server name default localhost
  $username="root";  //mysql username default is root.
  $password="";       //blank if no password is set for mysql.
}
$database="wallmart";  //database name which you created
// connect to with mysql
$con=mysqli_connect($hostname,$username,$password, $database);
if(! $con)
{
	die('Connection Failed'.mysqli_error());
}
echo("Welcome Bala");
?>