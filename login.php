<?php

/**

 * dealondeals application

 * Developed by Nilesh Kardate

  * @package mssinfotect-dealondeals

 * source : http://mssinfotech.com

 */



require 'config.php';

$id=""; 

if ($fbuser) {

	if(!isset($_SESSION["user"]) || $_SESSION["user"]["uid"]==""){

		redirect($return_url);

	}

}

elseif(isset($_SESSION["user"]["uid"]) && $_SESSION["user"]["uid"]!==""){

	redirect(URL_ROOT);

}
elseif(isset($_COOKIE['mobile']) && $_COOKIE['mobile']!="" && isset($_COOKIE['password']) && $_COOKIE['password']!="" ){
	$redirect=base64_encode(URL_ROOT);
	$this->login(array("mobile"=>$_COOKIE["mobile"],"password"=>$_COOKIE["password"],"redirect"=>$redirect));
	redirect(URL_ROOT);
}
else{
	

	$mobile="";$password="";

	if(isset($_POST["mobile"])){$mobile = $_POST["mobile"];}

	elseif(isset($_COOKIE['mobile'])){$userid = $_COOKIE['login'];}

	if(isset($_POST["password"])){$password = $_POST["password"];}

	elseif(isset($_COOKIE['password'])){$password = $_COOKIE['password'];}
	
	

	$smarty->assign("mobile",$mobile);

	$smarty->assign("password",$password);


	$smarty->assign("meta_title", "Login :: ".$LinksDetails["general_meta_title"]);

	$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);

	$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);

	$redirect = base64_encode(URL_ROOT);

	if($_REQUEST["redirect"]!="")	$redirect=$_REQUEST["redirect"];

	else	$redirect=base64_encode($_SERVER["HTTP_REFERER"]);

	$smarty->assign("redirect", $redirect);

	$smarty->display('login.html');



}

