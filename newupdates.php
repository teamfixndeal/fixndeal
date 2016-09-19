<?php
/**
 * favorchat application
 * Developed by mragank shekhar soni
 * @package mssinfotect-epoojapath
 * source : http://mssinfotech.com
 */
require 'config.php';
$id="";
if(isset($_SESSION["user"]["uid"])){$id=$_SESSION["user"]["uid"];
	$mydeatilCurrent=$objuser->userdetail($id);
	$smarty->assign("myDetail", $mydeatilCurrent);
}
//$cart_detail=$objcart->refreshCart();
//$pageDetail=$objpage->pageDetail($_GET["page_id"]);$smarty->assign("pageDetail", $pageDetail);
//echo "<pre>";print_r($mydeatilCurrent);echo $db->getErMsg().$db->getLastQuery();print_r($_SESSION);exit;
$content=explode("-",$_REQUEST['id']);
$cdetail=$objindex->new_updates($content[0]);
$smarty->assign("new_updates",$cdetail);
$smarty->assign("meta_title", $cdetail["Name"]);
$smarty->assign("meta_tag", $LinksDetail["meta_keywords"]);
$smarty->assign("meta_description", $LinksDetail["meta_description"]);
$smarty->assign("slider",$objindex->slider());
$smarty->assign("extra_content",$objindex->extra_content("Introducing"));
$smarty->assign("app",$objindex->extra_content("app"));
$smarty->assign("helps",$objindex->extra_content("help"));
$smarty->assign("footer_title",$objindex->extra_content("footer_title"));
$smarty->assign("thoughts",$objindex->extra_content("thought"));
$smarty->assign("WORKSPACE",$objindex->extra_content("WORKSPACE TYPES"));
$smarty->assign("POPULAR",$objindex->extra_content("POPULAR CITIES"));
$smarty->assign("Explor",$objindex->extra_content("Explore"));
$smarty->assign("featured",$objindex->extra_content("featured"));
 $smarty->assign("footermenu",$objpage->mymenu("footer"));
$smarty->assign("WORKSPACE",$objindex->workspace());

//echo "<pre>";
//print_r ($objindex->new_updates($content));exit;
//print_r ($objindex->viewassignment($content));exit;
$smarty->display('newupdates.html');