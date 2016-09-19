<?php
/**
 * dealondeals application
 * Developed by Nilesh Kardate
  * @package mssinfotect-dealondeals
 * source : http://mssinfotech.com
 */

require 'config.php';
$id=""; 
//echo "<pre>";print_r($_SESSION);exit;
if(isset($_SESSION["user"]["uid"]) && $_SESSION["user"]["uid"]!==""){
	$smarty->assign("meta_title", "PENDING APPROVAL :: ".$mydeatilCurrent['fullname']);
	$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
	$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
	$smarty->display('pendingapproval.html');
}
else
{
	redirect(URL_ROOT."login.html");
}
