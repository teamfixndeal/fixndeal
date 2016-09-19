<?php
/**
 * dealondeals application
 * Developed by Nilesh Kardate
  * @package mssinfotect-dealondeals
 * source : http://mssinfotech.com
 */
require 'config.php';
$id="";
$cityId=$db->getVal("select id from citystatecountry where name='".$_SESSION["city"]."'");
$countAd=$db->getVal("select count(id) from ".POSTS." where status=1 and ptype=1 and  is_parent=0");
$smarty->assign("MostPopular",$objdeal->MostPopular());
$smarty->assign("extra_content",$objindex->extra_content("Introducing"));
//$smarty->assign("how_it_work",$objindex->extra_content("HOW IT WORK"));
$smarty->assign("testimonial",$objindex->extra_content("TESTIMONIAL"));
$smarty->assign("webdemo",$objindex->slider("HOW IT WORKS"));
//echo "<pre>";print_r($objindex->MostPopular());echo $db->getLastQuery()."//".$db->getErMsg();exit;
$smarty->assign("footermenu",$objpage->mymenu("footer"));
$smarty->assign("side_silder",$objindex->slider("HEADER"));
$smarty->assign("meta_title", $LinksDetails["general_meta_title"]);
$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
$smarty->assign("countAd", $countAd);
$smarty->assign("Trending",$objdeal->Trendingads());
$randomcolor=array();
for($i=0;$i<=count($catListA);$i++){
	$randomcolor[] = '#' . strtoupper(dechex(rand(0,10000000)));
}
$smarty->assign("randomColor",$randomcolor);
$smarty->display('index.html');
