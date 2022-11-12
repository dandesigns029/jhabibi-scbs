<?php
include_once('fb-config.php');
if(isset($_SESSION['fbUserId']) and $_SESSION['fbUserId']!=""){
    $dasboard = base_url;
	header('location: $dasboard');
	exit;
}
$permissions = array('email'); // Optional permissions
$log_url = base_url.'facebook/fb-callback.php';
$loginUrl = $helper->getLoginUrl($log_url, $permissions);
// echo htmlspecialchars($loginUrl);
header("Location: $loginUrl");
?>