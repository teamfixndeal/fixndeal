<?php
/**
 * dealondeals application
 * Developed by Nilesh Kardate
  * @package mssinfotect-dealondeals
 * source : http://mssinfotech.com
 */

require 'config.php';
$id=""; 
//echo "<pre>";print_r($_SERVER); print_r(getBrowser());exit;
$smarty->assign("country",$objextra->getCountryList());
$smarty->assign("meta_title", $LinksDetails["general_meta_title"]);
$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
$smarty->assign("state",$objextra->getStateList());
$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
$smarty->assign("category",$objindex->getcategory());


//if resend otp erify otp and skip 
$smarty->display('otpverify.html');

