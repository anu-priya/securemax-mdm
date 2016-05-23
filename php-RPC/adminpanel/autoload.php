<?php
$error = '';
$session = '';
if(basename($_SERVER['PHP_SELF']) != 'index.php' && !isset($_SESSION['authorized'])) {
  header("Location:index.php");
  exit;
}
function __autoload($className) {
  if (file_exists('classes/' . $className . '.php')) {
    require_once 'classes/' . $className . '.php';
    return true;
  }
  throw new Exception("Unable to load $className.");
  //return false;
}

function getIP() {
  $ip = $_SERVER['SERVER_ADDR'];

  if (PHP_OS == 'WINNT'){
      $ip = getHostByName(getHostName());
  }

  if (PHP_OS == 'Linux'){
      $command="/sbin/ifconfig";
      exec($command, $output);
      // var_dump($output);
      $pattern = '/inet addr:?([^ ]+)/';

      $ip = array();
      foreach ($output as $key => $subject) {
          $result = preg_match_all($pattern, $subject, $subpattern);
          if ($result == 1) {
              if ($subpattern[1][0] != "127.0.0.1")
              $ip = $subpattern[1][0];
          }
      //var_dump($subpattern);
      }
  }
  return $ip;
}

