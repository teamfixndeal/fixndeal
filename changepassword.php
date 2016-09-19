<?php
/**
 * ezyquest application
 * Developed by mragank shekhar soni
 * @package mssinfotect-favorchat
 * source : http://mssinfotech.com
 */
require 'config.php';
$id="";



$smarty->assign("myDetail", $mydeatilCurrent);
$smarty->assign("meta_title", "Change Password :: ".$mydeatilCurrent['fullname']);
$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
//$smarty->assign("silder",$objindex->slider());



	
$smarty->display('changepassword.html');
