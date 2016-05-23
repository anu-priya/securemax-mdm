<?php

/**
 * Class WSModel
 *
 * This class contains everything that is related to Web Services Calls and Responses.
 */
class WSModel
{
	public static function get_ws_service($url, $arr = array(), $json = FALSE, $method = 'POST') {
	$username = 'admin';
    $password= 'admin';
    $basic = base64_encode($username.':'.$password);
    //echo TEMP_DIR;exit;
    $cookiefile = TEMP_DIR.'curl-session.txt';
    $fh = fopen($cookiefile, "a");
    //echo $fh;exit;
    // $fh = fopen($cookiefile, "a");
    fwrite($fh, "10000");
    fclose($fh);

    $ch = curl_init();
    $headr = array();
    $headr[] = 'Content-type: application/json';
    $headr[] = 'Authorization: Basic '.$basic;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    if($method != 'POST') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    } else {
        curl_setopt($ch, CURLOPT_POST, 1);
    }
    
    if(!empty($arr)) {
      $data_string = json_encode($arr);
      // echo "<pre>"; print_r($data_string); exit;
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    
    // Set the COOKIE files
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
          
    $response = curl_exec($ch);
    curl_close($ch);
    
    $reponseCheck = json_decode($response);
    // echo "<pre>"; print_r($reponseCheck); exit;
    
    if(isset($reponseCheck->status) && $reponseCheck->status == 401) {
        if (isset($_SESSION['message']) && $_SESSION['message']=="logout") {
            $_SESSION['message'] = 'Logged Out Successfully!';      
        } else {
            $_SESSION['message'] = 'Session expired. Please login again.';
        }
      header('Location: '.BASE_URL.'pages/user/login.html');
      exit;
    }
    
    if($json) {
      $responsearray = $response;
    } else {
      $responsearray = $reponseCheck;
    }
    return $responsearray;
	}
}