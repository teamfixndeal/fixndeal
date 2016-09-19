<?php
/**
 * dealondeals application
 * Developed by Nilesh Kardate
  * @package mssinfotect-dealondeals
 * source : http://mssinfotech.com
 */
require 'config.php';
$id="";
$smarty->assign("footermenu",$objpage->mymenu("footer"));
$smarty->assign("side_silder",$objindex->slider("HEADER"));
$smarty->assign("meta_title", $LinksDetails["general_meta_title"]);
$smarty->assign("meta_tag", $LinksDetails["general_meta_tags"]);
$smarty->assign("meta_description", $LinksDetails["general_meta_desc"]);
$disname=$db->getRows("select 	
	c.name as city,
	c.visit as cvisit,
	country.name as country,
	state.name as state	
	from 
	citystatecountry as c,
	citystatecountry as state,
	citystatecountry as country 	
	where 	
	c.nametype = 'city' AND 	state.nametype = 's' AND	country.nametype = 'c' and	state.id=c.underof and 	country.id=state.underof AND country.id =101 order by c.visit DESC ");
	$dataAry=array();
	if(is_array($disname) && count($disname)){
		$i=0;
		foreach($disname as $dist){$i++;
			$dataAry[]=$dist["city"];
		}
	}
//print_r($dataAry);exit;
$smarty->assign("cityList", $dataAry);	
$smarty->display('city.html');
