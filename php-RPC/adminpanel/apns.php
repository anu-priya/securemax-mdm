<?php
function pushNotification($cgID, $db, $action='new') {
  define( 'API_ACCESS_KEY', 'AIzaSyCOFJYCzmYrZT3kf-ZulUdRBkABWqDKX5I');
  $cgDevice = $db -> select("SELECT * FROM notificationregistry where CareGiver_ID = '".$cgID."'");
  $registrationIds = array($cgDevice[0]['DeviceRegisterID']);

  // prep the bundle
  $msg = array
  (
      'message'     => 'New Appointment Created.',
      'title'        => 'Remote Patient Care'
  );
  
  if($action == 'edit') {
    $msg['message'] = "Appointment Updated";
  }

  $fields = array
  (
    'registration_ids'  => $registrationIds,
    'data'              => $msg
  );
   
  $headers = array
  (
      'Authorization: key=' . API_ACCESS_KEY,
      'Content-Type: application/json'
  );
   
  $ch = curl_init();
  curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
  curl_setopt( $ch,CURLOPT_POST, true );
  curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
  curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
  curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
  $result = curl_exec($ch );
  curl_close( $ch );
  
  echo $result;
}

?>

