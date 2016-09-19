<?php
/**
 * dealondeals application
 * Developed by Nilesh Kardate
  * @package mssinfotect-dealondeals
 * source : http://mssinfotech.com
 */

require 'config.php';
$id=""; 

	$smarty->assign("meta_title", "REGISTER :: ".$LinksDetails["general_meta_title"]);
	
	$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
	$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
	$username=$db->getVal("select username from ".SITE_USER." where id='".$_SESSION['user']['uid']."'");
	
	$smarty->assign("getChatUserList",$objdeal->getChatUserList($username));
		$smarty->assign("chat",$objdeal->fetchMessage($_GET['senderid'],$_GET['rid']));

	
	//echo "<pre>";print_r($objdeal->fetchMessage($_GET['senderid'],$_GET['rid']));exit;


	$smarty->display('mychat.html');

