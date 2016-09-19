<?php
/**
 * dealondeals application
 * Developed by Nilesh Kardate
  * @package mssinfotect-dealondeals
 * source : http://mssinfotech.com
 */
 require 'config.php';
$id=""; error_reporting(55);
$pid=explode("-",$_GET['id']);
$pDetails=$objdeal->getProducDetail($_GET['id']);
$smarty->assign("pDetail",$pDetails["detail"]);
$smarty->assign("parentproname",$objdeal->parentproname($_GET['id']));
$uids=$db->getVal("select added_by= from ".POSTS." where id='".$_GET['id']."'");
//$smarty->assign("getAvgRating",$objdeal->getAvgRating($uids));
$smarty->assign("meta_title", $pDetails["detail"]["title"]);
$smarty->assign("meta_tag", $pDetails["detail"]["title"].",".$pDetail["detail"]["category_name"].",".$pDetail["detail"]["subcategory_name"]);
$smarty->assign("meta_description", $pDetails["smalldes"]);
$count=$db->query("update ".POSTS." set total_view=total_view+1 where id='".$pid[0]."' and added_by!='".$_SESSION['user']['uid']."'");
$total_view=$db->getVal("select total_view from ".POSTS." where id='".$pid[0]."' ");
$smarty->assign("category",$objindex->getCategory());
$smarty->assign("Offers",$objdeal->getOffers($pid[0]));
if(isset($_SESSION["user"]["uid"])){
	$chk=$db->getVal("select id from ".FAV." where pid='".$pid[0]."' and userid='".$_SESSION['user']['uid']."' ");
	$smarty->assign("checkFav",$chk);
	$url=curPageURL();
	$searchHistory=array("keyword"=>$pid[1],"uid"=>$_SESSION["user"]["uid"],"url"=>$url,"type"=>"view");
	$db->insertAry(SEARCH_HISTORY,$searchHistory);
}
$next=$db->getRow("select id,title from ".POSTS." where id>".$pid[0]." limit 0,1");
$aname=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$next['title'])));
if(is_array($next) && count($next)>0){
	$smarty->assign("nextUrl",URL_ROOT."product/".$next["id"]."-".$aname.".html");
}
if(count($pDetails["detail"])!=""){
	//echo "yes";exit;
	$smarty->display('offeradeal.html');	
}else{
	$smarty->display('error.html');	
}

