<?php

/**
 * dealondeals application
 * Developed by Nilesh Kardate
  * @package mssinfotect-dealondeals
 * source : http://mssinfotech.com
 */
require 'config.php';
$id=""; 
$vari=$objdeal->getSoldReview($_GET['username']);
	$smarty->assign("getSoldReview",$vari);
	$uid=$db->getVal("select id from ".SITE_USER." where username='".$_GET['username']."'");
	$userdetail=$objuser->userdetail($uid);
	$smarty->assign("userdetailon",$userdetail);
	//echo "<pre>";print_r($objdeal->getSoldReview($_GET['username']));echo $db->getLastQuery();exit;
	$smarty->assign("extra_content",$objindex->extra_content("Introducing"));

	$smarty->assign("footermenu",$objpage->mymenu("footer"));

	$smarty->assign("side_silder",$objindex->slider("SIDE"));

	$smarty->assign("meta_title", $LinksDetails["general_meta_title"]);

	$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);

	$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);

	$smarty->display('userprofile.html');

