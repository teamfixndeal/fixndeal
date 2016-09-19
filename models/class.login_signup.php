<?php
class login_signup
{
	function login($ARRAY){
		global $db;
		if($ARRAY['mobile']==""){
			$status=array("status"=>"error","msg"=>"Please enter mobile number");
		}elseif($ARRAY['password']==""){
			$status=array("status"=>"error","msg"=>"Please enter password");
		}else{	
			$logdata=$db->getRow("select id,mobile,mob_verify,username,email,fullname from ".SITE_USER." where (mobile='".POST($ARRAY['mobile'])."' or email='".POST($ARRAY['mobile'])."') and pass='".base64_encode(POST($ARRAY['password']))."'");
			if(is_array($logdata) && count($logdata)>0){
				$_SESSION["user"]["uid"]=$logdata["id"];
				$_SESSION["user"]["uname"]=$logdata["username"];
				$_SESSION["user"]["fullname"]=$logdata["fullname"];
				$_SESSION["user"]["email"]=$logdata["email"];
				if(isset($ARRAY["remember"]) && $ARRAY["remember"]==1){
					$time = time() + (24*3600*365) ;
					setcookie( "mobile", $ARRAY['mobile'], $time );       
					setcookie("password", $ARRAY['password'], $time);
				}
				$ua=getBrowser();
				$yourbrowser= $ua['name'] . " " . $ua['version'] ;
				$historyData=array('login_ip'=>$_SERVER['REMOTE_ADDR'],
								  'login_browser' =>$yourbrowser,
								  'userid' =>$logdata["id"],
								  'uname' => $logdata["username"],
								  'email' =>$logdata["email"],
								  'ldate' => date("Y-m-d H:i:s"));
				$UpdateAry["is_online"]=1;
				if($_COOKIE['regId']!="")$UpdateAry["regId"]=$_COOKIE['regId'];
				$db->updateAry(SITE_USER,$UpdateAry," where id='".$_SESSION["user"]["uid"]."'");
				$db->insertAry(LOGIN_HISTORY,$historyData);
				//$redirect=base64_decode($ARRAY["redirect"]);
				//$redirect=("dashboard.html");
				$redirect=URL_ROOT;
				if($logdata['mob_verify']==1){
					$status=array("status"=>"success","type"=>"url","url"=>$redirect,"msg"=>"Login Successful","data"=>$logdata);
				}else{
					$abc=$logdata['mobile'];
					$otp=randomFix('6');
					
					$db->updateAry(SITE_USER,array("otp"=>$otp)," where mobile='".$abc."' ");
					$sms="Fixndeal verification OTP For account is $otp";
					mysms($abc,$sms,'1');
				//otp send to uuser /skip ke bad user can user website
					$status=array("status"=>"success","msg"=>"Login Successfull","type"=>"url","url"=>URL_ROOT."otpverify.php?mnumber=$abc");					
					}
				
			}else
			{
				$status=array("status"=>"error","msg"=>"Invalid Mobile No. or password ");//.$db->getErMsg().$db->getLastQuery();
			}
		}
		return $status;
	}
	function signup($POST){
		global $db,$LinksDetails,$objdeal;
		$checkEmail=$db->getVal("select id from ".SITE_USER." where mobile='".$POST["mobile"]."'");
		if(!isset($POST["fname"]) || trim($POST["fname"])==""){
			$status=array("status"=>"error","msg"=>"Please enter FullName");
		}elseif(!isset($POST["mobile"]) || trim($POST["mobile"])==""){
			$status=array("status"=>"error","msg"=>"Please enter Mobile Number");
		}elseif(!isset($POST["password"]) || trim($POST["password"])==""){
			$status=array("status"=>"error","msg"=>"Please enter Password");
		}
		elseif(!isset($POST["email"]) || trim($POST["email"])==""){
			$status=array("status"=>"error","msg"=>"Please enter Email");
		}elseif(strlen(trim($POST["mobile"]))!=10){
			$status=array("status"=>"error","msg"=>"Mobile Number must 10 digit only ");
		}elseif($checkEmail!=""){
			$status=array("status"=>"error","msg"=>"Mobile already exist");
		}elseif(preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $POST["mobile"])) {
		    $status=array("status"=>"error","msg"=>"Invalid Mobile Number");
		}else{
			$role=$db->getVal("select id from ".ROLL." where status=1 limit 0,1");
			$otp=rand(100000,999999);
			$username=preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(" ","",$POST["fname"]));
			if(isset($POST["username"]) && $POST["username"]!="")$username=$POST["username"];
			$avatar="man.png";
			if(isset($POST["avatar"]) && $POST["avatar"]!="")$avatar=$POST["avatar"];
			$password=randomFix(6);
			if(isset($POST["password"]) && $POST["password"]!=""){
				if (!preg_match_all('$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$', $POST["password"])){
					return array("status"=>"error","msg"=>"Input Password must contain min 8 characters which contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character");
					exit;
				}
				$password=$POST["password"];
			}if(isset($POST["cpassword"]) && ($POST["cpassword"]!=$POST["password"])){
				return array("status"=>"error","msg"=>"Input Confirm Password must same as password");
					exit;
			}
			
			$dob=$POST['Date_Year']."-".$POST['Date_Month']."-".$POST['Date_Day'];
			$aryData=array(
						"fullname"=>$POST["fname"],
						"username"=>$username,
						"email"=>$POST["email"],
						"mobile"=>$POST["mobile"],
						"gender"=>$POST["gender"],
						"dob"=>$dob,
						"country"=>$POST["country"],
						"state"=>$POST["statenames"],
						"city"=>$POST["citynames"],
						"register_date"=>date("Y/m/d"),
						"area"=>$POST["area"],
						"zip"=>$POST["zip"],
						"utype"=>'user',
						"vcode"=>md5(microtime()),
						"pass"	=> base64_encode($password),
						"avatar"=>$avatar,
						"is_online"=>0,
						"otp"=>$otp,
						"status"=>2,
						"role"=>7,
			);
			if($_COOKIE['regId']!="")$aryData["regId"]=$_COOKIE['regId'];
			$aryData['referedby']=$POST['referid'];
			$sms="Hello ".$POST["fname"]." OTP for FixNdeal is $otp";
			if(isset($POST["calltype"]) && $POST["calltype"]!=""){
				$typeOfSocial=$POST["calltype"];
				$aryData[$typeOfSocial]=$POST[$typeOfSocial];
				$sms="Dear ".$POST["fname"]." you have successfully registered with ".$LinksDetails["site_name"].". Your Login Detail are: ID:".$POST["mobile"]." and Pass: ".$password;				
			}
			$ins=$db->insertAry(SITE_USER,$aryData);
			$rpoint=$db->getVal("select price from ".COINSDETAIL." where title='register' ");
			$pointAry=array("fromid"=>'ADMIN',"detail"=>"Registration Bonus","userid"=>$ins,"type"=>"deposit","amount"=>$rpoint,"status"=>'1');
			$ins=$db->insertAry(TRANS,$pointAry);
			$Stat=0;
			$defaultPlan=$db->getVal("select id from ".MEMBERSHIPS." where is_default=1 and status=1");
			$pac=$objdeal->savePackage($defaultPlan,$ins);
			
			mysms($POST["mobile"],$sms,$Stat);
			if(!is_null($ins)){
				$status=array("id"=>$ins,"status"=>"success","type"=>"script","script"=>"signUp","msg"=>"Check Your phone And Enter OTP".$db->getErMsg());
			}else{
				$status=array("status"=>"error","msg"=>$db->getErMsg());
			}
		}
		return $status;
	}
	function verifyEmail(){
		global $db, $LinksDetails;
		$vcode=md5(microtime());
		$upData=$db->updateAry(SITE_USER,array('vcode'=>$vcode)," where id='".$_SESSION['user']['uid']."'");
		if(!is_null($upData)){
			$body="";
			$url=URL_ROOT."login.html";
			$code=base64_encode($vcode."-".$_SESSION['user']['email']);
			$verifyurl=URL_ROOT."verification.php?code=".$code;
			$aryEmail=array("[NAME]"=>$_SESSION['user']['uname'],
							"[SITENAME]"=>$LinksDetails["site_name"],
							"[VERIFYLINK]"=>$verifyurl);
			mymail($LinksDetails['mail_sender_email'],$_SESSION['user']['email'],$subject,$body,"EMAIL_VERIFICATION",$aryEmail);
			$profile=URL_ROOT."dashboard.html";
			$status=array("status"=>"success","msg"=>"Verification Link Sent! Please Check Your Mail","type"=>"url","url"=>$profile);
		}	
		return $status;
	}
	function sendInvite($email){
		global $db, $LinksDetails;
		
		$mailAry=array( 
								"[FULLNAME]"=>$POST["fname"],
								"[SITE_NAME]"=>$LinksDetails["site_name"],
								"[LOGO]"=>$LinksDetails["logo"],
								"[PASSWORD]"=>$POST["pass"],
								"[USERNAME]"=>$POST["username"],
								"[EMAIL]"=>$email,
								"[LOGINLINK]"=>'www.mssinfotech.co.uk/halal-app',
								
								//"[LINK]"=>URL_ROOT."verification.php?code=".$vCode
							  );
			$body="";
			mymail($LinksDetails["mail_sender_email"],$POST["email"],"Congratulation ! You are successfully register on ".$LinksDetails["site_name"],"","INVITE",$mailAry);
			
			$status=array("status"=>"success","msg"=>"Verification Link Sent! Please Check Your Mail","type"=>"url","url"=>$profile);
		return $status;
	}
	function verifyotp($POST){
		$status=array();global $db, $LinksDetails;
		$otp=$db->getRow("select * from ".SITE_USER." where email='".$POST["mnumber"]." or mobile='".$POST["mnumber"]."'");
		//$ot=$db->getLastQuery();
		//return $otp;
		if($otp['otp']!=$POST['otp']){
			$status=array("status"=>"error","msg"=>"Invalid OTP. Please  Try again");
		}
		else
		{
			$ins=$db->updateAry(SITE_USER,array('otp'=>'','mob_verify'=>1)," where mobile='".$POST["mnumber"]."'");
			if(!isset($_SESSION["user"])){
				$body="";
				$sms="Dear ".$otp["fullname"]." you have successfully registered with ".$LinksDetails["site_name"].". Your Login Detail are: ID:".$POST["mnumber"]." and Pass: ".base64_decode($otp["pass"]);
				$url=URL_ROOT."login.html";
				$code=base64_encode($otp['vcode']."-".$otp['email']);
				$verifyurl=URL_ROOT."verification.php?code=".$code;
				$notice="New user :".$otp["fullname"].": registered with ".$LinksDetails["site_name"];
				$aryEmail=array("[NAME]"=>$otp["fullname"],
								"[SITENAME]"=>$LinksDetails["site_name"],
								"[LOGIN]"=>$POST["mnumber"],
								"[PASSWORD]"=>base64_decode($otp["pass"]),
								"[LINK]"=>$url,
								"[VERIFYLINK]"=>$verifyurl);
				mymail($LinksDetails['mail_sender_email'],$otp["email"],$subject,$body,"REGISTRATION",$aryEmail);
				mysms($POST["mnumber"],$sms,0); //sms to user
				notification($notice,"newuser",0,$otp["id"],$otp["id"]); //notification to admin fro new user
				$redirect=base64_encode(URL_ROOT."dashboard.html");
				$this->login(array("mobile"=>$POST["mnumber"],"password"=>base64_decode($otp["pass"]),"redirect"=>$redirect));
				$profile=URL_ROOT;
				$status=array("status"=>"success","msg"=>"Verification Successfull! Please Check Your Mail","type"=>"url","url"=>$profile);
			}
			else{
				$codep=$db->getVal("select referedby from ".SITE_USER." WHERE id='".$_SESSION['user']['uid']."'");
				$userin=$db->getVal("select id from ".SITE_USER." where referedby='".$codep."'");
				$pointAry=array("fromid"=>'ADMIN',"userid"=>$userin,"type"=>"deposit","amount"=>$LinksDetails["refer_coin"],"status"=>'1');
				$ins=$db->insertAry(TRANS,$pointAry);
				$status=array("status"=>"success","msg"=>"Mobile Verify Successfully!","type"=>"url","url"=>URL_ROOT);
			
			}
		}
		return $status;
		
	}
	function verifyAndroidotp($POST){
		$status=array();global $db,$objdeal, $LinksDetails;
		if($_SESSION["form"]["otp"]!=$POST['otp']){
			$status=array("status"=>"error","msg"=>$POST['otp']."Invalid OTP. Please  Try again");
		}else
		{
			$role=$db->getVal("select id from ".ROLL." where status=1 limit 0,1");
			$otp=rand(100000,999999);
			$username=preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(" ","",$_SESSION["form"]["fname"]));
						
			$avatar="man.png";
			$password=randomFix(6);
			$vcode=md5(microtime());
			
			$aryData=array(
						"fullname"=>$_SESSION["form"]["fname"],
						"username"=>$username,
						"email"=>$_SESSION["form"]["email"],
						"mobile"=>$_SESSION["form"]["mobile"],
						"register_date"=>date("Y/m/d"),
						"utype"=>'user',
						"vcode"=>$vcode,
						"pass"	=> base64_encode($password),
						"avatar"=>$avatar,
						"is_online"=>0,
						
						"mob_verify"=>1,
						
						"status"=>2,
						"role"=>7,
			);
			if($_COOKIE['regId']!="")$aryData["regId"]=$_COOKIE['regId'];
			$aryData['referedby']=$POST['referid'];
			$sms="Dear ".$_SESSION["form"]["fname"]." you have successfully registered with ".$LinksDetails["site_name"].". Your Login Detail are: ID:".$_SESSION["form"]["mobile"]." and Pass: ".$password;				
			$ins=$db->insertAry(SITE_USER,$aryData);
			$rpoint=$db->getVal("select price from ".COINSDETAIL." where title='register' ");
			$pointAry=array("fromid"=>'ADMIN',"userid"=>$ins,"type"=>"deposit","amount"=>$rpoint,"status"=>'1');
			$ins=$db->insertAry(TRANS,$pointAry);
			
			$Stat=0;
			$defaultPlan=$db->getVal("select id from ".MEMBERSHIPS." where is_default=1 and status=1");
			$pac=$objdeal->savePackage($defaultPlan,$ins);
			
			
				$body="";
				$sms="Dear ".$otp["fullname"]." you have successfully registered with ".$LinksDetails["site_name"].". Your Login Detail are: ID:".$POST["mnumber"]." and Pass: ".base64_decode($otp["pass"]);
				$url=URL_ROOT."login.html";
				$code=base64_encode($vcode."-".$_SESSION["form"]['email']);
				$verifyurl=URL_ROOT."verification.php?code=".$code;
				$notice="New user :".$_SESSION["form"]["fname"].": registered with ".$LinksDetails["site_name"];
				$aryEmail=array("[NAME]"=>$_SESSION["form"]["fname"],
								"[SITENAME]"=>$LinksDetails["site_name"],
								"[LOGIN]"=>$_SESSION["form"]["mobile"],
								"[PASSWORD]"=>base64_decode($password),
								"[LINK]"=>$url,
								"[VERIFYLINK]"=>$verifyurl);
				mymail($LinksDetails['mail_sender_email'],$_SESSION["form"]["email"],$subject,$body,"REGISTRATION",$aryEmail);
				mysms($_SEESION["form"]["mobile"],$sms,0); //sms to user
				notification($notice,"newuser",0,$ins,$ins,""); //notification to admin fro new user
				$redirect=base64_encode(URL_ROOT."dashboard.html");
				$this->login(array("mobile"=>$_SESSION["form"]["mobile"],"password"=>$password,"redirect"=>$redirect));
				$profile=URL_ROOT;
				$status=array("status"=>"success","msg"=>"Verification Successfull! Please Check Your Mail","type"=>"url","url"=>$profile);
				unset($_SESSION["form"]);
		}
		return $status;
	}
	function verifyOtpForLostPass($POST){
		$status=array();global $db, $LinksDetails;
		if(is_numeric($POST["mnumber"])){
			$otp=$db->getRow("select * from ".SITE_USER." where mobile='".$POST["mnumber"]."'");
		}else{
			$otp=$db->getRow("select * from ".SITE_USER." where email='".$POST["mnumber"]."'");	
		}
		if($otp['otp']!=$POST['otp']){
			$status=array("status"=>"error","msg"=>$POST['mnumber']);
		}else
		{
			if(!isset($_SESSION["user"])){
				$body="";
				$sms="Dear ".$otp["fullname"]." your password for ".$LinksDetails["site_name"]." is : ".base64_decode($otp["pass"])."";
				$aryEmail=array("[NAME]"=>$otp["fullname"],"[PASSWORD]"=>base64_decode($otp["pass"]));
				mymail($LinksDetails['mail_sender_email'],$otp["email"],$subject,$body,"FORGOT",$aryEmail);
				mysms($POST["mnumber"],$sms,0); //sms to user
				$profile=URL_ROOT;
				$status=array("status"=>"success","msg"=>"Password Sent to your Mail! Please Check Your Mail","type"=>"url","url"=>$profile);
			}else{
				$status=array("status"=>"success","msg"=>"Mobile Verify Successfully!","type"=>"url","url"=>"");
			}
		}
		return $status;
		
	}
	function UpdateProfile($POST){
		//return array("status"=>"success","msg"=>$POST['fullname']);exit;
		global $db,$LinksDetails;
		$checkEmail=$db->getVal("select username from ".SITE_USER." where username='".$POST["username"]."' and id<>".$_SESSION['user']['uid']);
		if(!isset($POST["fullname"]) || trim($POST["fullname"])==""){
			$status=array("status"=>"error","msg"=>"Please enter your Full Name");
		}elseif($checkEmail!=""){
			$status=array("status"=>"error","msg"=>"Username $checkEmail Already Exsist");
		}elseif(!isset($POST["country"]) || trim($POST["country"])==""){
			$status=array("status"=>"error","msg"=>"Please Select Country");
		}elseif(!isset($POST["statenames"]) || trim($POST["statenames"])==""){
			$status=array("status"=>"error","msg"=>"Please Select State");
		}else{	
			$aryData=array( "fullname"=>$POST["fullname"],
							"username" => $POST["username"],
							"gender"=>$POST["gender"],
							"dob"=>$POST["dob"],
							"country"=>$POST["country"],
							"state"=>$POST["statenames"],
							"city"=>$POST["citynames"],
							"area"=>$POST["area"],
							"zip"=>$POST["zip"]
						);
			$ins=$db->updateAry(SITE_USER,$aryData,"where id ='".$_SESSION['user']['uid']."'");
		if(!is_null($ins)){
           		$status=array("status"=>"success","msg"=>"Profile updated successfully");
		}else{
				$status=array("status"=>"error","msg"=>$db->getLastQuery());
			}
		}
		return $status;
	}
	function forgot($array){
		global $db,$LinksDetails;
		$isValid="yes";
		if(is_numeric($array['mobile'])){
			$getMobDetail=$db->getVal("select id from ".SITE_USER." where mobile='".$array['mobile']."'");
			if($getMobDetail==""){
				$isValid="no";
			}
		}else{
			$getEmailDetail=$db->getVal("select id from ".SITE_USER." where email='".$array['mobile']."'");
			if($getEmailDetail==""){
				$isValid="no";
			}
		}
		if($array['mobile']==""){
			$status=array("status"=>"error","msg"=>"Please Enter Email/Mobile Number");
		}elseif ($isValid=="no") {
			$status=array("status"=>"error","msg"=>"Invalid Email/Mobile Number.");
		}else{
			if(is_numeric($array['mobile'])){
					$otp=rand(100000,999999);
					$db->updateAry(SITE_USER,array('otp'=>$otp)," where mobile='".$array['mobile']."'" );
					$status=array("status"=>"success","type"=>"script","script"=>"signUp","msg"=>"Check Your phone And Enter OTP");
					$sms="Your OTP to reset your password is ".$otp;
					mysms($array["mobile"],$sms,0); //sms to user
				}else{
					$logdata=$db->getRow("select * from ".SITE_USER." where email='".$array['mobile']."'");
					if(is_array($logdata) && count($logdata)>0){
					$otp=rand(100000,999999);
					$db->updateAry(SITE_USER,array("otp"=>$otp)," where email='".$logdata["email"]."'");
					$message="Sorry you have forgotten your password at ".$LinksDetails['site_name'].". <br />Here is OTP :<strong>".$otp."</strong>";
					$mailAry=array("[NAME]"=>$logdata["fullname"],"[MESSAGE]"=>$message);
					$body="";$subject="Notification ! Sorry ".$logdata["username"]." for  losing your password ";
					mymail($LinksDetails["mail_sender_email"],$logdata["email"],$subject,$body,"COMMON",$mailAry);
					$status=array("status"=>"success","type"=>"script","script"=>"signUp","msg"=>"Check Your Email And Enter OTP");	
				}		
			}
		}
		return $status;
	}
	function resetPassword($POST){
		global $db;
		$chkPass=$db->getVal("select pass from ".SITE_USER." where id='".$_SESSION["user"]["uid"]."'");
		if(!isset($POST["opass"]) || trim($POST["opass"])==""){
			$status=array("status"=>"error","msg"=>"Please Enter old password");
		}elseif(!isset($POST["pass"]) || trim($POST["pass"])==""){
			$status=array("status"=>"error","msg"=>"Please Enter new password");
		}elseif(!isset($POST["pass"]) || strlen($POST["pass"])<6){
			$status=array("status"=>"error","msg"=>"Password must be atleast 6 character");
		}elseif(!isset($POST["cpass"]) || trim($POST["cpass"])==""){
			$status=array("status"=>"error","msg"=>"Please Enter Confirm password");
		}elseif($chkPass!=$POST["opass"]){
			$status=array("status"=>"error","msg"=>"Current Password Invalid");
		}elseif($POST["cpass"]!=$POST["pass"]){
			$status=array("status"=>"error","msg"=>"Confirm password must equal to New Password");
		}else{
			$logdata=$db->updateAry(SITE_USER,array("pass"=>base64_encode($POST["pass"])),"where id='".$_SESSION["user"]["uid"]."'");
			if(!is_null($logdata)){
				$url=URL_ROOT."dashboard.html";
				$status=array("status"=>"success","type"=>"url","url"=>$url,"msg"=>"Your Password has been successfully reset");
			}else{
				$status=array("status"=>"error","msg"=>$db->getErMsg());
			}
		}
		return $status;
	}
	function callbacklogin($POST){
		$signUP=$this->signup($POST);
		if($signUP["status"]=="success"){
			$redirect=base64_encode(URL_ROOT."dashboard.html");
			$data=$this->login(array("mobile"=>$POST["mobile"],"password"=>$POST["password"],"redirect"=>$redirect));
			return $data;
		}else{
			return $signUP;
		}
	}
	function SubscribeNow($POST){
		global $db,$LinksDetails;
		$chkemail=$db->getVal("select id from ".SITE_USER." where email='".$POST['email']."'");
		$chknews=$db->getVal("select id from ".NEWSLETTAR." where email='".$POST['email']."'");
		if(!isset($POST["email"]) || trim($POST["email"])==""){
			$status=array("status"=>"error","msg"=>"Please enter email address");
		}elseif($chkemail!=""){
			$status=array("status"=>"error","msg"=>"You are a registered user. Please login with cridential");
		}elseif($chknews!=""){
			$status=array("status"=>"error","msg"=>"You have already subscribed with us");
		}else{
			$flgn=$db->insertAry(NEWSLETTAR,array("email"=>$POST['email'],"status"=>1));
			if(!is_null($flgn)){
				$subject="Subscribed Successfully | ".$LinksDetails['site_name'];
				$msg="You have successfully subscribed with ".$LinksDetails['site_name'];
				$aryEmail=array("[NAME]"=>$POST["email"],"[MESSAGE]"=>$msg);
				mymail($LinksDetails['mail_sender_email'],$POST["email"],$subject,$body,"COMMON",$aryEmail);
				$status=array("status"=>"success","msg"=>"You have subscribe successfully","type"=>"url","url"=>"");	
			}
		}
		return $status;
	}
	function contactus($POST){
		global $db,$LinksDetails;
		if(!isset($POST["fname"]) || trim($POST["fname"])==""){
			$status=array("status"=>"error","msg"=>"Please enter first name");
		}elseif(!isset($POST["lname"]) || trim($POST["lname"])==""){
			$status=array("status"=>"error","msg"=>"Please enter last name");
		}elseif(!isset($POST["email"]) || trim($POST["email"])==""){
			$status=array("status"=>"error","msg"=>"Please enter email address");
		}elseif(!isset($POST["message"]) || trim($POST["message"])==""){
			$status=array("status"=>"error","msg"=>"Please enter your message");
		}else{
			$aryEmail=array("[NAME]" => $POST["fname"]." ".$POST["lname"],
							"[EMAIL]"	=>	$POST["email"],
							"[MOBILE]"	=>	$POST["mobile"],
							"[MESSAGE]"	=>	$POST["message"],
							"[SITENAME]"	=>	$LinksDetails['site_name']
			);	
			$subject="";$body="";
			$subject1="Acknowledgement from ".$LinksDetails['site_name'];
			$msg="This is automatically generated message to infor you that we have received your query. We will contact you soon";
			$aryEmail1=array("[NAME]" => $POST["fname"]." ".$POST["lname"],
							"[MESSAGE]"	=>	$msg
			);
			mymail($LinksDetails['mail_sender_email'],$LinksDetails["contact_email"],$subject,$body,"CONTACT",$aryEmail);
			mymail($LinksDetails['mail_sender_email'],$POST["email"],$subject1,$body,"COMMON",$aryEmail);
			return array("status"=>"success","msg"=>"Query sent successfully","type"=>"url","url"=>"");
		}
	}
}
?>