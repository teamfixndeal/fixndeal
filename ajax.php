<?php
include("config.php");
$ch=$_REQUEST["type"];
if($ch=="SignIn")
{
	$logdata=$objlogin_signup->login($_REQUEST);
	echo json_encode($logdata);
}
elseif($ch=="upload-profile-picture")
{
	//echo "<pre>";print_r($_REQUEST);exit;
	if(isset($_FILES["file"]["name"]) && !empty($_FILES["file"]["name"]))
	{
		$lfilename = basename($_FILES['file']['name']);
		$lext = strtolower(substr($lfilename, strrpos($lfilename, '.')+1));
		if(in_array($lext,array('jpeg','jpg','gif','png')))
		{
			
			$lnewfile=md5(microtime()).".".$lext;
			if(move_uploaded_file($_FILES['file']['tmp_name'],"uploads/avatar/".$lnewfile))
			{
				$status=0;
				$INS=$db->updateAry(SITE_USER,array("avatar"=>$lnewfile)," where id='".$_SESSION["user"]["uid"]."'");
				$imgurl=URL_ROOT."media/avatar/100/100/".$lnewfile;
				$file=URL_ROOT."uploads/avatar/200/200/".$lnewfile;
				$watermark=URL_ROOT."uploads/media/w/100/watermark.png";
				smart_waterMark( $file, $watermark);
				echo json_encode(array("status"=>"success","type"=>"image","imgurl"=>URL_ROOT."uploads/avatar/".$lnewfile,"msg"=>"Profile Image updated successfully..."));
				
			}else{
				echo "error==>No diectory exist";	
			}
		}else{
			echo "error==>invalid file extension";
		}
	 }else{
	 	echo "error==>Plesae select file to Change Profile Picture";
	 }
}	 
elseif($ch=="SignUp")
{
	$lnewfile="";
	if(isset($_FILES["uploadFile"]["name"]) && !empty($_FILES["uploadFile"]["name"]))
	{
		$lfilename = basename($_FILES['uploadFile']['name']);
		$lext = strtolower(substr($lfilename, strrpos($lfilename, '.')+1));
		if(in_array($lext,array('jpeg','jpg','gif','png')))
		{
			$lnewfile=md5(microtime()).".".$lext;
			$_REQUEST["avatar"]=$lnewfile;
		}else{
			$status=array("status"=>"error","msg"=>"Your Photo Must be in 'jpeg','jpg','gif','png' format..");
		}
	}
	$logdata=$objlogin_signup->signup($_REQUEST);
	if($logdata["status"]=="success"){
		if(isset($_FILES["uploadFile"]["name"]) && !empty($_FILES["uploadFile"]["name"]))
		{
			if(move_uploaded_file($_FILES['uploadFile']['tmp_name'],"uploads/avatar/".$lnewfile))
			{
				$flgUp=$db->updateAry(SITE_USER,array('avatar'=>$lnewfile)," where id='".$logdata['id']."'");
				if(!is_null($flgUp))
				{	
					$imgurl=URL_ROOT."media/avatar/100/100/".$lnewfile;
					$file=URL_ROOT."uploads/avatar/200/200/".$lnewfile;
					$watermark=URL_ROOT."uploads/media/w/100/watermark.png";
					smart_waterMark( $file, $watermark);
					/*$file=URL_ROOT."uploads/avatar/".$lnewfile;
					$watermark=URL_ROOT."uploads/media/w/250/watermark.png";
					smart_waterMark( $file, $watermark);*/
				}
			}
		}
		echo json_encode($logdata);
	}else{
		$status=array("status"=>"error","msg"=>$db->getErMsg());
		echo json_encode($logdata);exit;
	}
	
}
elseif($ch=="appSignUp")
{
	$checkEmail=$db->getVal("select id from ".SITE_USER." where mobile='".$POST["mobile"]."'");
	if(!isset($_POST["fname"]) || trim($_POST["fname"])==""){
		$status=array("status"=>"error","msg"=>"Please enter Full Name");
	}elseif(!isset($_POST["mobile"]) || trim($_POST["mobile"])==""){
		$status=array("status"=>"error","msg"=>"Please enter Mobile Number");
	}elseif(!isset($_POST["email"]) || trim($_POST["email"])==""){
		$status=array("status"=>"error","msg"=>"Please enter Email");
	}elseif(strlen(trim($_POST["mobile"]))!=10){
		$status=array("status"=>"error","msg"=>"Mobile Number must 10 digit only ");
	}elseif($checkEmail!=""){
		$status=array("status"=>"error","msg"=>"Mobile already exist");
	}elseif(preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $_POST["mobile"])) {
		$status=array("status"=>"error","msg"=>"Invalid Mobile Number");
	}
	if($status["status"]=="error"){
		echo json_encode($status);
	}else{
		$_SESSION["form"]=$_POST;
		$otp=rand(100000,999999);
		$_SESSION["form"]["otp"]=$otp;
		$sms="OTP for FixNdeal is ".$otp.". Please do ot share with any one.";
		$Stat=0;
		$status=array("status"=>"success","msg"=>"OTP for FixNdeal is sent to you number please check","type"=>"script");
		echo json_encode($status);
		mysms($_POST["mobile"],$sms,$Stat);
	}
	
}
elseif($ch=="getOTP")
{
	$logdata=$objdeal->getOTP($_REQUEST);
	echo json_encode($logdata);
}
elseif($ch=="forgot")
{	
	$logdata=$objlogin_signup->forgot($_REQUEST);
	echo json_encode($logdata);
} 
elseif($ch=="buymembership")
{	
	$logdata=$objdeal->buymembership($_REQUEST['id'],$_SESSION['user']["uid"]);
	echo json_encode($logdata);
}
elseif($ch=="contactus")
{	
	$logdata=$objlogin_signup->contactus($_REQUEST);
	echo json_encode($logdata);
}
elseif($ch=="acceptbid"){
	$logdata=$objdeal->acceptBid($_REQUEST['adid']);
	echo json_encode($logdata);
	
}
elseif($ch=="buysellreview"){
//$status=array("status"=>"error","msg"=>$_REQUEST['pid']);echo json_encode($status);exit;
	$logdata=$objdeal->buysellreview($_REQUEST['smalldes'],$_REQUEST['star'],$_REQUEST['pid']);
	echo json_encode($logdata);
	
}
elseif($ch=="deleteBid"){
	$logdata=$objdeal->deleteBid($_REQUEST['pid']);
	echo json_encode($logdata);
	
}	
elseif($ch=="updateNotification"){
	$logdata=$db->updateAry(NOTIFICATION,array('status'=>0)," where id='".$_REQUEST['id']."'");
	echo $db->getErMsg();
}
elseif($ch=="readAllNotification"){
	$logdata=$db->updateAry(NOTIFICATION,array('status'=>0)," where pid='".$_SESSION["user"]["uid"]."'");
}	
elseif($ch=="verifyotp"){
	//echo "yes";error_reporting(99);verifyOtpForLostPass
	$logdata=$objlogin_signup->verifyAndroidotp($_REQUEST);
	//echo $db->getLastQuery();
	echo json_encode($logdata);	
		
}elseif($ch=="webverifyotp"){
	$logdata=$objlogin_signup->verifyotp($_REQUEST);
	//echo $db->getLastQuery();exit;
	echo json_encode($logdata);	
	}
elseif($ch=="verifyOtpForLostPass"){
	//echo "yes";error_reporting(99);
	$logdata=$objlogin_signup->verifyOtpForLostPass($_REQUEST);
	echo json_encode($logdata);	
		
}
elseif($ch=="sendSms"){
	$logdata=$objdeal->sendSms($_REQUEST['sms'],$_REQUEST['touid'],$_REQUEST['mfrom']);
	echo $db->getLastQuery();
	echo json_encode($logdata);	
}
elseif($ch=="verifyEmail"){
	$logdata=$objlogin_signup->verifyEmail();
	echo json_encode($logdata);	
}elseif($ch=="sendInvite"){
	$logdata=$objlogin_signup->sendInvite($_REQUEST['email']);
	echo json_encode($logdata);	
}
elseif($ch=="resetPassword")
{
	$logdata=$objlogin_signup->resetPassword($_REQUEST);
	echo json_encode($logdata);
}
elseif($ch=="SubscribeNow")
{
	$logdata=$objlogin_signup->SubscribeNow($_REQUEST);
	echo json_encode($logdata);
}
elseif($ch=="UpdateProfile")
{
	$_REQUEST['dob']=$_REQUEST['Date_Year']."-".$_REQUEST['Date_Month']."-".$_REQUEST['Date_Day'];
	unset($_REQUEST['Date_Year']);unset($_REQUEST['Date_Month']);unset($_REQUEST['Date_Day']);
	$logdata=$objlogin_signup->UpdateProfile($_REQUEST);
	echo json_encode($logdata);			
}
elseif($ch=="custom_request"){
	//echo "<pre>";print_r($_REQUEST);exit;
	//$status=array("status"=>"error","msg"=>$_REQUEST['subcatEx']);echo json_encode($status);exit;
	$logdata=$objdeal->custom_request($_REQUEST);
	echo json_encode($logdata);	
}
elseif($ch=="chkmobile")
{
	$logdata=$objdeal->chkmobile($_REQUEST);
	echo json_encode($logdata);			
}
elseif($ch=="chkemail")
{
	$logdata=$objdeal->chkemail($_REQUEST);
	echo json_encode($logdata);			
}
elseif($ch=="chktitle")
{
	$logdata=$objdeal->chktitle($_REQUEST);
	echo json_encode($logdata);			
}
elseif($ch=="callbacklogin")
{
	$logdata=$objlogin_signup->callbacklogin($_REQUEST);
	echo json_encode($logdata);			
}
elseif($ch=="deletePost"){
	$delPost=$db->delete(POSTS," where id='".$_REQUEST['id']."'");
	if(!is_null($delPost)){
		$status=array("status"=>"success","msg"=>"Deleted successfully","type"=>"div");
	}else{
		$status=array("status"=>"error","msg"=>$db->getErMsg());
	}
	echo json_encode($status);
}
elseif($ch=="highlight"){
	$mem=$db->getVal("select Highlight from ".USERMEMBERSHIP." where uid=".$_SESSION["user"]["uid"]." and status=1");	
	if($mem>0){
		$aryData=array("is_highlight"=>'1');
		$ins=$db->updateAry(POSTS,$aryData, "where id='".$_REQUEST['id']."'" );
		/*$pointAry=array("userid"=>'1',"fromid"=>$_SESSION['user']['uid'],"type"=>"withdraw","amount"=>'5',"status"=>'1');
		$ins=$db->insertAry(TRANS,$pointAry);*/
		if(!is_null($ins)){
			$db->query("update ".USERMEMBERSHIP." set Highlight=Highlight-1 where uid=".$_REQUEST['id']." and status=1");
			$status=array("status"=>"success","msg"=>"Highlighted successfully","type"=>"div");
		}else{
			$status=array("status"=>"error","msg"=>$db->getErMsg());
		}
	}else{
		$status=array("status"=>"error","msg"=>"Please upgrade your membership to Highlight your ads");
	}
	echo json_encode($status);
}
elseif($ch=="featured"){
	$mem=$db->getRow("select featured,featured_text,featured_color from ".USERMEMBERSHIP." where uid=".$_SESSION["user"]["uid"]." and status=1");	
	if($mem["featured"]>0){
		$aryData=array("is_featured"=>'1',"featured_color"=>$mem["featured_color"],"featured_text"=>$mem["featured_text"]);
		$ins=$db->updateAry(POSTS,$aryData, "where id='".$_REQUEST['id']."'" );
		if(!is_null($ins)){
			$db->query("update ".USERMEMBERSHIP." set featured=featured-1 where uid=".$_REQUEST['id']." and status=1");
			$status=array("status"=>"success","msg"=>"Your Ads featured successfully","type"=>"div");
		}else{
			$status=array("status"=>"error","msg"=>$db->getErMsg());
		}
	}else{
		$status=array("status"=>"error","msg"=>"Please upgrade your membership to Make Featured your ads".$db->getLastQuery());
	}
	echo json_encode($status);
}
elseif($ch=="express"){
	$mem=$db->getVal("select Refress from ".USERMEMBERSHIP." where uid=".$_SESSION["user"]["uid"]." and status=1");	
	if($mem>0){
		$aryData=array("is_express_refress"=>'1',"udate"=>date('Y-m-d H:i:s'));
		$ins=$db->updateAry(POSTS,$aryData, "where id='".$_REQUEST['id']."'" );
		if(!is_null($ins)){
			$db->query("update ".USERMEMBERSHIP." set Refress=Refress-1 where uid=".$_REQUEST['id']." and status=1");
			$status=array("status"=>"success","msg"=>"Expressed successfully","type"=>"div");
		}else{
			$status=array("status"=>"error","msg"=>$db->getErMsg());
		}
	}else{
		$status=array("status"=>"error","msg"=>"Please upgrade your membership to Refress your ads");
	}
	echo json_encode($status);
}
elseif($ch=="deleteFav"){
	$delPost=$db->delete(FAV," where id='".$_REQUEST['id']."'");
	if(!is_null($delPost)){
		$status=array("status"=>"success","msg"=>"Remove successfully","type"=>"div");
	}else{
		$status=array("status"=>"error","msg"=>$db->getErMsg());
	}
	echo json_encode($status);
}
elseif($ch=="makedeal"){
	//echo "error==><pre>";echo $_FILES[''];exit;
	if(count($_FILES['uploadFile']['name'])>0){
		for($i=0;$i<count($_FILES['uploadFile']['name']);$i++)
		{
			$lfilename = basename($_FILES['uploadFile']['name'][$i]);
			$lext = strtolower(substr($lfilename, strrpos($lfilename, '.')+1));
			if(in_array($lext,array('jpeg','jpg','gif','png')))
			{
				$lnewfile=md5(microtime()).".".$lext;
				$_REQUEST["imagesList"][$i]=$lnewfile;
				//smart_resize_image("../uploads/car/thumb/".$lnewfile,100,100 );
							
			}
		}	
	}
	if(is_array($_REQUEST["imagesList"]) && count($_REQUEST["imagesList"])>0)
	$_REQUEST['image']=implode(",",$_REQUEST["imagesList"]);
	$logdata=$objdeal->make_a_deal($_REQUEST);
	if($logdata["status"]=="success"){
		if(is_array($_REQUEST["imagesList"]) && count($_REQUEST["imagesList"])>0){
			foreach($_REQUEST["imagesList"] as $key=>$val){
				if(move_uploaded_file($_FILES['uploadFile']['tmp_name'][$key],PATH_UPLOAD."ads".DS.$val))
				{
					$file=URL_ROOT."uploads/ads/w/638/".$val;
					$watermark=URL_ROOT."uploads/media/w/250/watermark.png";
					smart_waterMark( $file, $watermark);
				}
			}
		}
	}
	echo json_encode($logdata);
}
elseif($ch=="updateProfilePic")
{
	if(isset($_FILES["file"]["name"]) && !empty($_FILES["file"]["name"]))
	{
		$lfilename = basename($_FILES['file']['name']);
		$lext = strtolower(substr($lfilename, strrpos($lfilename, '.')+1));
		if(in_array($lext,array('jpeg','jpg','gif','png')))
		{
			$lnewfile=md5(microtime()).".".$lext;
			if(move_uploaded_file($_FILES['file']['tmp_name'],"uploads/avatar/".$lnewfile))
			{
				$flgUp=$db->updateAry(SITE_USER,array('avatar'=>$lnewfile)," where id='".$_SESSION['user']['uid']."'");
				if(!is_null($flgUp))
				{	
					$imgurl=URL_ROOT."media/avatar/100/100/".$lnewfile;
					$file=URL_ROOT."uploads/avatar/200/200/".$lnewfile;
					$watermark=URL_ROOT."uploads/media/w/100/watermark.png";
					smart_waterMark( $file, $watermark);
					/*$file=URL_ROOT."uploads/avatar/".$lnewfile;
					$watermark=URL_ROOT."uploads/media/w/250/watermark.png";
					smart_waterMark( $file, $watermark);*/
					$status=array("status"=>"success","type"=>"image","imgurl"=>$imgurl,"msg"=>"Updated Successfull");
				}
				else
				{
					$status=array("status"=>"error","msg"=>$db->getLastQuery());
				}
			}
		}else{
			$status=array("status"=>"error","msg"=>"Your Photo Must be in 'jpeg','jpg','gif','png' format..");
		}
	}
	
	echo json_encode($status);			
}
elseif($ch=="changepassword")
{
	//print_r ($_REQUEST);exit;
	//echo "error==>working deep";exit;
	$logdata=$objextra->changepassword($_REQUEST);//echo $ch;
	if($logdata["status"]=="ok"){
		$url=URL_ROOT."settings.html"; 
		echo "success==>".$logdata["msg"]."==>".$url;
		
		//echo "success==>image==>".URL_ROOT."profile.html";
				
		
	}else{
		echo "error==>".$logdata["msg"];
	}
}
elseif($ch=="Update")
{
	 //echo "success==>Login successfully==>";exit;
	$logdata=$objlogin_signup->Update($_REQUEST);
	if($logdata["status"]=="success"){
		$_SESSION["msg"]=$logdata["msg"];
		echo "success==>".$logdata["msg"]."==>".$logdata["url"];
	}else{
		echo "error==>".$db->getErMsg().$logdata["msg"];
	}
}
elseif($ch=="editProfileType")
{
	$FinalAry=$objspace->editProfileType($_REQUEST["spaceid"],$_REQUEST['ftype']);
	echo json_encode($FinalAry);
}		
elseif($ch=="makeOfferADeal")
{
	$logdata=$objdeal->makeOfferADeal($_REQUEST);
	echo json_encode($logdata);			
	}
elseif($ch=="addFollowing")
{
//echo "error==> <PRE>hello";print_r($_REQUEST);exit;	
	$logdata=$objspace->addFollowing($_REQUEST['spaceid']);
	 if($logdata["status"]=="error"){
		echo "error==>".$logdata["msg"];
	 }else{
		
		echo "success==>".$logdata["msg"]."==>".$logdata['url'];
	}
}
elseif($ch=="CreateAlert"){
	$logdata=$objdeal->CreateAlert($_REQUEST);
		//echo "error==>hello";exit;
	if($logdata["status"]=="success"){
			
		echo "success==>Success==>".$logdata['url'];;
		
	}elseif($logdata["status"]=="error"){
		echo "error==> ";print_r($logdata["msg"]);exit;
		//print_r($logdata["msg"]);
	}
}		
elseif($ch=="autoloadDist"){
	header('Content-Type: application/javascript');
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
			$dataAry[]='"'.$dist["city"].'":"'.$dist["city"].'"';
		}
	}
	echo "var usastates = {".implode(",",$dataAry)."}";
	//echo "var usastates = {'a':'c'}";
}	
elseif($ch=="autoloadSearch"){
	if($_REQUEST["q"]!=""){
		echo "<ul>";
		//header('Content-Type: application/javascript');
		$conDetail=array();	$contentDetail = $db->getRows("select c.name,c.id as catids from ".POSTS." as p,".CATEGORY." as c where p.ptype='1' and p.status=1 and p.is_parent=0 and (p.title like '%".$_REQUEST["q"]."%' or c.name like '%".$_REQUEST["q"]."%') and p.category=c.id order by title");
		if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
			//echo "<ul>";
			foreach($contentDetail as $dist){$i++;
				$id=$dist["catids"];
				$conDetail[$id]["name"]=$dist["name"];
				$conDetail[$id]["type"]="category";
			}
		}
		$catDetail = $db->getRows("select name,id,is_parent from ".CATEGORY." where status=1 and name like '%".$_REQUEST["q"]."%' order by name");
		if(is_array($catDetail) && count($catDetail)>0){$i=0;
			//echo "<ul>";
			foreach($catDetail as $dist){$i++;
				$id=$dist["id"];
				$conDetail[$id]["name"]=$dist["name"];
				if($dist["is_parent"]==0){
					$conDetail[$id]["type"]="category";
					$conDetail[$id]["category"]=$id;
				}else{
					$is_parent=$db->getVal("select is_parent from ".CATEGORY." where id='".$dist["is_parent"]."'");
					if($is_parent==0){
						$conDetail[$id]["type"]="subcat";
						$conDetail[$id]["category"]=$dist["is_parent"];
					}else{
						$is_parent_parent=$db->getVal("select is_parent from ".CATEGORY." where id='".$is_parent."'");
						if($is_parent_parent==0){
							$conDetail[$id]["type"]="productcat";
							$conDetail[$id]["category"]=$is_parent;
						}
						else{
							$is_parent_parent_parent=$db->getVal("select is_parent from ".CATEGORY." where id='".$is_parent_parent."'");
							if($is_parent_parent_parent==0){
								$conDetail[$id]["type"]="brandcat";
								$conDetail[$id]["category"]=$is_parent_parent;	
							}else{
								$is_parent_parent_parent_parent=$db->getVal("select is_parent from ".CATEGORY." where id='".$is_parent_parent_parent."'");
								if($is_parent_parent_parent_parent==0){
									$conDetail[$id]["type"]="modelcat";
									$conDetail[$id]["category"]=$is_parent_parent_parent;
								}
							}
						}
					
					}
				}
				//$dataAry[]='"'.$dist["title"].'"';
				//echo "<li><a href=''><strong style='color:#000'>".$_REQUEST["q"]."</strong> <span  style='color:#999'>in category</span> </a></li>";
			}
			//echo "</ul>";
		}
		if(is_array($conDetail) && count($conDetail)>0){
			foreach($conDetail as $kay=>$val){
				$url="#";
				if($val["type"]=="category"){
					$url=URL_ROOT."search.html?city=".$city."&search=".$_REQUEST["q"]."&category=".$kay;
				}elseif($val["type"]=="subcat"){
					$url=URL_ROOT."search.html?city=".$city."&search=".$_REQUEST["q"]."&category=".$val["category"]."&subcat=".$kay;
				}elseif($val["type"]=="productcat"){
					$url=URL_ROOT."search.html?city=".$city."&search=".$_REQUEST["q"]."&category=".$val["category"]."&productcat=".$kay;
				}elseif($val["type"]=="brandcat"){
					$url=URL_ROOT."search.html?city=".$city."&search=".$_REQUEST["q"]."&category=".$val["category"]."&brandcat=".$kay;
				}elseif($val["type"]=="modelcat"){
					$url=URL_ROOT."search.html?city=".$city."&search=".$_REQUEST["q"]."&category=".$val["category"]."&modelcat=".$kay;
				}
				echo "<li><a href='".$url."'><i class='icon-search  icon-append'></i><strong style='color:#000'>".$_REQUEST["q"]."</strong> <span  style='color:#999'>in category</span> <span style='color:#ccc'>".$val["name"]."</span></a></li>";
			}	
		}
		echo "<li><a href='".URL_ROOT."search.html?city=".$city."&search=".$_REQUEST["q"]."'><i class='icon-search  icon-append'></i><strong style='color:#000'>".$_REQUEST["q"]."</strong> <span  style='color:#999'>in category</span> <span style='color:#ccc'>All Categories</span></a></li>";
		//echo $db->getErMsg();
		//echo "var POST_title = [".implode(",",$dataAry)."]";
		//echo "var usastates = {'a':'c'}";
		echo "<ul>";
	}
}	
elseif($ch=="showsubCatEx"){ 
	$showsubCat=$db->getRows("select id,name from ".CATEGORY." where is_parent='".$_REQUEST['stid']."' ");
	if($showsubCat>0)
	{
	?>
	<select class="form-control unicase-form-control text-input select input-form" name="subcatnamesEx" id="subcatnamesEx">
        <?php foreach($showsubCat as $cityname){?>     
                <option value="<?php echo $cityname['id'];?>" <?php if($_REQUEST['cid']==$cityname['id']){ echo "selected";} ?>><?php echo $cityname['name'];?></option>
              
          <?php } ?>    
              </select>
	<?php
	}else{echo $db->getErMsg();}
	
}
elseif($ch=="showsubCat"){ 
	$showsubCat=$db->getRows("select id,name from ".CATEGORY." where is_parent='".$_REQUEST['stid']."' ");
	if($showsubCat>0)
	{
	?>
	<select class="form-control unicase-form-control text-input select input-form" name="subcatnames" id="subcatnames">
        
        <?php foreach($showsubCat as $cityname){?>     
                <option value="<?php echo $cityname['id'];?>" <?php if($_REQUEST['cid']==$cityname['id']){ echo "selected";} ?>><?php echo $cityname['name'];?></option>
              
          <?php } ?>    
              </select>
	<?php
	}else{echo $db->getErMsg();}
	
}
elseif($ch=="mycustomecat"){ 
	$showsubCat=$db->getRows("select id,name from ".CATEGORY." where is_parent='".$_REQUEST['cid']."' ");
	if(is_array($showsubCat) && count($showsubCat)>0)
	{
	?>
	<select class="form-control unicase-form-control text-input select input-form" onChange="showCatProcess(this.value,'<?php echo $_REQUEST["next"] ?>')" name="<?php echo $_REQUEST['div'] ?>">
        <option value="">Select <?php echo ucwords($_REQUEST['first']); ?></option>
		<?php foreach($showsubCat as $cityname){?>     
                <option value="<?php echo $cityname['id'];?>" <?php if($_REQUEST['selected']==$cityname['id']){ echo "selected";} ?>><?php echo $cityname['name'];?></option>
              
          <?php } ?>    
              </select>
	<?php
	}else{echo $db->getErMsg();}
	
}
elseif($ch=="mycustomecatAry"){ 
	$showsubCat=$db->getRows("select id,name from ".CATEGORY." where is_parent='".$_REQUEST['cid']."' ");
	if(is_array($showsubCat) && count($showsubCat)>0)
	{
	?>
	<select class="form-control unicase-form-control text-input select input-form" onChange="showCatProcessAry(this.value,'<?php echo $_REQUEST["next"] ?>','<?php echo $_REQUEST["totalDIvLenght"] ?>')" name="<?php echo $_REQUEST['div'] ?>[]">
        <option value="">Select <?php echo ucwords($_REQUEST['first']); ?></option>
		<?php foreach($showsubCat as $cityname){?>     
                <option value="<?php echo $cityname['id'];?>" <?php if($_REQUEST['selected']==$cityname['id']){ echo "selected";} ?>><?php echo $cityname['name'];?></option>
              
          <?php } ?>    
              </select>
	<?php
	}else{echo $db->getErMsg();}
	
}
elseif($ch=="getState"){
	
	$showState=$db->getRows("select * from citystatecountry where underof='".$_REQUEST['id']."' ");
	if(count($showState)>0)
	{
	?>
	<select class="form-control unicase-form-control text-input select input-form" onChange="getCity(this.value)"  name="statenames" id="statenames">
        
        <?php foreach($showState as $statename){?>     
                <option value="<?php echo $statename['id'];?>" <?php if($_REQUEST['sid']==$statename['id']){ echo "selected";} ?>><?php echo $statename['name'];?></option>
              
          <?php } ?>    
              </select>
	<?php
	}else{echo $db->getErMsg();}
		
}
elseif($ch=="getCity"){
	
	
	$showCity=$db->getRows("select id,name from citystatecountry where underof='".$_REQUEST['id']."' ");
	if($showCity>0)
	{
		
	?>
	<select class="form-control unicase-form-control text-input select input-form" name="citynames" id="citynames">
        
        <?php foreach($showCity as $citynames){?>     
                <option value="<?php echo $citynames['id'];?>" <?php if($_REQUEST['cid']==$citynames['id']){ echo "selected";} ?>><?php echo $citynames['name'];?></option>
              
          <?php } ?>    
              </select>
	<?php
	}else{echo $db->getErMsg();}
}
elseif($ch=="searchUserPost"){
	$conDetail=array();
	$pcount = $db->getVal("select count(id) from ".POSTS." where  added_by='".$_SESSION['user']['uid']."' and ptype='1' and status=1");
	$startV=$_REQUEST['startV'];
	$endV=$_REQUEST['endV'];
	$ProDetail["totPost"]=$pcount;
	$contentDetail = $db->getRows("select * from ".POSTS." where  added_by='".$_SESSION['user']['uid']."' and ptype='1' and is_parent=0 and status=1 limit $startV, $endV ");
	//$ProDetail["query"]=$db->getLastQuery();
	$ProDetail["ncount"]=count($contentDetail);
	if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
		foreach($contentDetail as $pd){$i++;
			$ProDetail["Result"][$i]=$pd;
			$ProDetail["Result"][$i]["smalldesShort"]=substr(strip_tags($pd["smalldes"]),0,150);
			$ProDetail["Result"][$i]["i"]=$i;
			$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$pd['title'])));
			$ProDetail["Result"][$i]["url"]=URL_ROOT."product/".$pd["id"]."-".$pagename.".html";
			$image=explode(",",$pd['image']);
			$ProDetail["Result"][$i]['udate']=$pd['udate'];
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
	$ProDetail["status"]="searchUserPost";
	echo json_encode($ProDetail);	
}
elseif($ch=="pendingapproval"){
	$conDetail=array();
	$pcount = $db->getVal("select count(id) from ".POSTS." where  added_by='".$_SESSION['user']['uid']."' and ptype='1' and is_parent=0 and status=0");
	$startV=$_REQUEST['startV'];
	$endV=$_REQUEST['endV'];
	$ProDetail["totPost"]=$pcount;
	$contentDetail = $db->getRows("select * from ".POSTS." where  added_by='".$_SESSION['user']['uid']."' and ptype='1' and is_parent=0 and status=0 limit $startV, $endV ");
	$ProDetail["query"]=$db->getLastQuery();
	$ProDetail["ncount"]=count($contentDetail);
	if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
		foreach($contentDetail as $pd){$i++;
			$ProDetail["Result"][$i]=$pd;
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
	$ProDetail["status"]="pendingapproval";
	echo json_encode($ProDetail);	
}
elseif($ch=="searchHistory"){
	$conDetail=array();
	$pcount = $db->getVal("select count(id) from ".SEARCH_HISTORY." where  uid='".$_SESSION['user']['uid']."'");
	$startV=$_REQUEST['startV'];
	$endV=$_REQUEST['endV'];
	$ProDetail["totPost"]=$pcount;
	$contentDetail = $db->getRows("select * from ".SEARCH_HISTORY." where  uid='".$_SESSION['user']['uid']."' order by udate DESC limit $startV, $endV ");
	$ProDetail["query"]=$db->getLastQuery();
	$ProDetail["ncount"]=count($contentDetail);
	if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
		foreach($contentDetail as $pd){$i++;
			$ProDetail["Result"][$i]=$pd;
			$ProDetail["Result"][$i]["i"]=$i;
			$ProDetail["Result"][$i]['images']=$image;
			$ProDetail["Result"][$i]['offers']=$objdeal->getOffers($pd["id"]);
		}
	}
	$ProDetail["status"]="searchHistory";
	echo json_encode($ProDetail);	
}
elseif($ch=="searchTHistory"){
	$conDetail=array();
	//$pcount = $db->getVal("select count(id) from ".SEARCH_HISTORY." where  uid='".$_SESSION['user']['uid']."'");
	$startV=$_REQUEST['startV'];
	$endV=$_REQUEST['endV'];
	//$ProDetail["totPost"]=$pcount;
	$contentDetail = $db->getRows("select * from ".TRANS." where amount>0 and userid='".$_SESSION['user']['uid']."' OR fromid='".$_SESSION['user']['uid']."' limit $startV, $endV ");
	$ProDetail["query"]=$db->getLastQuery();
	$ProDetail["ncount"]=count($contentDetail);
	if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
		foreach($contentDetail as $pd){$i++;
			//echo "<pre>";print_r($pd);
			$ProDetail["Result"][$i]=$pd;
			$ProDetail["Result"][$i]["i"]=$i;
			
			if(strtolower($pd['userid'])=="admin" || $pd['userid']=="")
			$ProDetail["Result"][$i]["fname"]="FixNDeal";
			else
			$ProDetail["Result"][$i]["fname"]=$db->getVal("select fullname from ".SITE_USER." where id='".$pd['userid']."' ");
			
			if(strtolower($pd['fromid'])=="admin" || $pd['fromid']=="")
			$ProDetail["Result"][$i]["tname"]="FixNDeal";
			else
			$ProDetail["Result"][$i]["tname"]=$db->getVal("select fullname from ".SITE_USER." where id='".$pd['fromid']."' ");
			
			
			$ProDetail["Result"][$i]['images']=$image;
			//$ProDetail["Result"][$i]['offers']=$objdeal->getOffers($pd["id"]);
		}
	}
	$ProDetail["error"]=$db->getErMsg();
	$ProDetail["status"]="searchTHistory";
	echo json_encode($ProDetail);	
}
elseif($ch=="searchMyOfferForDeal"){
	$conDetail=array();
	$pcount = $db->getVal("select count(id) from ".POSTS." where  added_by='".$_SESSION['user']['uid']."' and ptype='1' and status=1");
	$startV=$_REQUEST['startV'];
	$endV=$_REQUEST['endV'];
	$ProDetail["totPost"]=$pcount;
	$contentDetail = $db->getRows("select * from ".POSTS." where  added_by='".$_SESSION['user']['uid']."' and ptype='1' and status=1 limit $startV, $endV ");
	//echo $db->getLastQuery();exit;
	$ProDetail["query"]=$db->getLastQuery();
	$ProDetail["ncount"]=count($contentDetail);
	if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
		foreach($contentDetail as $pd){$i++;
			$ProDetail["Result"][$i]=$pd;
			$ProDetail["Result"][$i]["i"]=$i;
			$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$pd['title'])));
			$ProDetail["Result"][$i]["url"]=URL_ROOT."product/".$pd["id"]."-".$pagename.".html";
			$image=explode(",",$pd['image']);
			$ProDetail["Result"][$i]['images']=$image[0];
			$ProDetail["Result"][$i]['dealIndex']=$pd["dtype"];
			$ProDetail["Result"][$i]['udate']=ago($pd["udate"]);
			if($pd["dtype"]=="1")$ProDetail["Result"][$i]["dtype"]="Only Cash";
			elseif($pd["dtype"]=="2")$ProDetail["Result"][$i]["dtype"]="Exchange";
			elseif($pd["dtype"]=="3")$ProDetail["Result"][$i]["dtype"]="Exchange And Cash";
			elseif($pd["dtype"]=="4")$ProDetail["Result"][$i]["dtype"]="For Sale";
			elseif($pd["dtype"]=="5")$ProDetail["Result"][$i]["dtype"]="Buy Now";
			elseif($pd["dtype"]=="6")$ProDetail["Result"][$i]["dtype"]="Other";
		}
	}
	$ProDetail["status"]="searchMyOfferForDeal";
	echo json_encode($ProDetail);	
}
elseif($ch=="searchMyOffer"){
	$conDetail=array();
	$pcount = $db->getVal("select count(id) from ".POSTS." where  added_by='".$_SESSION['user']['uid']."' and ptype='2'");
	$startV=$_REQUEST['startV'];
	$endV=$_REQUEST['endV'];
	$ProDetail["totPost"]=$pcount;
	$contentDetail = $db->getRows("select * from ".POSTS." where  added_by='".$_SESSION['user']['uid']."' and ptype='2' limit $startV, $endV ");
	$ProDetail["query"]=$db->getLastQuery();
	$ProDetail["ncount"]=count($contentDetail);
	if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
		foreach($contentDetail as $pd){$i++;
			$ProDetail["Result"][$i]=$pd;
			$ProDetail["Result"][$i]["dealIndex"]=$pd["dtype"];
			$ProDetail["Result"][$i]["i"]=$i;
			$pimg=explode(",",$pd['image']);
			$ProDetail["Result"][$i]["pimg"]=$pimg[0];
			$ProDetail["Result"][$i]["dealDetail"]=$objdeal->getProducDetail($pd["underof"]);
			if($pd["dtype"]=="1")$ProDetail["Result"][$i]["dtype"]="Only Cash";
			elseif($pd["dtype"]=="2")$ProDetail["Result"][$i]["dtype"]="Exchange";
			elseif($pd["dtype"]=="3")$ProDetail["Result"][$i]["dtype"]="Exchange And Cash";
			elseif($pd["dtype"]=="4")$ProDetail["Result"][$i]["dtype"]="For Sale";
			elseif($pd["dtype"]=="5")$ProDetail["Result"][$i]["dtype"]="Buy Now";
			elseif($pd["dtype"]=="6")$ProDetail["Result"][$i]["dtype"]="Other";
		}
	}
	
	$ProDetail["status"]="searchMyOffer";
	echo json_encode($ProDetail);	
}
elseif($ch=="fetchFavPost"){
	$conDetail=array();
	$pcount = $db->getVal("select count(id) from ".FAV." where  userid='".$_SESSION['user']['uid']."'");
	$startV=$_REQUEST['startV'];
	$endV=$_REQUEST['endV'];
	$ProDetail["totPost"]=$pcount;
	$contentDetail = $db->getRows("select * from ".FAV." where  userid='".$_SESSION['user']['uid']."' limit $startV, $endV ");
	$ProDetail["query"]=$db->getLastQuery();
	$ProDetail["ncount"]=count($contentDetail);
	if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
		foreach($contentDetail as $pd){$i++;
			$ProDetail["Result"][$i]=$pd;
			$ProDetail["Result"][$i]["i"]=$i;
			$title=$db->getRow("select * from ".POSTS." where id='".$pd['pid']."'");
			$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$title['title'])));
			$ProDetail["Result"][$i]["url"]=URL_ROOT."product/".$pd["pid"]."-".$pagename.".html";
			$image=explode(",",$title['image']);
			$ProDetail["Result"][$i]['images']=$image;
			$ProDetail["Result"][$i]['title']=$title['title'];
			$ProDetail["Result"][$i]['udate']=$title['udate'];
			$ProDetail["Result"][$i]['total_view']=$title['total_view'];
			$ProDetail["Result"][$i]['streetname']=$title['streetname'];
			if($title["dtype"]=="1")$ProDetail["Result"][$i]["dtype"]="Only Cash";
			elseif($title["dtype"]=="2")$ProDetail["Result"][$i]["dtype"]="Exchange";
			elseif($title["dtype"]=="3")$ProDetail["Result"][$i]["dtype"]="Exchange And Cash";
			elseif($title["dtype"]=="4")$ProDetail["Result"][$i]["dtype"]="For Sale";
			elseif($title["dtype"]=="5")$ProDetail["Result"][$i]["dtype"]="Buy Now";
			elseif($title["dtype"]=="6")$ProDetail["Result"][$i]["dtype"]="Iteration";
		}
	}
	$ProDetail["status"]="fetchFavPost";
	echo json_encode($ProDetail);	
}
elseif($ch=="fetchOfferList"){
	$conDetail=array();
	$pcount = $db->getVal("select count(id) from ".POSTS." where  underof='".$_REQUEST['id']."'");
	$startV=$_REQUEST['startV'];
	$endV=$_REQUEST['endV'];
	$ProDetail["totPost"]=$pcount;
	$contentDetail=$db->getRows("select * from ".POSTS." where underof='".$_REQUEST['id']."' and status!=3 limit $startV, $endV ");
	
	$ProDetail["query"]=$db->getLastQuery();
	$ProDetail["ncount"]=count($contentDetail);
	if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
		foreach($contentDetail as $pd){$i++;
			$offer_added_by=$pd["added_by"];
			$post_posted_by=$db->getVal("select added_by from ".POSTS." where id='".$_REQUEST['id']."'");
			$ProDetail["Result"][$i]=$pd;
			$ProDetail["Result"][$i]["i"]=$i;
			$ProDetail["Result"][$i]['udate']=ago($pd['udate']);
			$ProDetail["Result"][$i]['curr']=$LinksDetails['currency_symbol'];
			$Udetail=$db->getRow("select fullname,avatar,mobile,username from ".SITE_USER." where id='".$pd['added_by']."' ");
			$ProDetail["Result"][$i]['username']=$Udetail['username'];
			$ProDetail["Result"][$i]['avatar']=$Udetail['avatar'];
			$ProDetail["Result"][$i]['fullname']=$Udetail['fullname'];
			$ProDetail["Result"][$i]['dtype']=$pd['dtype'];
			$ProDetail["Result"][$i]['status']=$db->getVal("select status from ".POSTS." where id='".$_REQUEST['id']."'");
			//$ProDetail["Result"][$i]['image']=$pd['image'];	
			$ProDetail["Result"][$i]['mobile']=$Udetail['mobile'];
			if($offer_added_by==$_SESSION['user']['uid']){
				$ProDetail["Result"][$i]['admin']=0; //not admin
			}elseif($post_posted_by==$_SESSION['user']['uid']){
				$ProDetail["Result"][$i]['admin']=1; 
			}
		
				$nimage=explode(",",$pd["image"]);
	if(count($nimage)>1)
	{
		$ProDetail["Result"][$i]["image"]=$nimage['0'];
		}
		}
	}
	
	$ProDetail["status"]="success";
	$ProDetail["query"]=$db->getLastQuery();
	echo json_encode($ProDetail);	
}
elseif($ch=="searchPost"){
	$postDetail=array();$url=array();$whereOr="";
	$_SESSION["city"]=$_POST["city"];
	$p_age=$_POST["p_age"];unset($_POST["p_age"]);
	$postago=$_POST["postago"];unset($_POST["postago"]);
	$imgType=$_POST["imgType"];unset($_POST["imgType"]);
	$minserprice=$_POST['minserprice'];unset($_POST['minserprice']);
	$maxserprice=$_POST['maxserprice'];unset($_POST['maxserprice']);
	$db->query("update ".citystatecountry." set visit=visit+1 where name='".$city."'");
	$time = time() + (24*3600*365) ;
	setcookie( "city", $_POST["city"], $time );   
	$start=$_POST["startV"];$end=$_POST["endV"];$q=$_POST["q"];$CurrentPage=$_POST["CurrentPage"];$shortBy=$_POST["shortBy"];
	if(isset($_POST["city"]))
	$_POST["city"]=$db->getVal("select id from citystatecountry where name='".$_POST["city"]."'");	
	unset($_POST["type"]);unset($_POST["q"]);unset($_POST["endV"]);unset($_POST["startV"]);unset($_POST["CurrentPage"]);unset($_POST["shortBy"]);
	unset($_POST["city"]);
	$data=$_POST;$where="";$whereAry=array();
	if(is_array($data) && count($data)>0){
		foreach($data as $key=>$value){
			if($value!="" && $value!="")
			$whereAry[]="p.".$key."='".$value."'";
			$url[]=$key."=".$value;
		}
	}
	//print_r($_POST);
	$whereAry[]=" p.added_by=u.id";
	if($p_age!=""){
		$YearOn = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - ".($p_age*365)." day"));
		$whereAry[]="( p_age between '".$YearOn."' and '".date("Y-m-d")."' ) ";	
		$url[]="p_ago=".$p_age;
	}
	if($postago!=""){
		$whereAry[]=" (p.udate > DATE_SUB(NOW(), INTERVAL ".$postago." HOUR)  AND p.udate <= NOW()) ";
		$url[]="postago=".$postago;
	}
	if($imgType=="withimage"){
		$whereAry[]="( p.image!='noimage.jpg' ) ";	
	}if($minserprice>0 || $maxserprice>0){
		$whereAry[]="( p.price>='".$minserprice."' and p.price<='".$maxserprice."' ) ";	
	}
	if(is_array($whereAry) && count($whereAry)>0){
		$where=" WHERE (".implode(" AND ",$whereAry).")";
	}
	
	$TABLE="";
	if($q!=""){		
		$whereOr="(UPPER(p.title) like '%".strtoupper($q)."%' or UPPER(p.smalldes) like '%".strtoupper($q)."%' OR (p.category in (select id from ".CATEGORY." where UPPER(name) like '%".strtoupper($q)."%' and status=1 and is_parent=0)) or (p.subcat in (select id from ".CATEGORY." where UPPER(name) like '%".strtoupper($q)."%' and status=1 and is_parent in (select id from ".CATEGORY." where status=1 and is_parent=0))))";
		$url[]="search=".$q;
		if($where!="")	$where.= " AND $whereOr";
		else	$where= " WHERE $whereOr AND";
		
		//$TABLE=','.CATEGORY.' as cat ';
	}
	$totPost=$db->getVal("select count(p.id) from ".POSTS." as p $TABLE ,".SITE_USER." as u $where");
	$postDetail["totPostQuery"]=$db->getErMsg().$db->getLastQuery();;
	$postDetail["totPost"]=$totPost;
	$postDetail["FtotPost"]=" Total ".$totPost." Ads Found";
	if($imgType=="withimage")
		$postDetail["FtotPost"]=" Total ".$totPost." Ads Found with Images";
	
//,cat.name as category_name,subcat.name as subcategory_name
	$query=$db->getRows("select u.mob_verify,u.mobile, p.* from ".POSTS." as p $TABLE , ".SITE_USER." as u $where $shortBy limit $start,$end");
	$postDetail["MtQuery"]=$db->getLastQuery();
	if(is_array($query) && count($query)>0){$i=0;
		foreach($query as $qry){$i++;
			$postDetail["Result"][$i]=$qry;
		//cat.name as category_name,subcat.name as subcategory_name	
			$postDetail["Result"][$i]["category_name"]=$db->getVal("select name from ".CATEGORY." where id=".$qry["category"]);
			
			$postDetail["Result"][$i]["subcategory_name"]=$db->getVal("select name from ".CATEGORY." where id=".$qry["subcat"]);
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
	if($whereOr!="")$whereOr=" or $whereOr";
	$relatedProduct=$db->getRows("select * from ".POSTS." where (category='".$_REQUEST['category']."' $whereOr)  and status=1 and ptype=1 ORDER BY udate DESC LIMIT 0,5");
	//echo $db->getLastQuery();exit;
	$postDetail["rcount"]=count($relatedProduct);
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
	$postDetail["ncount"]=count($qry1);
	//$postDetail["queryError"]=$db->getErMsg();
	$postDetail["status"]="searchPost";
	if(isset($_SESSION["user"]["uid"])){
		$url=URL_ROOT."search.html?".implode("&",$url);
		$searchHistory=array("keyword"=>$q,"uid"=>$_SESSION["user"]["uid"],"url"=>$url,"type"=>"search");
		$db->delete(SEARCH_HISTORY,"where uid=".$_SESSION["user"]["uid"]." and url='".$url."'");
		$db->insertAry(SEARCH_HISTORY,$searchHistory);
	}
	echo json_encode($postDetail);
}
elseif($ch=="addfav"){	
	$logdata=$objdeal->addfav($_REQUEST['spaceid']);
	echo json_encode($logdata);
}
elseif($ch=="cancelOffer"){
	$logdata=$objdeal->cancelOffer($_REQUEST);
	echo json_encode($logdata);
}elseif($ch=="removeDeal"){
	$logdata=$objdeal->removeDeal($_REQUEST);
	echo json_encode($logdata);
}elseif($ch=="closeDeal"){
	$logdata=$objdeal->closeDeal($_REQUEST);
	echo json_encode($logdata);
}
elseif($ch=="OfferDeal"){
	$logdata=$objdeal->OfferDeal($_REQUEST);
	echo json_encode($logdata);
}
elseif($ch=="Buysell"){
	
	if(isset($_FILES["uploadFile"]["name"]) && !empty($_FILES["uploadFile"]["name"]))
	{
		$lfilename = basename($_FILES['uploadFile']['name']);
		$lext = strtolower(substr($lfilename, strrpos($lfilename, '.')+1));
		if(in_array($lext,array('jpeg','jpg','gif','png')))
		{
			$lnewfile=md5(microtime()).".".$lext;
			if(move_uploaded_file($_FILES['uploadFile']['tmp_name'],"uploads/ads".DS.$lnewfile))
			{
				/*$file=URL_ROOT."uploads/ads/126/94/".$lnewfile;
				$watermark=URL_ROOT."uploads/media/w/100/watermark.png";
				smart_waterMark( $file, $watermark);*/
				$file=URL_ROOT."uploads/ads/w/638/".$lnewfile;
				$watermark=URL_ROOT."uploads/media/w/250/watermark.png";
				smart_waterMark( $file, $watermark);
				$_REQUEST["image"]=$lnewfile;
			}				
		}
	}
	$logdata=$objdeal->buySell($_REQUEST);
	//echo "<pre>";print_r($logdata);exit;
	echo json_encode($logdata);exit;
		
}	
elseif($ch=="category"){
	$logdata=$objindex->getCategory();
	echo json_encode($logdata);
}
elseif($ch=="BuyCoin"){
	if(isset($_REQUEST["status"]) && $_REQUEST["status"]=="ok"){
		//$status=$objpay->paybypaypal($_SESSION["payment"]["total"],$_SESSION["payment"]["day"],$_SESSION["user"]["uid"]);
		//deposit($_SESSION["payment"]["coin"],$_SESSION["user"]["uid"],"Purchase ".$_SESSION["payment"]["coin"]." coin  of INR ". $_SESSION["payment"]["amount"]);
		//$m=$db->getVal("select mobile from ".SITE_USER." where id='".$_SESSION["user"]["uid"]."'");
		//mysms($m,"You have Purchase ".$_SESSION["payment"]["coin"]." coin  of INR ". $_SESSION["payment"]["amount"]." at fixndeal.com");
		unset($_SESSION["payment"]);
		redirect(URL_ROOT."accountstatement.html");
	}elseif(isset($_REQUEST["status"]) && $_REQUEST["status"]=="error"){
		unset($_SESSION["payment"]);
		redirect(URL_ROOT."accountstatement.html");
	}else{
		$_SESSION["payment"]["detail"]="Purchase ".$_REQUEST["coin"]." coins";
		$_SESSION["payment"]["coin"]=$_REQUEST["coin"];
		$_SESSION["payment"]["amount"]=$_REQUEST["amount"];
		$_SESSION["payment"]["total"]=get_currency($_REQUEST["amount"],"INR","USD");
		$_SESSION["payment"]["callback_success"]=URL_ROOT."ajax.php?type=".$ch."&status=ok";
		$_SESSION["payment"]["callback_error"]=URL_ROOT."ajax.php?type=".$ch."&status=error";
		$data["status"]="success";
		$data["type"]="url";
		$data["msg"]="Congratulation we are procedding for payment";
		$data["url"]=URL_ROOT."payment/payumoney.php";
		echo json_encode($data);
	}
}
elseif($ch=="updateProfileDirect"){
	echo $db->updateAry(SITE_USER,array($_REQUEST["filed"]=>$_REQUEST["value"]),"where id='".$_SESSION["user"]["uid"]."'");
	echo $db->getErMsg();
}
elseif($ch=="getcatcriteria"){
	
	$alldata=$db->getRows("select * from ".CATEGORYCRITERIA." where cid='".$_REQUEST["cid"]."'");
	if(is_array($alldata) && count($alldata)>0){
		?><input type="hidden" name="criteria_i[]" value="<?php echo $_REQUEST["i"] ?>" /><?php
		foreach($alldata as $data){
			?>
			<div class="form-group">
                <input type="hidden" name="criteria_id_<?php echo $_REQUEST["i"] ?>[]" value="<?php echo $data["id"] ?>" />
                <input type="hidden" name="criteria_name_<?php echo $_REQUEST["i"] ?>[]" value="<?php echo $data["name"] ?>" />
              	<label class="col-md-3 control-label"><?php echo $data["name"] ?><br>
<small><span style="color:red"><?php echo $data["detail"] ?></span></small></label>
              	<div class="col-md-8">
              	<?php if($data["criteria_type"]==1){ ?>
                <input type="text" name="criteria_value_<?php echo $_REQUEST["i"] ?>[]" value="" class="form-control">
                <?php }elseif($data["criteria_type"]==2){
				$dataListMe=explode(",",$data["criteria_value"]);
				?>
				<select id="mydtype" name="criteria_value_<?php echo $_REQUEST["i"] ?>[]" class="form-control select input-form">
                <option value="">Select</option>
                <?php foreach($dataListMe as $ListMe){ ?>
                    <option value="<?php echo $ListMe ?>"><?php echo $ListMe ?></option>
                <?php } ?>                          
                </select>
				<?php	
				}elseif($data["criteria_type"]==3){
				?>
				<textarea rows="2" cols="30"  name="criteria_value_<?php echo $_REQUEST["i"] ?>[]" class="form-control w-input input-form textarea"></textarea>
                
				<?php	
				}?>
                </div>
            </div>
			<?php
		}
	}
	else{
		echo $db->getErMsg();
	}
}elseif($ch=="catcriteria"){
	
	$alldata=$db->getRows("select * from ".CATEGORYCRITERIA." where cid='".$_REQUEST["cid"]."'");
	if(is_array($alldata) && count($alldata)>0){
		?><input type="hidden" name="criteria_i" value="1" /><?php
		foreach($alldata as $data){
			?>
            <input type="hidden" name="criteria_id[]" value="<?php echo $data["id"] ?>" />
                <input type="hidden" name="criteria_name[]" value="<?php echo $data["name"] ?>" />
              	<?php if($data["criteria_type"]==3){ ?>
                <input type="text" name="criteria_value_<?php echo $_REQUEST["i"] ?>[]" value="" class="form-control" placeholder="<?php echo $data["name"] ?>">
                <?php }elseif($data["criteria_type"]==2){
				$dataListMe=explode(",",$data["criteria_value"]);
				?>
				<select id="mydtype" name="criteria_value_<?php echo $_REQUEST["i"] ?>[]" class="form-control select input-form">
                <option value="">Select <?php echo $data["name"] ?></option>
                <?php foreach($dataListMe as $ListMe){ ?>
                    <option value="<?php echo $ListMe ?>"><?php echo $ListMe ?></option>
                <?php } ?>                          
                </select>
				<?php	
				}elseif($data["criteria_type"]==1){
				?>
				<textarea  placeholder="<?php echo $data["name"] ?>" style="margin-Bottom: 5px;" rows="2" cols="30"  name="criteria_value_<?php echo $_REQUEST["i"] ?>[]" class="form-control w-input input-form textarea"></textarea>
                
				<?php	
				}?>
			<?php
		}
	}
	else{
		echo $db->getErMsg();
	}
}
?>