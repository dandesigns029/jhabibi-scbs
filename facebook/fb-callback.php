<?php
ob_start();
include_once('fb-config.php');
try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (!isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
  exit;
}

if(!$accessToken->isLongLived()){
  // Exchanges a short-lived access token for a long-lived one
  try {
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
  } catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
    exit;
  }
}

//$fb->setDefaultAccessToken($accessToken);

# These will fall back to the default access token
$res 	= 	$fb->get('/me',$accessToken->getValue());
$fbUser	=	$res->getDecodedBody();

var_dump($res);
echo "<br><br>";
var_dump($fbUser);

$resImg		=	$fb->get('/me/picture?type=large&redirect=false',$accessToken->getValue());
$picture	=	$resImg->getGraphObject();


$profile_pic = $picture;
$fbUserId = $fbUser['id'];
$fullname = $fbUser['name'];
$name_part = explode(" ", $fullname);
$_SESSION['fbAccessToken']	=	$accessToken->getValue();

//------------------------------------------------------
//======================================================
$firstname = $name_part[0];
$lastname = $name_part[1];
$email_raw = $fbUser['email'];
$password = 'test123';
$password = md5($password);
// $image_path = $google_account_info->picture;
if($fbUser['email'] != NULL){
    // echo "Email is not NULL";
    $au_id = $email_raw;
    $success_link = base_url."login.php?scl=".$au_id;
}else{
    // echo "Email is NULL";
    $au_id = $fbUserId;
    $success_link = base_url."login.php?scl=".$au_id;
}
//insert into Database
//Make database connection
$conn = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME);
if($conn){
    echo "Database Connection succesful";
}else{
    echo "Database Connection Failed";
}
$check_exist = "SELECT * FROM `client_list` WHERE `email` = '$au_id'";
$res_check = mysqli_query($conn,$check_exist);
$count = mysqli_num_rows($res_check);
if($count > 0){
    echo "<br>=========================================<br>";
    echo "This Id Already Associted in Database";
    echo "<br>=========================================<br>";
}else{
    $f_profile = "INSERT INTO `client_list`(`firstname`,`lastname`,`email`,`password`,`image_path`)
    VALUES('$firstname','$lastname','$au_id','$password','$image_path')
    ";
    $res_ins = mysqli_query($conn,$f_profile);
    if($res_ins){
        echo "<br>=========================================<br>";
        echo "Facebook Profile Data Inserted Succesfully";
        echo "<br>=========================================<br>";
    }
}
header("Location: $success_link");
exit;
ob_end();
?>