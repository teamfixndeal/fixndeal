<?php
/**
 * dealondeals application
 * Developed by Nilesh Kardate
  * @package mssinfotect-dealondeals
 * source : http://mssinfotech.com
 */

require 'config.php';
$id=""; 
	$smarty->assign("meta_title", "MY ADS :: ".$mydeatilCurrent['fullname']);
	$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
	$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
	$smarty->assign("MyPostingListactive",$objdeal->MyPostingList($_SESSION['user']['uid'],'1'));
	$smarty->assign("MyPostingListinactive",$objdeal->MyPostingList($_SESSION['user']['uid']),'3');
	$smarty->assign("MyPostingListpending",$objdeal->MyPostingList($_SESSION['user']['uid'],'0'));
	$smarty->assign("MyPostingClosed",$objdeal->MyPostingList($_SESSION['user']['uid'],'2'));
	//echo "<pre>";print_r($objdeal->MyPostingList($_SESSION['user']['uid'],'0'));echo $db->getErMsg();exit;
	$smarty->display('postings.html');
