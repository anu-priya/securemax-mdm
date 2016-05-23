<?php
class User {
  function __construct() {
    
  }
  
  static function Authenticate($user) {
    $_SESSION['user'] = $user->id;
    $_SESSION['name'] = $user->userName;
    $_SESSION['firstname'] = $user->firstName;
    $_SESSION['lastname'] = $user->lastName;
    $_SESSION['email'] = $user->email;
    $_SESSION['roles'] = $user->roles;
    return;
  }
}