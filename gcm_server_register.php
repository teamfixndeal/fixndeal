<?php

// response json
$json = array();

/**
 * Registering a user device
 * Store reg id in users table
 */
if (isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["regId"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $gcm_regid = $_POST["regId"]; // GCM Registration ID
    // Store user details in db
	include_once 'config.php';
    include_once 'GCM.php';
	$gcm = new GCM();
    $res = $gcm->storeUser($name, $email, $gcm_regid);
    echo $res;
} else {
    // user details missing
}
?>