<?php
function __autoload($className) {
  if (file_exists('classes/' . $className . '.php')) {
    require_once 'classes/' . $className . '.php';
    return true;
  }
  throw new Exception("Unable to load $className.");
  //return false;
} 