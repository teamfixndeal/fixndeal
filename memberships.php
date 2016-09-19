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
	$roll=$db->getRows("select * from ".ROLL."");
	//echo "<pre>";print_r($mydeatilCurrent);exit;
	$smarty->assign("package",$objdeal->getMemberships());
	
	
	
	//echo "<pre>";print_r($objdeal->getMemberships());exit;
	$smarty->assign("meta_title", "Profile :: ".$mydeatilCurrent['fullname']);
	$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
	$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
	$smarty->assign("getMemberships",$objdeal->getMemberships());
	//echo "<pre>";print_r($objdeal->getMemberships());exit;
	$smarty->display('memberships.html');
	
}
else
{
	$smarty->assign("package",$objdeal->getMemberships());
	$smarty->assign("slider",$objindex->slider());
	$smarty->assign("category",$objindex->getCategory());
	
	$smarty->assign("extra_content",$objindex->extra_content("Introducing"));
	$smarty->assign("footermenu",$objpage->mymenu("footer"));
	$smarty->assign("side_silder",$objindex->slider("SIDE"));
	$smarty->assign("meta_title", $LinksDetails["general_meta_title"]);
	$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
	$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
	//echo "<pre>";print_r($objindex->slider());exit;
	$smarty->display('login.html');

}
