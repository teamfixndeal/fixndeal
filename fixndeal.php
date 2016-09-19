<?php
include("config.php");
$ch=$_REQUEST["type"];
if($ch=="city"){
	
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
		$dataAry["status"]="success";
		$dataAry["msg"]="";
		foreach($disname as $dist){$i++;
			$dataAry["data"][$i]["city"]=$dist["city"];
			$dataAry["data"][$i]["state"]=$dist["state"];
			$dataAry["data"][$i]["country"]=$dist["country"];
		}
	}else{
		$dataAry["status"]="success";
		$dataAry["msg"]="No Record Found.";
	}
	echo json_encode($dataAry);
	//echo "var usastates = {'a':'c'}";
}elseif($ch=="popular_ads"){
	$ProductDetail = $db->getRows("select id,dtype,title,image from ".POSTS." where status=1 and ptype=1 ORDER BY udate DESC LIMIT 20 ");
	$pDetailsListData=array();
	if(is_array($ProductDetail) && count($ProductDetail)>0){$i=0;
		$pDetailsListData["status"]="success";
		$pDetailsListData["msg"]="";$ProD=array();
		foreach($ProductDetail as $pd){
			$image=explode(",",$pd['image']);
			$dealType="";
			if($pd["dtype"]=="1")$dealType="Only Cash";
			elseif($pd["dtype"]=="2")$dealType="Exchange";
			elseif($pd["dtype"]=="3")$dealType="Exchange And Cash";
			elseif($pd["dtype"]=="4")$dealType="For Sale";
			elseif($pd["dtype"]=="5")$dealType="Buy Now";
			elseif($pd["dtype"]=="6")$dealType="Other";
			
			$ProD[]=array("name"=>$pd["title"],"image"=>URL_ROOT."media/ads/126/94/".$image[0],"dealtype"=>$dealType,"id"=>$pd["id"]);
		}
		$pDetailsListData["data"]=$ProD;
	}else{
		$pDetailsListData["status"]="error";
		$pDetailsListData["msg"]="Recod not found";
	}
	echo json_encode($pDetailsListData);
}elseif($ch=="trending_ads"){
	$ProductDetail = $db->getRows("select * from ".POSTS." where status=1 and ptype=1 ORDER BY total_view DESC LIMIT 20 ");
	$pDetailsListData=array();
	if(is_array($ProductDetail) && count($ProductDetail)>0){$i=0;
		$pDetailsListData["status"]="success";
		$pDetailsListData["msg"]="";$ProD=array();
		foreach($ProductDetail as $pd){
			$image=explode(",",$pd['image']);
			$dealType="";
			if($pd["dtype"]=="1")$dealType="Only Cash";
			elseif($pd["dtype"]=="2")$dealType="Exchange";
			elseif($pd["dtype"]=="3")$dealType="Exchange And Cash";
			elseif($pd["dtype"]=="4")$dealType="For Sale";
			elseif($pd["dtype"]=="5")$dealType="Buy Now";
			elseif($pd["dtype"]=="6")$dealType="Other";
			
			$ProD[]=array("name"=>$pd["title"],"image"=>URL_ROOT."media/ads/126/94/".$image[0],"dealtype"=>$dealType,"id"=>$pd["id"]);
		}
		$pDetailsListData["data"]=$ProD;
	}else{
		$pDetailsListData["status"]="error";
		$pDetailsListData["msg"]="Recod not found";
	}
	echo json_encode($pDetailsListData);
}elseif($ch=="category"){
	$id="0";
	if(isset($_GET["id"]))$id=$_GET["id"];
	$category=$db->getRows("select c.*,(select count(id) from `".POSTS."` where category=c.id) as total_product from ".CATEGORY." as c where c.status=1 and c.is_parent= ".$id);
	if(is_array($category) && count($category)>0){$i=0;
		$categoryList["status"]="success";
		$categoryList["msg"]="";$allCat=array();
		foreach($category as $cat){$i++;
			$allCat[]=array("name"=>$cat["name"],"id"=>$cat["id"],"image"=>URL_ROOT."uploads/category/h/60/".$cat["image"],"product"=>$cat["total_product"]);
		}
		$categoryList["data"]=$allCat;
	}else{
		$categoryList["status"]="error";
		$categoryList["msg"]="Recod not found";
	}
	echo json_encode($categoryList);
}elseif($ch=="mostView"){
	$ProDetail=array();
	$ProductDetail = $db->getRows("select p.*,u.udate as joinDate,u.username,u.fullname,u.email,u.mobile,u.avatar,u.mob_verify from ".POSTS." as p,".SITE_USER." as u where p.status=1 and p.ptype=1 and u.id=p.added_by ORDER BY p.udate DESC LIMIT 10 ");
	if(is_array($ProductDetail) && count($ProductDetail)>0){$i=0;
		$ProDetail["status"]="success";
		$ProDetail["message"]="";
		foreach($ProductDetail as $pd){$i++;
			$ProDetail['data']['mostView'][$i]=$pd;
			
			$ProDetail['data']["mostView"][$i]["category"]=$db->getVal("select name from ".CATEGORY." where id=".$pd["category"]);
			$ProDetail['data']["mostView"][$i]["subcat"]=$db->getVal("select name from ".CATEGORY." where id=".$pd["subcat"]);
			
			
			$image=explode(",",$pd['image']);
			
			$ProDetail['data']['mostView'][$i]['images']=$image;
			if($pd["dtype"]=="1")$ProDetail['data']['mostView'][$i]["dtype"]="Only Cash";
			elseif($pd["dtype"]=="2")$ProDetail['data']['mostView'][$i]["dtype"]="Exchange";
			elseif($pd["dtype"]=="3")$ProDetail['data']['mostView'][$i]["dtype"]="Exchange And Cash";
			elseif($pd["dtype"]=="4")$ProDetail['data']['mostView'][$i]["dtype"]="For Sale";
			elseif($pd["dtype"]=="5")$ProDetail['data']['mostView'][$i]["dtype"]="Buy Now";
			elseif($pd["dtype"]=="6")$ProDetail['data']['mostView'][$i]["dtype"]="Other";
		}
	}else{
		$ProDetail["status"]="error";
		$ProDetail["message"]="Record now found";
	}
	$ProductDetail = $db->getRows("select p.*,u.udate as joinDate,u.username,u.fullname,u.email,u.mobile,u.avatar,u.mob_verify from ".POSTS." as p,".SITE_USER." as u where p.status=1 and p.ptype=1 and u.id=p.added_by ORDER BY p.total_view DESC LIMIT 10 ");
	if(is_array($ProductDetail) && count($ProductDetail)>0){$i=0;
		foreach($ProductDetail as $pd){$i++;
			$ProDetail['data']['trending'][$i]=$pd;
			$ProDetail['data']["trending"][$i]["category"]=$db->getVal("select name from ".CATEGORY." where id=".$pd["category"]);
			$ProDetail['data']["trending"][$i]["subcat"]=$db->getVal("select name from ".CATEGORY." where id=".$pd["subcat"]);
			$image=explode(",",$pd['image']);
			$ProDetail['data']['trending'][$i]['images']=$image;
			if($pd["dtype"]=="1")$ProDetail['data']['trending'][$i]["dtype"]="Only Cash";
			elseif($pd["dtype"]=="2")$ProDetail['data']['trending'][$i]["dtype"]="Exchange";
			elseif($pd["dtype"]=="3")$ProDetail['data']['trending'][$i]["dtype"]="Exchange And Cash";
			elseif($pd["dtype"]=="4")$ProDetail['data']['trending'][$i]["dtype"]="For Sale";
			elseif($pd["dtype"]=="5")$ProDetail['data']['trending'][$i]["dtype"]="Buy Now";
			elseif($pd["dtype"]=="6")$ProDetail['data']['trending'][$i]["dtype"]="Other";
		}
	}
	echo json_encode($ProDetail);
}elseif($ch=="myads"){
	$conDetail=array();$i=0;
	$pcount = $db->getVal("select count(id) from ".POSTS." where  added_by='".$_REQUEST['userid']."' and ptype='1' and status=1");
	$startV=$_REQUEST['startV'];
	$endV=$_REQUEST['endV'];
	$ProDetail["totPost"]=$pcount;
	$contentDetail = $db->getRows("select * from ".POSTS." where  added_by='".$_REQUEST['userid']."' and ptype='1' and status=1 limit $startV, $endV ");
	$ProDetail["query"]=$db->getLastQuery();
	$ProDetail["ncount"]=count($contentDetail);
	if(is_array($contentDetail) && count($contentDetail)>0){
		$ProDetail["status"]="success";
		$ProDetail["msg"]="";
		foreach($contentDetail as $pd){$i++;
			$ProDetail["Result"][$i]=$pd;
			$ProDetail["Result"][$i]["smalldesShort"]=substr(strip_tags($pd["smalldes"]),0,150);
			$ProDetail["Result"][$i]["i"]=$i;
			$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$pd['title'])));
			$ProDetail["Result"][$i]["url"]=URL_ROOT."product/".$pd["id"]."-".$pagename.".html";
			$image=explode(",",$pd['image']);
			$ProDetail["Result"][$i]['images']=$image;
			$ProDetail["Result"][$i]['offers']=$objdeal->getOffers($pd["id"]);
			if($pd["dtype"]=="1")$ProDetail["Result"][$i]["dtype"]="Only Cash";
			elseif($pd["dtype"]=="2")$ProDetail["Result"][$i]["dtype"]="Exchange";
			elseif($pd["dtype"]=="3")$ProDetail["Result"][$i]["dtype"]="Exchange And Cash";
			elseif($pd["dtype"]=="4")$ProDetail["Result"][$i]["dtype"]="For Sale";
			elseif($pd["dtype"]=="5")$ProDetail["Result"][$i]["dtype"]="Buy Now";
			elseif($pd["dtype"]=="6")$ProDetail["Result"][$i]["dtype"]="Other";
		}
	}
	else{$ProDetail["status"]="error";$ProDetail["msg"]="Invalid Product id...";}
	echo json_encode($ProDetail);
}elseif($ch=="search"){
	$postDetail=array();$url=array();
	$imgType=$_GET["imgType"];unset($_GET["imgType"]);
	$minserprice=$_GET['minserprice'];unset($_GET['minserprice']);
	$maxserprice=$_GET['maxserprice'];unset($_GET['maxserprice']);
	$db->query("update ".citystatecountry." set visit=visit+1 where name='".$city."'");
	$time = time() + (24*3600*365) ;
	setcookie( "city", $_GET["city"], $time );   
	$start=$_GET["startV"];$end=$_GET["endV"];$q=$_GET["q"];$CurrentPage=$_GET["CurrentPage"];$shortBy=$_GET["shortBy"];
	if(isset($_GET["city"]))
	$_GET["city"]=$db->getVal("select id from citystatecountry where name='".$_GET["city"]."'");	
	unset($_GET["type"]);unset($_GET["q"]);unset($_GET["endV"]);unset($_GET["startV"]);unset($_GET["CurrentPage"]);unset($_GET["shortBy"]);
	$data=$_GET;$where="";$whereAry=array();
	if(is_array($data) && count($data)>0){
		foreach($data as $key=>$value){
			if($value!="" && $value!="")
			$whereAry[]="p.".$key."='".$value."'";
			$url[]=$key."=".$value;
		}
	}
	$whereAry[]="p.category=cat.id and p.subcat=subcat.id and p.added_by=u.id";
	if($imgType=="withimage"){
		$whereAry[]="( p.image!='noimage.jpg' ) ";	
	}if($minserprice>0 || $maxserprice>0){
		$whereAry[]="( p.price>='".$minserprice."' and p.price<='".$maxserprice."' ) ";	
	}
	if(is_array($whereAry) && count($whereAry)>0){
		$where=" WHERE (".implode(" AND ",$whereAry).")";
	}
	if($q!=""){
		$whereOr="(UPPER(p.title) like '%".strtoupper($q)."%' or UPPER(p.smalldes) like '%".strtoupper($q)."%')";
		$url[]="search=".$q;
		if($where!="")	$where.= " AND $whereOr";
		else	$where= " WHERE $where Or";
	}
	$totPost=$db->getVal("select count(p.id) from ".POSTS." as p,".CATEGORY." as cat,".CATEGORY." as subcat,".SITE_USER." as u  $where");
	$postDetail["totPost"]=$totPost;
	$postDetail["FtotPost"]=" Total ".$totPost." Ads Found";
	if($imgType=="withimage")
		$postDetail["FtotPost"]=" Total ".$totPost." Ads Found with Images";
	
	$query=$db->getRows("select u.mob_verify,u.mobile, p.*,cat.name as category_name,subcat.name as subcategory_name from ".POSTS." as p,".CATEGORY." as cat,".CATEGORY." as subcat,".SITE_USER." as u $where $shortBy limit $start,$end");
	$postDetail["MtQuery"]=$db->getLastQuery();
	if(is_array($query) && count($query)>0){$i=0;
		foreach($query as $qry){$i++;
			$postDetail["Result"][$i]=$qry;
			$postDetail["Result"][$i]["smalldesShort"]=substr(strip_tags($qry["smalldes"]),0,150);
			$image=explode(",",$qry['image']);
			$postDetail["Result"][$i]['src']=$image[0];
			if($image[0]==""){$postDetail["Result"][$i]['src']="noimage.jpg";}
			$postDetail["Result"][$i]["udate"]=ago($qry["udate"]);
			$postDetail["Result"][$i]["number"]=$qry['mobile'];
			$postDetail["Result"][$i]["pincode"]=$qry['zip'];
			$postDetail["Result"][$i]["mob_verify"]=$qry['mob_verify'];
			$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$qry['title'])));
			$postDetail["Result"][$i]["url"]=URL_ROOT."product/".$qry["id"]."-".$pagename.".html";
		}
	}
	$relatedProduct=$db->getRows("select * from ".POSTS." where category='".$_REQUEST['category']."' and status=1 and ptype=1 ORDER BY udate DESC LIMIT 0,5");
	if(is_array($relatedProduct) && count($relatedProduct)>0){$ij=0;
		foreach($relatedProduct as $qry1){$ij++;
			$postDetail["RelatedResult"][$ij]=$qry1;
			$image=explode(",",$qry1['image']);
			$postDetail["RelatedResult"][$ij]['src']=$image[0];
			if($image[0]==""){$postDetail["Result"][$ij]['src']="noimage.jpg";}
			$postDetail["RelatedResult"][$ij]["udate"]=ago($qry1["udate"]);
			$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$qry1['title'])));
			$postDetail["RelatedResult"][$ij]["url"]=URL_ROOT."product/".$qry1["id"]."-".$pagename.".html";
			if($qry1["dtype"]=="1")$postDetail["RelatedResult"][$ij]["dtype"]="Only Cash";
			elseif($qry1["dtype"]=="2")$postDetail["RelatedResult"][$ij]["dtype"]="Exchange";
			elseif($qry1["dtype"]=="3")$postDetail["RelatedResult"][$ij]["dtype"]="Exchange And Cash";
			elseif($qry1["dtype"]=="4")$postDetail["RelatedResult"][$ij]["dtype"]="For Sale";
			elseif($qry1["dtype"]=="5")$postDetail["RelatedResult"][$ij]["dtype"]="Buy Now";
			elseif($qry1["dtype"]=="6")$postDetail["RelatedResult"][$ij]["dtype"]="Other";
		}
	}
	$postDetail["mycity"]=$_SESSION["city"];
	$postDetail["ncount"]=count($query);
	$postDetail["queryError"]=$db->getErMsg();
	$postDetail["status"]="searchPost";
	if(isset($_SESSION["user"]["uid"])){
		$url=URL_ROOT."search.html?".implode("&",$url);
		$searchHistory=array("keyword"=>$q,"uid"=>$_SESSION["user"]["uid"],"url"=>$url,"type"=>"search");
		$db->insertAry(SEARCH_HISTORY,$searchHistory);
	}
	echo json_encode($postDetail);
}elseif($ch=="homepage"){
	$id="0";
	if(isset($_GET["id"]))$id=$_GET["id"];
	$category=$db->getRows("select c.*,(select count(id) from `".POSTS."` where category=c.id) as total_product from ".CATEGORY." as c where c.status=1 and c.is_parent= ".$id);
	$Product_popular = $db->getRows("select id,dtype,title,image from ".POSTS." where status=1 and ptype=1 ORDER BY udate DESC LIMIT 20 ");
	$Product_trending = $db->getRows("select * from ".POSTS." where status=1 and ptype=1 ORDER BY total_view DESC LIMIT 20 ");	
	$List["status"]="success";
	$List["msg"]="";$allCat=array();
	foreach($category as $cat){$i++;
		$allCat["category"][]=array("name"=>$cat["name"],"id"=>$cat["id"],"image"=>URL_ROOT."uploads/category/h/60/".$cat["image"],"product"=>$cat["total_product"]);
	}
	foreach($Product_popular as $pd){
		$image=explode(",",$pd['image']);
		$dealType="";
		if($pd["dtype"]=="1")$dealType="Only Cash";
		elseif($pd["dtype"]=="2")$dealType="Exchange";
		elseif($pd["dtype"]=="3")$dealType="Exchange And Cash";
		elseif($pd["dtype"]=="4")$dealType="For Sale";
		elseif($pd["dtype"]=="5")$dealType="Buy Now";
		elseif($pd["dtype"]=="6")$dealType="Other";
		
		$allCat["popular"][]=array("name"=>$pd["title"],"image"=>URL_ROOT."media/ads/126/94/".$image[0],"dealtype"=>$dealType,"id"=>$pd["id"]);
	}
	foreach($Product_trending as $pd){
		$image=explode(",",$pd['image']);
		$dealType="";
		if($pd["dtype"]=="1")$dealType="Only Cash";
		elseif($pd["dtype"]=="2")$dealType="Exchange";
		elseif($pd["dtype"]=="3")$dealType="Exchange And Cash";
		elseif($pd["dtype"]=="4")$dealType="For Sale";
		elseif($pd["dtype"]=="5")$dealType="Buy Now";
		elseif($pd["dtype"]=="6")$dealType="Other";
		
		$allCat["trending"][]=array("name"=>$pd["title"],"image"=>URL_ROOT."media/ads/126/94/".$image[0],"dealtype"=>$dealType,"id"=>$pd["id"]);
	}
	$List["data"][]=$allCat;
	echo json_encode($List);
}
	
	
	
?>