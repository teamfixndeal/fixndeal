<?php

/**

 * dealondeals application

 * Developed by Nilesh Kardate

  * @package mssinfotect-dealondeals

 * source : http://mssinfotech.com

 */



require 'config.php';

$id=""; 

if(isset($_SESSION["user"]["uid"]) && $_SESSION["user"]["uid"]!==""){

	$smarty->assign("meta_title", "MY ADS :: ".$mydeatilCurrent['fullname']);

	$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);

	$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);

	$smarty->assign("MyPostingListactive",$objdeal->MyPostingList($_SESSION['user']['uid']));

	$smarty->assign("MyPostingListinactive",$objdeal->MyPostingListinactive($_SESSION['user']['uid']));

	$smarty->assign("MyPostingListpending",$objdeal->MyPostingList($_SESSION['user']['uid'],'3'));

	//echo "<pre>";print_r($objdeal->MyPostingList($_SESSION['user']['uid'],'1'));exit;

	$smarty->display('settings.html');

}

else{

	redirect(URL_ROOT."login.html");

}