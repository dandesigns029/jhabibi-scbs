<?php
session_start();
include_once('vendor/autoload.php');
include "../initialize.php";
$fb = new Facebook\Facebook(array(
	'app_id' => '460969326145555', // Replace with your app id
	'app_secret' => '03eb96bee45eee977b27665677945ab9',  // Replace with your app secret
	'default_graph_version' => 'v3.2',
));

$helper = $fb->getRedirectLoginHelper();
?>