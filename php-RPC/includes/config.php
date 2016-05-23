<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

define ('BASE_WS_URL', 'http://10.1.7.95:8080/agency/');
// define ('BASE_WS_URL', 'http://115.114.95.123:8080/agency/');
define('BASE_DIR',  dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
define('WEB_ROOT', 'http://'.$_SERVER['HTTP_HOST'].'/projects/rpc/');

$ws_url = array (
  'authenticateUser' => 'login',
  //forgot password
  'resetPassword' => 'user/forgot/password',
  'changePassword' => 'user/changePassword',
  'currentUser' => 'user/currentUser',
  'allUsers' => 'user/allUsers',
  'logoutUser' => 'user/logout',
  
  'allPatients' => 'patient/all',
  'listActivePatients' => 'patient/all?status=Active',
  'listActiveDoctors' => 'user/allDoctors?status=Active',
  'listActiveCareGivers' => 'user/allCareGivers?status=Active',
  'activePatients' => 'patient/all/count?status=Active',
  'activeDoctors' => 'user/allDoctors/count?status=Active',
  'patientData' => 'patient/pId/',
  'patientUpdate' => 'patient/update',
  'patientAdd' => 'patient/registration',
  'patientDelete' => 'patient/delete/id/',
  'patientInfoDel' => 'patient/info/delete/id/',
  'patientPblmDel' => 'patient/problem/delete/id/',
  'patientHisDel' => 'patient/history/delete/id/',
  'patientSurDel' => 'patient/surgery/delete/id/',
  'patientCircleDel' => 'patient/circle/delete/id/',
  
  'allRelations' => 'patient/circle/relation/all',
  'relationData' => 'patient/circle/relation/relationId/',
  'relationUpdate' => 'patient/circle/relation/update',
  'relationAdd' => 'patient/circle/relation/add',
  
  'userCircle' => 'patient/circle/uId/',
  'patientCircle' => 'patient/circle/pId/',
  'circleUpdate' => 'patient/circle/update',
  'circleAdd' => 'patient/circle/add',
  
  //User
  'userPassUpdate' => 'user/updatePassword',
  'resetPass' => 'user/resetpass',
  'userAdd' => 'user/registration',
  //User Confirmation
  'regConf' => 'user/regitrationConfirm',
  //Current user profile
  'userProfile' => 'user/currentUser',

  'userUpdate' => 'user/update',
  'userData' => 'user/uId/',

  'allRoles' => 'user/allRoles',
  'addRole' => 'role/add',
  'updateRole' => 'role/update',
  'roleData' => 'role/roleId/',
  //search by role
  'searchRole' =>'user/roleID/',
  
  'allPrivileges' => 'user/allPrivileges',
  'addPrivilege' => 'privilege/add',
  'updatePrivilege' => 'privilege/update',
  'privilegeData' => 'privilege/privilegeId/',
  
  //Appointment
   'addAppointment' => 'appointment/add',
   'updateAppointment' => 'appointment/update',
   'searchAppointment' => 'appointment/search',
   'allAppointments' => 'appointment/all',
   'appointmentData' => 'appointment/appId',
   'appSearchDateBetween' => 'appointment/fromDateToDate',
   'appSearchByToday' => 'appointment/toDay',
   
   //doctors
   'allDoctors' => 'user/allDoctors',
   //careGivers
   'allCareGivers' => 'user/allCareGivers',
   //Tasks
   //List all Admins.
   'allAdmins' => 'user/allAdmins',
   'allTasks' => 'task/all',
   'taskAdd' => 'task/add',
   'taskData' => 'task/taskId/',
   'taskUpdate' => 'task/update',
   
   //Patient Info
  'patientInfoData' => 'patient/info/pId/',
  'patientProblemData' => 'patient/problem/pId/',
  'patientHistoryData' => 'patient/history/pId/',
  'patientSurgeryData' => 'patient/surgery/pId/',
  'patientCircleData' => 'patient/circle/pId/',
  'addPatientInfo' => 'patient/info/add',
  'updatePatientInfo' => 'patient/info/update',
  'getPatientInfoById' => 'patient/info/id/',

  'addPatientProblem' => 'patient/problem/add',
  'getPatientProblemById' => 'patient/problem/id/',
  'updatePatientProblem' => 'patient/problem/update',

  'addPatientHistory' => 'patient/history/add',
  'getPatientHistoryById' => 'patient/history/id/',
  'updatePatientHistory' => 'patient/history/update',

  'addPatientSurgery' => 'patient/surgery/add',
  'getPatientSurgeryById' => 'patient/surgery/id/',
  'updatePatientSurgery' => 'patient/surgery/update',
  //Allergy
  'patientAllergyData' => 'patient/allergy/pId/',
  'addPatientAllergy' => 'patient/allergy/add',
  'updatePatientAllergy ' => 'patient/allergy/update',
  'getPatientAllergyById' => 'patient/allergy/id/',
  'delPatientAllergy' => 'patient/allergy/delete/id/',
  
  //Medication
  'patientMedicationData' => 'patient/homemed/pId/',
  'addPatientMedication' => 'patient/homemed/add',
  'updatePatientMedication' => 'patient/homemed/update',
  'getPatientMedicationById' => 'patient/homemed/id/',
  'delPatientMedication' => 'patient/homemed/delete/id/',
  
  //Patient Circle
  'getPatientCircleById' => 'patient/circle/id/',
  'updatePatientCircle' => 'patient/circle/update',

);


$chartTitle = "Licenses Generated from " . date("F, 1 Y", strtotime("-6 months", strtotime(date('Y-m-d')))) . " to " . date("F, 1 Y", strtotime(date('Y-m-d')));