<?php

/**

 * dealondeals application

 * Developed by Nilesh Kardate

  * @package mssinfotect-dealondeals

 * source : http://mssinfotech.com

 */



require 'config.php';

$id=""; 

$catname=str_replace("-"," ",str_replace("~","/",str_replace("and","&",$_REQUEST["name"])));

$smarty->assign("country",$objextra->getCountryList());





/**for mobile only***/

$id=$db->getRow("select c.id,c.name,(select count(id) from `".POSTS."` where category=c.id) as total_product from ".CATEGORY." as c where c.status=1 and c.name= '".$catname."'");



$smarty->assign("catName",$id);

$catid=$db->getVal("select id from ".CATEGORY." where name='".POST($catname)."'");

//echo $db->getLastQuery();echo $catid;exit;
$smarty->assign("categoryId",$catid);
$smarty->assign("categoryMyList",$objextra->getCatList($catid));



/***for mobile only****/

//echo "<pre>";print_r($objextra->getCatList($catid));echo $db->getLastQuery();exit;

$smarty->assign("meta_title", "Category :: ".$catname);

$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);

$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);

$smarty->assign("categoryName",POST($catname));

$randomcolor=array();

for($i=0;$i<=100;$i++){

	$randomcolor[] = '#' . strtoupper(dechex(rand(0,10000000)));

}

$smarty->assign("randomColor",$randomcolor);

$smarty->display('subcat.html');

