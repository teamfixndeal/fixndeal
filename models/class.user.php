<?php
class user
{
	function userdetail($userid,$what="")
	{
		global $db;
		global $objdeal;
		if($what=="")$what="*";
		$udetail=array();$i=0;
		$userDetail = $db->getRow("select $what from ".SITE_USER." where id='".$userid."'");
		if($userDetail && count($userDetail)>0){
			$i++;
			$udetail=$userDetail;
			$udetail["dob"]=explode("-",$userDetail['dob']);
			$udetail["myads"]=$db->getVal("select count(id) from ".POSTS." where added_by='".$userid."' and is_parent=0 and status=1 and ptype=1");
			$udetail["rat"]=$objdeal->getAvgRating($id);
			$udetail["myoffers"]=$db->getVal("select count(id) from ".POSTS." where  added_by='".$userid."' and ptype='2'");
			$udetail["favlist"]=count($objdeal->FavoritesList($userid));
			$udetail["searchlist"]=$db->getVal("select count(id) from ".SEARCH_HISTORY." where uid='".$userid."'");
			$udetail["pendinglist"]=$db->getVal("select count(id) from ".POSTS." where added_by='".$userid."' and is_parent=0 and status=0 and ptype=1");
			$udetail["lastlogin"]=$db->getRow("select ldate,login_browser from ".LOGIN_HISTORY." where userid='".$userid."' order by ldate DESC limit 0,1");
		}
		return $udetail;
	}
	function ucountry()
	{
		global $db;
		$CountryDetail = $db->getRows("select * from ".COUNTRY);
		return $CountryDetail;
	}
	function detail($what,$id=""){
		global $db;
		if($id=="")$id=$_SESSION["user"]["uid"];
		$userDetail = $db->getVal("select $what from ".SITE_USER." where id='".$id."' and status=1");
		return $userDetail;
	}
	function allusers()
	{
		global $db;
		//$userlist=array();
		$userDetail = $db->getRows("select * from ".SITE_USER." where status==1");
		return $userDetail;
	}
	function update_profile($array)
	{
		global $db;
		$status="";
		$all_child="";
	if(is_array($_POST["name"]) && count($_POST["name"])>0)
	{
		for($i=0; $i<=count($_POST['name'])-1; $i++)
		{
			if($_POST["name"][$i]!="" && $_POST['age'][$i]!="" && $_POST['gender'][$i]!=""){
				$attr[] = array(	
						'name'		=>		$_POST['name'][$i],
						'age'		=>		$_POST['age'][$i],
						'gender'		=>		$_POST['gender'][$i]		
				);
			}
		}
		$all_child=json_encode($attr);
	}
			$aryData=array('username' => $array['username'],
					   'lastname' => $array['lastname'],
					   'email' => $array['email'],
					   'fathername' => $array['fathername'],
					   'mothername' => $array['mothername'],
					   'age' => $array['age'],
					   'birthplace' => $array['birthplace'],
					   'postaladdress' => $array['postaladdress'],
					   'houseno' => $array['houseno'],
					   'landmark' => $array['landmark'],
					   'postoffice' => $array['postoffice'],
					   'street' => $array['street'],
					   'state' => $array['state'],
					   'city' => $array['city'],
					   'country' => $array['country'],
					   'zipcode' => $array['zipcode'],
					   'mobileno' => $array['mobileno'],
					   'workplace' => $array['workplace'],
					   "childdetail"=>$all_child
					   
					   
					   
		);	
		$updata=$db->updateAry(SITE_USER,$aryData," where id='".$_SESSION['user']['uid']."'");
		
		if(!is_null($updata))
		{
			$status=array("status"=>"success","msg"=>"Profile Update Successfully");
		}else{
			$status=array("status"=>"error","msg"=>"Error: ".$db->getErMsg());	
		}
		return $status;
	}
	function changePass($array)
	{
		global $db;
		$status="";
		$pass=md5($array['cpass']);
		$aryData=array('pass' => $pass);	
		$updata=$db->updateAry(SITE_USER,$aryData," where id='".$_SESSION['user']['uid']."'");
		if(!is_null($updata))
		{
			$status=array("status"=>"success","msg"=>"Password Update Successfully");
		}else{
			$status=array("status"=>"error","msg"=>"Error: ".$db->getErMsg());	
		}
		return $status;
	}
	
	function fetchNotification(){
		global $db; global $theme;
		$userlist=array();
		$userDetail = $db->getRows("select n.notice,n.id,u.avatar,u.username,u.id as uid from ".NOTIFICATION." as n,".SITE_USER." as u where u.id=n.added_by and n.pid='".$_SESSION["user"]["uid"]."' and n.status=2 ");
		$userlist["count"]=count($userDetail);
		if(is_array($userDetail) && count($userDetail)>0){$i=0;
			foreach($userDetail as $uDetail){$i++;
				$userlist["user"][$i]["link"]=$uDetail["link"];
				$userlist["user"][$i]["id"]=$uDetail["id"];
				$userlist["user"][$i]["notice"]=$uDetail["notice"];
				$userlist["user"][$i]["username"]=$uDetail["username"];
				$userlist["user"][$i]["uid"]=$uDetail["uid"];
				//$userlist["user"][$uid]["fdetail"]=$fdetail;
				$userlist["user"][$i]["avatar"]=URL_ROOT."/media/avatar/50/50/".$uDetail["avatar"];
			}
		}
		$userlist["error"]=$db->getErMsg();
		$userlist["lastQuery"]=$db->getLastQuery();
		return $userlist;
		echo"<pre>";print_r($userlist);exit;
	}
	function updateNotification($id){
		global $db;
		$userDetail = $db->updateAry(NOTIFICATION,array("status"=>0)," where id='".$id."' and status=2 and type='user'");
		$msg=$db->getVal("select notice from ".NOTIFICATION." where id=$id");
		return array("msg"=>$msg);
	}
	function setStaus($status,$text){
		global $db;
		$userDetail = $db->updateAry(SITE_USER,array("is_online"=>$status,"online_status"=>$text)," where id='".$_SESSION["user"]["uid"]."'");
		$color="#f00";
		if($status==0)$color="#f00";
		elseif($status==2)$color="#FF0";
		elseif($status==1)$color="0C0";
		return array("msg"=>$userDetail,"color"=>$color);
	}
	function fetchStatus(){
		global $db;
		$userDetail = $db->getRows("select id,color,title from ".STATUS."");
		$list=array();
		if(is_array($userDetail) && count($userDetail)>0){
			foreach($userDetail as $udetail){
				$list[]=$udetail;
			}
		}
		return $list;
	}
	function updateProfile($what,$value){
		global $db;
		$db->updateAry(SITE_USER,array($what=>$value),"where id='".$_SESSION["user"]["uid"]."'");
	}
	function CategoryList(){
		global $db;
		$status = $db->getRows("select * from ".CATEGORY." where status='1'");
		return $status;
	}
	function myInterestsCategory($id){
		global $db;
		$statusR=array();$i=0;
		$status = $db->getRows("select c.* from ".INTERESTS." as i,".CATEGORY." as c where i.status='1' and i.uid='".$id."' and c.id=i.category group by i.category");
		if(is_array($status) && count($status)>0){
			foreach($status as $s){$i++;
				$statusR[$i]=$s;
				$detail=$db->getRows("select * from ".INTERESTS." where category='".$s["id"]."' and uid='".$id."' and status=1");
				$statusR[$i]["intrests"]=$detail;
			}
		}
		return $statusR;
	}
	function saveInterests($POST){
		global $db;
		$saveAry=array("title"=>POST($POST["title"]),
						"category"=>POST($POST["category"]),
						"file"=>POST($POST["file"]),
						"uid"=>$_SESSION["user"]["uid"]
		);
		$db->insertAry(INTERESTS,$saveAry);
		$status["error"]=$db->getErMsg();
		$status["lastQuery"]=$db->getLastQuery();
		$status["status"]="success";
		$status["msg"]="Interests updated";
		return $status;
	}
	function removeIntrests($id){
		global $db;
		$db->delete(INTERESTS,"where id=$id");
	}
	function track_order($oid,$email)
	{
		global $db;
		$getOrder=$db->getRows("select * from ".ORDER." where oid=$oid and  email='$email' ");
		if($getOrder!="")
		return array("status"=>"success","msg"=>$getOrder);
		else
		return array("status"=>"error","msg"=>"Invalid order ID");
	}
	function searchHistory(){
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRows("select * from ".SEARCH_HISTORY." where  uid='".$_SESSION['user']['uid']."'  group by url order by udate DESC limit 0,6");
		$ProDetail["query"]=$db->getLastQuery();
		$ProDetail["ncount"]=count($contentDetail);
		if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
			foreach($contentDetail as $pd){$i++;
				$ProDetail["Result"][$i]=$pd;
				if($pd["type"]=="view"){
					$url=explode("product/",$pd["url"]);
					$id=explode("-",$url[1]);
					$ProDetail["Result"][$i]['pdetail']=$db->getRow("select smalldes,image,udate,dtype,title from ".POSTS." where id=".$id[0]);
					$image=explode(",",$ProDetail["Result"][$i]['pdetail']['image']);
					$ProDetail["Result"][$i]['pdetail']['images']=$image;
					if($ProDetail["Result"][$i]['pdetail']["dtype"]=="1")$ProDetail["Result"][$i]['pdetail']["dtype"]="Only Cash";
					elseif($ProDetail["Result"][$i]['pdetail']["dtype"]=="2")$ProDetail["Result"][$i]['pdetail']["dtype"]="Exchange";
					elseif($ProDetail["Result"][$i]['pdetail']["dtype"]=="3")$ProDetail["Result"][$i]['pdetail']["dtype"]="Exchange And Cash";
					elseif($ProDetail["Result"][$i]['pdetail']["dtype"]=="4")$ProDetail["Result"][$i]['pdetail']["dtype"]="For Sale";
					elseif($ProDetail["Result"][$i]['pdetail']["dtype"]=="5")$ProDetail["Result"][$i]['pdetail']["dtype"]="Buy Now";
					elseif($ProDetail["Result"][$i]['pdetail']["dtype"]=="6")$ProDetail["Result"][$i]['pdetail']["dtype"]="Other";
				}
			}
		}
		$ProDetail["status"]="searchHistory";
		return ($ProDetail);
	}
}