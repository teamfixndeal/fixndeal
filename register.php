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
	redirect(URL_ROOT);
}
else
{
	$smarty->assign("meta_title", "REGISTER :: ".$LinksDetails["general_meta_title"]);
	$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
	$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
	$smarty->assign("country",$objextra->getCountryList());
	if($_REQUEST["redirect"]!="")	$redirect=$_REQUEST["redirect"];
	else	$redirect=base64_encode($_SERVER["HTTP_REFERER"]);
	$smarty->assign("redirect", $redirect);
	$smarty->display('register.html');

}
