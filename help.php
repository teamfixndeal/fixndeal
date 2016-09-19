<?php
/**
 * dealondeals application
 * Developed by Nilesh Kardate
  * @package mssinfotect-dealondeals
 * source : http://mssinfotech.com
 */

require 'config.php';
$id=""; 
$smarty->assign("meta_title", " HELP :: ".$mydeatilCurrent['fullname']);
$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
$List=$db->getRows("select * from ".CMS." where mobilemenu=1");

//echo "<pre>";print_r($List);exit;
$smarty->assign("List",$List);	
$smarty->display('help.html');
