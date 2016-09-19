<?php
/**
 * dealondeals application
 * Developed by Nilesh Kardate
  * @package mssinfotect-dealondeals
 * source : http://mssinfotech.com
 */

require 'config.php';

$smarty->assign("meta_title", $LinksDetails["general_meta_title"]);
$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
$id=""; 
$innerCar=array();
if(isset($_GET["subcat"])){
	$innerCar=$objindex->getInnerCategory($_GET["subcat"]);
	//echo "<pre>";print_r($innerCar);exit;
}
$smarty->assign("InnerCategory",$innerCar);

$smarty->assign("extra_content",$objindex->extra_content("Introducing"));
$smarty->assign("searchHistory",$objuser->searchHistory());
//echo "<pre>";print_r($objuser->searchHistory());exit;
$smarty->assign("footermenu",$objpage->mymenu("footer"));
$smarty->assign("meta_title", $LinksDetails["general_meta_title"]);
$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
$smarty->display('search.html');
