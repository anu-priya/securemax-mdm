<?php
include '../../includes/header.php';
if(isset($_SESSION['user']) && is_numeric($_SESSION['user'])) {
  // Do nothing
} else {
  header('Location: '.BASE_URL.'pages/user/login.html');
}
function stringToColorCode($str) {
  $code = dechex(crc32($str));
  $code = substr($code, 0, 6);
  return $code;
}
// function toColorCode($initial){
//   $checksum = md5($initial);
//   return array(
//     "R" => hexdec(substr($checksum, 0, 2)),
//     "G" => hexdec(substr($checksum, 2, 2)),
//     "B" => hexdec(substr($checksum, 4, 2))
//   );
// }
function ColorDarken($color, $dif=50){
    $color = str_replace('#', '', $color);
    if (strlen($color) != 6){ return '000000'; }
    $rgb = '';
    for ($x=0;$x<3;$x++){
        $c = hexdec(substr($color,(2*$x),2)) - $dif;
        $c = ($c < 0) ? 0 : dechex($c);
        $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
    }
     return '#'.$rgb;
} 
$requestMethod = $_SERVER['REQUEST_METHOD'];
switch ($requestMethod) {
	case "GET":
		$action = $_GET['action'];
		break;
    case "PUT":
    	$inputObject = (array) json_decode(file_get_contents('php://input'));
    	print_r($inputObject);exit;
	case "DELETE":
		$inputObject = (array) json_decode(file_get_contents('php://input'));
		$action = $inputObject['action'];
		$_POST = $inputObject;
		break;
	default:
		$action = $_POST['action'];
}

switch ($action) {
	case "getuserData":
		$userData = WSModel::get_ws_service(BASE_WS_URL.$ws_url['userData'].$_GET['id'], TRUE, 'GET');
		$arr_userData =  json_decode($userData);
		//echo ($arr_userData->response->userInfo->dob);
		$arr_userData->response->userInfo->dob = (date("Y-m-d H:i:s",$arr_userData->response->userInfo->dob));
		echo json_encode($arr_userData);
		break;
    case "addPatientInfo":
		$postData = array( 
		  'height' => $_POST['height'],
		  'recordedOn' => date('Y-m-d H:i:s', strtotime($_POST['recordedOn'])),
		  'weight' => $_POST['weight'],
		  'patientId' => $_POST['patientId']
		);
		$addPatientInfo = WSModel::get_ws_service(BASE_WS_URL.$ws_url['addPatientInfo'], $postData, TRUE);
        echo $addPatientInfo;
        break;
	case "updatePatientInfo":
		$postData = array( 
		  'id' => $_POST['id'],
		  'height' => $_POST['height'],
		  'recordedOn' => date('Y-m-d H:i:s', strtotime($_POST['recordedOn'])),
		  'weight' => $_POST['weight'],
		  'patientId' => $_POST['patientId']
		);
		$updatePatientInfo = WSModel::get_ws_service(BASE_WS_URL.$ws_url['updatePatientInfo'], $postData, TRUE ,'PUT');
	    echo $updatePatientInfo;
        break;
	case "getPatientInfoById":
		$getPatientInfoById = WSModel::get_ws_service(BASE_WS_URL.$ws_url['getPatientInfoById'] . $_GET['id'], TRUE, 'GET');
        echo $getPatientInfoById;
        break;
	case "allPatients":
		$allPatients = WSModel::get_ws_service(BASE_WS_URL.$ws_url['allPatients'],array(),FALSE);
		$allPatientsarr = $allPatients->response;
		$location = array();
		foreach($allPatientsarr as $key=>$value){
			$mapKey1 = "latLng";
			$mapKey2 = "name";
			array_push($location,array($mapKey1=>explode(',',$value->location),$mapKey2=>@$value->firstname.' '.@$value->lastname)); 
		}
		echo $jsonValue = json_encode($location);
		break;
	case "allAppointments":
		$allAppointments = WSModel::get_ws_service(BASE_WS_URL.$ws_url['allAppointments'],array(),FALSE);
		$allAppointmentsarr = $allAppointments->response;
		$events = $result = array();
		$rm=0;
		foreach($allAppointmentsarr as $key=>$value){ if (!isset($value->doctorName) ) continue;
			$random = stringToColorCode($value->doctorName);
			$bgColor = ColorDarken($random);
			$mapKey1 = "start";
			$mapKey2 = "title";
			$mapKey3 = "tip";
			$mapKey4 = "CareGiver Name";
			$backgroundColor = "backgroundColor";
			$borderColor = "borderColor";
			if(isset($_POST['src'])){
			array_push($events,array($mapKey1=>date('m/d/Y', strtotime($value->appointmentDate)),$mapKey2=>$value->patientName,$mapKey3=>$value->doctorName,$mapKey4=>$value->careGiverName,$backgroundColor=>$bgColor,$borderColor=>"#f56954")); 	
			}
			else {
			array_push($events,array($mapKey1=>$value->appointmentDate,$mapKey2=>$value->patientName,$mapKey3=>$value->doctorName,$mapKey4=>$value->careGiverName,$backgroundColor=>$bgColor,$borderColor=>"#f56954")); 
			}
			
			$rm++;
		}
		echo $jsonValue = json_encode($events);
		break;	

	case "addPatientProblem":
		$postData = array(
		  'patientId' => $_POST['patientId'],
		  'ageofoccurence' => $_POST['ageofoccurence'],
		  'chronicity' => $_POST['chronicity'],
		  'problemdetails' => $_POST['problemdetails']
		);
		$addPatientProblem = WSModel::get_ws_service(BASE_WS_URL.$ws_url['addPatientProblem'], $postData, TRUE);
        echo $addPatientProblem;
        break;
		
	case "getPatientProblemById":
		$getPatientProblemById = WSModel::get_ws_service(BASE_WS_URL.$ws_url['getPatientProblemById'] . $_GET['id'], TRUE, 'GET');
        echo $getPatientProblemById;
        break;
		
	case "updatePatientProblem":
		$postData = array( 
		  'id' => $_POST['id'],
		  'patientId' => $_POST['patientId'],
		  'ageofoccurence' => $_POST['ageofoccurence'],
		  'chronicity' => $_POST['chronicity'],
		  'problemdetails' => $_POST['problemdetails']
		);
		$updatePatientProblem = WSModel::get_ws_service(BASE_WS_URL.$ws_url['updatePatientProblem'], $postData, TRUE ,'PUT');
        echo $updatePatientProblem;
        break;
	
	case "addPatientHistory":
		$postData = array(
		  'patientId' => $_POST['patientId'],
		  'details' => $_POST['historyDetails'],
		  'visitedDate' => date('Y-m-d H:i:s', strtotime($_POST['visitedDate']))
		);
		$addPatientHistory = WSModel::get_ws_service(BASE_WS_URL.$ws_url['addPatientHistory'], $postData, TRUE);
        echo $addPatientHistory;
        break;
	
	case "getPatientHistoryById":
		$getPatientHistoryById = WSModel::get_ws_service(BASE_WS_URL.$ws_url['getPatientHistoryById'] . $_GET['id'], TRUE, 'GET');
		print_r($getPatientHistoryById);exit;
        echo $getPatientHistoryById;
        break;
	
	case "updatePatientHistory":
		$postData = array( 
		  'id' => $_POST['id'],
		  'patientId' => $_POST['patientId'],
		  'details' => $_POST['historyDetails'],
		  'visitedDate' => $_POST['visitedDate']
		);
		$updatePatientHistory = WSModel::get_ws_service(BASE_WS_URL.$ws_url['updatePatientHistory'], $postData, TRUE ,'PUT');
        echo $updatePatientHistory;
        break;

	case "addPatientAllergy":
		$postData = array(
		  'allergy' => $_POST['allergy'],
		  'reaction' => $_POST['reaction'],
		  'severity' => $_POST['severity'],
		  'source' => $_POST['source'],
		  'patientId' => $_POST['patientId']
		);
		$addPatientAllergy = WSModel::get_ws_service(BASE_WS_URL.$ws_url['addPatientAllergy'], $postData, TRUE);
        echo $addPatientAllergy;
        break;
	
	case "getPatientAllergyById":
		$getPatientAllergyById = WSModel::get_ws_service(BASE_WS_URL.$ws_url['getPatientAllergyById'] . $_GET['id'], TRUE);
        echo $getPatientAllergyById;
        break;
	
	case "updatePatientAllergy":
		$postData = array( 
		  'id' => $_POST['id'],
		  'allergy' => $_POST['allergy'],
		  'reaction' => $_POST['reaction'],
		  'severity' => $_POST['severity'],
		  'source' => $_POST['source'],
		  'patientId' => $_POST['patientId']
		);
		$updatePatientAllergy = WSModel::get_ws_service(BASE_WS_URL.$ws_url['updatePatientAllergy'], $postData, TRUE ,'PUT');
        echo $updatePatientAllergy;
        break;
		
	case "addPatientSurgery":
		$postData = array(
		  'patientId' => $_POST['patientId'],
		  'datePerformed' => date('Y-m-d H:i:s', strtotime($_POST['datePerformed'])),
		  'surgerydetails' => $_POST['surgerydetails']
		);
		$addPatientSurgery = WSModel::get_ws_service(BASE_WS_URL.$ws_url['addPatientSurgery'], $postData, TRUE);
        echo $addPatientSurgery;
        break;
	
	case "getPatientSurgeryById":
		$getPatientSurgeryById = WSModel::get_ws_service(BASE_WS_URL.$ws_url['getPatientSurgeryById'] . $_GET['id'], TRUE, 'GET');
        echo $getPatientSurgeryById;
        break;
		
	case "addPatientMedication":
		$postData = array(
		  'patientId' => $_POST['patientId'],
		  'frequency' => $_POST['frequency'],
		  'medicine' => $_POST['medicine'],
		  'refills' => $_POST['refills'],
		  'route' => $_POST['route']
		);
		$addPatientMedication = WSModel::get_ws_service(BASE_WS_URL.$ws_url['addPatientMedication'], $postData, TRUE);
        echo $addPatientMedication;
        break;
	
	case "getPatientMedicationById":
		$getPatientMedicationById = WSModel::get_ws_service(BASE_WS_URL.$ws_url['getPatientMedicationById'] . $_GET['id'], TRUE, 'GET');
        echo $getPatientMedicationById;
        break;
	
	case "updatePatientMedication":
		$postData = array( 
		  'id' => $_POST['id'],
		  'patientId' => $_POST['patientId'],
		  'frequency' => $_POST['frequency'],
		  'medicine' => $_POST['medicine'],
		  'refills' => $_POST['refills'],
		  'route' => $_POST['route']
		);
		$updatePatientMedication = WSModel::get_ws_service(BASE_WS_URL.$ws_url['updatePatientMedication'], $postData, TRUE ,'PUT');
        echo $updatePatientMedication;
        break;
	
	case "updatePatientSurgery":
		$postData = array( 
		  'id' => $_POST['id'],
		  'patientId' => $_POST['patientId'],
		  'datePerformed' => $_POST['datePerformed'],
		  'surgerydetails' => $_POST['surgerydetails']
		);
		$updatePatientSurgery = WSModel::get_ws_service(BASE_WS_URL.$ws_url['updatePatientSurgery'], $postData, TRUE ,'PUT');
        echo $updatePatientSurgery;
        break;
	case "updatePatientCircle":
			$postData = array( 
		  'id' => $_POST['id'],
		  'patientId' => $_POST['patientId'],
		  'dob' => date('Y-m-d H:i:s', strtotime($_POST['dob'])),
		  'email' => $_POST['email'],
		  'firstname' => $_POST['firstname'],
		  'gender' => $_POST['gender'],
		  'lastname' => $_POST['lastname'],
		  'phone' => $_POST['phone'],
		  'title' => $_POST['title'],
		  'relation' => $_POST['relation'],
		  'user' => $_POST['user']
		);
		$updatePatientCircle = WSModel::get_ws_service(BASE_WS_URL.$ws_url['updatePatientCircle'], $postData, TRUE ,'PUT');
        echo $updatePatientCircle;
        break;
	case "getPatientCircleById":
		$getPatientCircleById = WSModel::get_ws_service(BASE_WS_URL.$ws_url['getPatientCircleById'] . $_GET['id'], TRUE, 'GET');
        echo $getPatientCircleById;
		break;
	case "circleUpdate":
		$postData = array( 
		  'id' => $_POST['id'],
		  'dob' => $_POST['dob'],
		  'email' => $_POST['email'],
		  'firstname' => $_POST['firstname'],
		  'gender' => $_POST['gender'],
		  'lastname' => $_POST['lastname'],
		  'phone' => $_POST['phone'],
		  'title' => $_POST['title'],
		  'patientId' => $_POST['patientId'],
		  'relation' => $_POST['relation'],
		  'user' => $_POST['user'],
		);
		$circleUpdate = WSModel::get_ws_service(BASE_WS_URL.$ws_url['circleUpdate'], $postData, TRUE ,'PUT');
        echo $circleUpdate;
        break;
    case "addPatientCircle":
		$postData = array(
			'dob'=> date('Y-m-d H:i:s', strtotime($_POST['dob'])),
		  	'email'=> $_POST['email'],
     	  	'firstname'=> $_POST['firstname'],
      		'gender' => $_POST['gender'],
      		'lastname' => $_POST['lastname'],
      		'phone' => $_POST['phone'],
      		'title' => $_POST['title'],
	  		'patientId' => $_POST['patientId'],
	  		'relation' => $_POST['relation'],
	  		'user' => $_POST['user']
	  	);
		$addPatientCircle = WSModel::get_ws_service(BASE_WS_URL.$ws_url['circleAdd'], $postData, TRUE, 'POST');
        echo $addPatientCircle;
        break;
	case "patientInfoDel":
		$patientInfoDel = WSModel::get_ws_service(BASE_WS_URL.$ws_url['patientInfoDel'].$_POST['id'], TRUE ,'DELETE');
        echo $patientInfoDel;
        break;
    case "patientDelete":
		$patientDelete = WSModel::get_ws_service(BASE_WS_URL.$ws_url['patientDelete'].$_POST['id'], TRUE ,'DELETE');
        echo $patientDelete;
        break;
    case "patientPblmDel":
		$patientPblmDel = WSModel::get_ws_service(BASE_WS_URL.$ws_url['patientPblmDel'].$_POST['id'], TRUE ,'DELETE');
        echo $patientPblmDel;
        break;
    case "patientHisDel":
		$patientHisDel = WSModel::get_ws_service(BASE_WS_URL.$ws_url['patientHisDel'].$_POST['id'], TRUE ,'DELETE');
        echo $patientHisDel;
        break;
    case "patientSurDel":
		$patientSurDel = WSModel::get_ws_service(BASE_WS_URL.$ws_url['patientSurDel'].$_POST['id'], TRUE ,'DELETE');
        echo $patientSurDel;
        break;
    case "patientCircleDel":
		$patientCircleDel = WSModel::get_ws_service(BASE_WS_URL.$ws_url['patientCircleDel'].$_POST['id'], TRUE ,'DELETE');
        echo $patientCircleDel;
        break;
    default:
        echo "Your favorite color is neither red, blue, nor green!";
}
?>