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
	//$mydeatilCurrent=$objuser->userdetail($id);
	$smarty->assign("meta_title", $LinksDetails["general_meta_title"]);
	$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
	$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
	$smarty->assign("country",$objextra->getCountryList());
	$smarty->assign("state",$objextra->getStateList());
	//echo "<pre>";print_r($objextra->getStateList());exit;echo "</pre>";
	$smarty->display('dashboard.html');
}
else
{
	
	
	$smarty->assign("slider",$objindex->slider());
	$smarty->assign("extra_content",$objindex->extra_content("Introducing"));
	$smarty->assign("footermenu",$objpage->mymenu("footer"));
	$smarty->assign("side_silder",$objindex->slider("SIDE"));
	$smarty->assign("meta_title", $LinksDetails["general_meta_title"]);
	$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
	$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
	$smarty->display('signUp.html');
}
