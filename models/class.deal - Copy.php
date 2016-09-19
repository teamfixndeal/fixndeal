<?php
class deal
{
	function addfav($pid)
    {
	
	global $db;
	
	$chk=$db->getVal("select id from ".FAV." where pid='".$pid."' and userid='".$_SESSION['user']['uid']."' ");
	
	if($chk=="")
	{
	
	$aryData=array(
					"pid"=>$pid,
					"userid"=>$_SESSION['user']['uid'],
					"status"=>'1'
	);
	
	
	$ins=$db->insertAry(FAV,$aryData);
	

	if(!is_null($ins))
			{

				$status=array("status"=>"success","msg"=>"added successfully","type"=>"div");

				
			}
			else
			{
				$status=array("status"=>"error","msg"=>$db->getErMsg().$db->getLastQuery());
			}
	}
	else{
		
		$del=$db->delete(FAV," where id='".$chk."' ");	
			if(!is_null($chk))
			{

				$status=array("status"=>"success","msg"=>"Deleted successfully","type"=>"div");
			}
			else
			{
				$status=array("status"=>"error","msg"=>$db->getErMsg().$db->getLastQuery());
			}
		
		
		}
	
	return $status;
	} 
	function OfferDeal($POST){
		global $db; global $LinksDetails;
		if(!isset($POST["category"]) || trim($POST["category"])==""){
			$status=array("status"=>"error","msg"=>"Please Select category");
		}
		elseif(!isset($POST["postTitle"]) || trim($POST["postTitle"])==""){
			$status=array("status"=>"error","msg"=>"Please Enter Name");
		}
		
		else{
			$aryData=array(
						"title"=>$POST['postTitle'],
						"category"=>$POST['category'],
						"underof"=>$POST['product_id'],
						"ptype"=>'2',
						"subcat"=>$POST['subcatnames'],
						"country"=>$db->getVal("select country from ".SITE_USER." where id=".$_SESSION['user']['uid'].""),
						"state"=>$db->getVal("select state from ".SITE_USER." where id=".$_SESSION['user']['uid'].""),
						"streetname"=>$db->getVal("select address from ".SITE_USER." where id=".$_SESSION['user']['uid'].""),
						"added_by"=>$_SESSION['user']['uid'],
						"status"=>2,
						
						);
						
						
			for($i=0;$i<count($FILES['file']['name']);$i++)
			{
				//$imgname=implode(",",$FILES['uploadFile']['name']);
				
				$lfilename = basename($FILES['file']['name'][$i]);
				
				$lext = strtolower(substr($lfilename, strrpos($lfilename, '.')+1));
				
				if(in_array($lext,array('jpeg','jpg','gif','png')))
				{
					$lnewfile=md5(microtime()).".".$lext;
					
					if(move_uploaded_file($FILES['file']['tmp_name'][$i],PATH_UPLOAD."media".DS.$lnewfile))
					{
						$imgfilename[]=$lnewfile;
					
					}				}
	}
	
			$aryData['image']=implode(",",$imgfilename);
			$ins=$db->insertAry(POSTS,$aryData);
			
			if(!is_null($ins)){
				$status=array("status"=>"success","msg"=>"Your Offer is submitted ","type"=>"url","url"=>URL_ROOT."dashboard.html");
			}else{
				$status=array("status"=>"error","msg"=>$db->getErMsg());
			}
		}
		
		
		
		return $status;
		
		
		
		}
	 
	function BuySell($POST){
	global $db; global $LinksDetails;
		if(!isset($POST["price"]) || trim($POST["price"])==""){
			$status=array("status"=>"error","msg"=>"Please Enter Price");
		}
		
		else{
			$aryData=array(
						"title"=>$POST['other'],
						
						"underof"=>$POST['product_id'],
						"ptype"=>'3',
						"added_by"=>$_SESSION['user']['uid'],
						"status"=>2,
						
						);
			$ins=$db->insertAry(POSTS,$aryData);
			
			if(!is_null($ins)){
				$status=array("status"=>"success","msg"=>"Your Offer is submitted ","type"=>"url","url"=>URL_ROOT."dashboard.html");
			}else{
				$status=array("status"=>"error","msg"=>$db->getErMsg());
			}
		}
		
		
		
		return $status;
		
		
		
		}
	
	function getOffers($id)
		{
		global $db;
		$status=$db->getRows("select * from ".POSTS." where  underof='".$id."' and ptype='2'");
		
		if(count($status)>0){ 
		$i=-1;
		foreach ($status as $spList)
		{$i++;
		$status[$i]['fullname']=$db->getVal("select fullname from ".SITE_USER." where id=".$spList['added_by']." ");
		$status[$i]['avatar']=$db->getVal("select avatar from ".SITE_USER." where id=".$spList['added_by']." ");
		
		}
		}
		return $status;
		}
	function FavoritesList($userid)
    {
	global $db;
	$idlist=$db->getRows("select * from ".FAV." where userid=".$userid."");
	
	foreach ($idlist as  $List)
	{
		$status=$this->getSingleDetail($List['pid']);;
		$cdetail[]=$status;
		
	}
	return $cdetail;
	
	}
   
    function MyPostingList($userid){
		global $db;
		
		$List=$db->getRows("select * from ".POSTS." where added_by=".$userid."");
		
		
		
		return $List;
		}

	function make_a_deal($POST,$FILES)
	{
		
		global $db; global $LinksDetails;
		if(!isset($POST["category"]) || trim($POST["category"])==""){
			$status=array("status"=>"error","msg"=>"Please Select category");
		}
		elseif(!isset($POST["dtype"]) || trim($POST["dtype"])==""){
			$status=array("status"=>"error","msg"=>"Please Select Type");
		}
		elseif(!isset($POST["smalldes"]) || trim($POST["smalldes"])==""){
			$status=array("status"=>"error","msg"=>"Please Enter Description");
		}
		
		else{
			$aryData=array(
						"category"=>$POST["category"],
						"title"=>$POST["title"],
						"dtype"=>$POST["dtype"],
						"smalldes"=>$POST["smalldes"],
						"streetname"=>$POST["streetname"],
						"smalldes"=>$POST["smalldes"],
						"added_by"=>$_SESSION['user']['uid'],
						"country"=>$POST['country'],
						"state"=>$POST['state'],
						"city"=>$POST['city'],
						"status"=>2,
						"subcat"=>$POST['subcatnames']
						);
						
						
			for($i=0;$i<count($FILES['file']['name']);$i++)
			{
				
				
				//$imgname=implode(",",$FILES['uploadFile']['name']);
				
				$lfilename = basename($FILES['file']['name'][$i]);
				
				$lext = strtolower(substr($lfilename, strrpos($lfilename, '.')+1));
				
				if(in_array($lext,array('jpeg','jpg','gif','png')))
				{
					$lnewfile=md5(microtime()).".".$lext;
					
					if(move_uploaded_file($FILES['file']['tmp_name'][$i],PATH_UPLOAD."media".DS.$lnewfile))
					{
						$imgfilename[]=$lnewfile;
					
					}				}
	}
	
			$aryData['image']=implode(",",$imgfilename);
			$ins=$db->insertAry(POSTS,$aryData);
			
			if(!is_null($ins)){
				$status=array("status"=>"success","msg"=>"Your Ad is submitted and  will be active in 2 hours","type"=>"url","url"=>URL_ROOT."dashboard.html");
			}else{
				$status=array("status"=>"error","msg"=>$db->getErMsg());
			}
		}
		return $status;
	}
		
	function CreateAlert($POST)
	{
		global $db; global $LinksDetails;
		if(!isset($POST["category"]) || trim($POST["category"])==""){
			$status=array("status"=>"error","msg"=>"Please Select category");
			
		}
		elseif(!isset($POST["subcat"]) || trim($POST["subcat"])==""){
			$status=array("status"=>"error","msg"=>"Please Select sub category");
		}
		elseif(!isset($POST["dtype"]) || trim($POST["dtype"])==""){
			$status=array("status"=>"error","msg"=>"Please Select Type");
		}
		elseif(!isset($POST["mobile"]) || trim($POST["mobile"])==""){
			$status=array("status"=>"error","msg"=>"Please Enter mobile");
		}
				elseif(!isset($POST["email"]) || trim($POST["email"])==""){
			$status=array("status"=>"error","msg"=>"Please Enter email");
		}
		
		
		else{
			$aryData=array(
						"category"=>$POST["category"],
						"subcat"=>$POST["subcat"],
						"dtype"=>$POST["dtype"],
						"price"=>$POST["price"],
						"locality"=>$POST["locality"],
						"email"=>$POST["email"],
						"mobile"=>$POST["mobile"],
						"created_by"=>$_SESSION['user']['uid'],
						
						"status"=>2
						);
			$ins=$db->insertAry(ALERT,$aryData);
			
			if($ins!=""){
           $status=array("status"=>"success","msg"=>"Your Ad is submitted and  will be active in 2 hours","url"=>URL_ROOT."dashboard.html");
			}else{
				$status=array("status"=>"error","msg"=>$db->getErMsg());
			}
		}
		return $status;
	
		
		
		
		}	
		
	function getPosts($id)
	{
	
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRows("select * from ".POSTS." where  added_by='".$id."'");
		
		if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
			foreach($contentDetail as $pd){$i++;
				$ProDetail[$i]=$pd;
				$ProDetail[$i]["i"]=$i;
				$ProDetail[$i]["url"]==href("view_page.php","product_id=".$pd["id"]);
				$image=explode(",",$pd['image']);
				$ProDetail[$i]['images']=$image;
			}
		}
		return $ProDetail;
		
		
		}
		
	function getSingleDetail($id)
	 {
		 global $db;$ProDetail=array();global $LinksDetails;
		if($what=="")$what="*";
		$ProductDetail = $db->getRow("select $what from ".POSTS." where id=$id");
		
		$ProDetail=$ProductDetail;//echo ($ProductDetail["new_price"]);exit;
		$ProDetail["url"]=href("view_page.php","product_id=".$id);
		$catname=$db->getVal("select name from ".CATEGORY." where id=".$ProDetail['category']."");
		$ProDetail["cat_name"]=$catname;
	$dealname=$db->getVal("select name from ".DEALTYPE." where id=".$ProDetail['dtype']."");
		$ProDetail["cat_name"]=$dealname;
		
		//$ProDetail['rat']=$this->getAvgRating($ProductDetail['product_id']);
		//$ProDetail['overall']=$this->review($pid);
		//if(isset($ProductDetail["attr"]) && $ProductDetail["attr"]!="")
		
		//$ProDetail["attr"]=$db->getRows("select c.attrValue as attr_name,a.attribute_val as attr_value from ".PRODUCT_ATTR." as a,".ATTRIBUTE_CRITERIA." as c where c.id=a.attribute_id and a.product_id=$pid");
		 
		 
		 
		 
		 
		 return $ProDetail;
		 }	
    function Trendingads()
	 {
		 global $db;$ProDetail=array();global $LinksDetails;
		if($what=="")$what="*";
		$ProductDetail = $db->getRows("select * from ".POSTS." ");
		if(is_array($ProductDetail) && count($ProductDetail)>0){$i=0;
			foreach($ProductDetail as $pd){$i++;
				$ProDetail[$i]=$pd;
				$ProDetail[$i]["i"]=$i;
				$image=explode(",",$pd['image']);
				$ProDetail[$i]["url"]=href("view_page.php","product_id=".$pd["id"]);
				//$ProDetail[$i]["url"]==href("view_page.php","product_id=".$pd["id"]);
				$ProDetail[$i]['images']=$image;
			}
		}
		 return $ProDetail;
		 }	
		 	 function MyOffersList($userid){
		global $db;
		
		$List=$db->getRows("select * from ".POSTS." where added_by=".$userid." and ptype='2'");
		
		
		
		return $List;
		}	
     
	 }
	
	