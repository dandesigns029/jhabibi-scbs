<?php
ob_start();
require_once 'vendor/autoload.php';
include "../initialize.php";
  
// init configuration
$clientID = '851011160877-cim14s13rlq29k6a06rm3colehsn6h6o.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = base_url.'google/';
   
// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");
// authenticate code from Google OAuth Flow
if(isset($_GET['code'])){
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);
    
    // get profile info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    
    $firstname = $google_account_info->given_name;
    $lastname = $google_account_info->family_name;
    $email = $google_account_info->email;
    $password = 'test123';
    $password = md5($password);
    $image_path = $google_account_info->picture;
    //Image procesing
    // $url = $image_path;
    // // Image path
    // $img = 'uploads/clients/'.rand().'.png';
    // // Save image
    // $ch = curl_init($url);
    // $fp = fopen($img, 'wb');
    // curl_setopt($ch, CURLOPT_FILE, $fp);
    // curl_setopt($ch, CURLOPT_HEADER, 0);
    // curl_exec($ch);
    // curl_close($ch);
    // fclose($fp);
    echo $success_link = base_url."login.php?scl=".$email;
    //insert into Database
    //Make database connection
    $conn = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME);
    if($conn){
        echo "Database Connection succesful";
    }else{
        echo "Database Connection Failed";
    }
    $check_exist = "SELECT * FROM `client_list` WHERE `email` = '$email'";
    $res_check = mysqli_query($conn,$check_exist);
    $count = mysqli_num_rows($res_check);
    if($count > 0){
        echo "<br>=========================================<br>";
        echo "This Id Already Associted in Database";
        echo "<br>=========================================<br>";
    }else{
        $g_profile = "INSERT INTO `client_list`(`firstname`,`lastname`,`email`,`password`,`image_path`)
        VALUES('$firstname','$lastname','$email','$password','$image_path')
        ";
        $res_ins = mysqli_query($conn,$g_profile);
        if($res_ins){
            echo "<br>=========================================<br>";
            echo "Google Profile Data Inserted Succesfully";
            echo "<br>=========================================<br>";
        }
    }
header("Location: $success_link");
} else {
    $action_link = $client->createAuthUrl();
    header ("Location: $action_link");
}
ob_end();
?>