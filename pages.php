
<?php
/**
 * favorchat application
 * Developed by mragank shekhar soni
 * @package mssinfotect-epoojapath
 * source : http://mssinfotech.com
 */
require 'config.php';
$id="";
$pageDetail=$objpage->pageDetail($_GET["page_id"]);
//echo "<pre>";print_r($pageDetail);exit;
$smarty->assign("meta_title", $pageDetail["heading"]);
$smarty->assign("meta_tag", $pageDetail["meta_keywords"]);
$smarty->assign("meta_description", $pageDetail["meta_description"]);
$smarty->assign("pageDetail", $pageDetail);
$smarty->assign("faq", $objextra->faq());
$reff=URL_ROOT."register.html?referid=".$_SESSION["user"]["uname"];
$smarty->assign("reff",$reff);
	$smarty->assign("package",$objdeal->getMemberships());
	
//$smarty->assign("getMemberships",$objdeal->getMemberships());
//echo "<pre>";print_r($objdeal->getMemberships());exit;
if(trim($pageDetail["external"])!=""){
	$name=$pageDetail["external"];
	$smarty->assign("file_to_show",$name);
}
$smarty->display('pages.html');