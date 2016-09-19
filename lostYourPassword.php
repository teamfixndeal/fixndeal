<?php
/**
 * dealondeals application
 * Developed by Nilesh Kardate
  * @package mssinfotect-dealondeals
 * source : http://mssinfotech.com
 */

require 'config.php';
if(isset($_SESSION["user"]["uid"]) && $_SESSION["user"]["uid"]!==""){
	redirect(URL_ROOT);
}
else
{
	$smarty->assign("meta_title", "Lost Your Password :: ".$LinksDetails["site_name"]);
	$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
	$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
	$smarty->display('lostYourPassword.html');

}
