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
	$smarty->assign("meta_title", "MY OFFERS :: ".$mydeatilCurrent['fullname']);
	$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
	$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);	
	$smarty->assign("myads",$objdeal->getMyOffersPost($_SESSION['user']['uid']));
		//echo "<pre>";print_r($objdeal->getMyOffersPost($_SESSION['user']['uid']));exit;
	$smarty->display('offers.html');
}
else
{
	redirect(URL_ROOT."login.html");

}
