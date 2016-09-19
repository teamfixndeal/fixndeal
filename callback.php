<?php include('config.php');

if($user_profile){	//facebook user

	$reg_email=$user_profile["email"];

	$fname=$user_profile["name"];

	$mobile=$user_profile["mobile"];

	$facebook_id=$user_profile["id"];

	$password=randomFix(8);

	$vCode=md5(microtime());

	$checkMEmail=$db->getRow("select id,username,email,utype,avatar from ".SITE_USER." where facebook='".$facebook_id."'");

	if(is_array($checkMEmail) && count($checkMEmail)>0)

	{

		$_SESSION["user"]["uid"]=$checkMEmail["id"];

		$_SESSION["user"]["uname"]=$checkMEmail["username"];

		$_SESSION["user"]["email"]=$checkMEmail["email"];

		$_SESSION["user"]["utype"]=$checkMEmail["utype"];

		$aryNewFinalData=array("is_online"=>1);

		$avatar=$checkMEmail["avatar"];

		if($avatar=="" || $avatar=="man.png" ){

			$lnewfile=md5(microtime()).".jpg";

			$content = file_get_contents("http://graph.facebook.com/".$user_profile["id"]."/picture?type=large");

			file_put_contents("uploads/avatar/".$lnewfile, $content);

			$file=URL_ROOT."uploads/ads/w/200/".$lnewfile;

			$watermark=URL_ROOT."uploads/media/w/100/watermark.png";

			smart_waterMark( $file, $watermark);

			//echo URL_ROOT."uploads/avatar/".$lnewfile;

			$avatar=$lnewfile;

			$aryNewFinalData['avatar']=$lnewfile;

		}

		?><br /><br /><br /><center><img src="<?php echo URL_ROOT."media/avatar/".$avatar ?>" /><br /><span>Welcome back, <?php echo $checkMEmail["username"] ?></span></center><?php

		$db->updateAry(SITE_USER,$aryNewFinalData," where id='".$_SESSION["user"]["uid"]."'");

		redirect(URL_ROOT."dashboard.html");

	}
	else{

		$username=preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(" ","",$fname));

		$lnewfile=md5($username).".jpg";

		$content = file_get_contents("http://graph.facebook.com/".$user_profile["id"]."/picture?type=large");

		file_put_contents(PATH_UPLOAD."avatar".DS.$lnewfile, $content);
		
		$imgurl=URL_ROOT."media/avatar/100/100/".$lnewfile;

		$file=URL_ROOT."uploads/avatar/200/200/".$lnewfile;

		$watermark=URL_ROOT."uploads/media/w/100/watermark.png";

		smart_waterMark( $file, $watermark);
		
		$data=array("photo"=>"https://graph.facebook.com/".$user_profile["id"]."/picture?type=large&w‌​idth=100&height=100",
		
					"avatar"=>$lnewfile,

					"fullname"=>$fname,

					"callbackid"=>$facebook_id,

					"username"=>$username,

					"gender"=>1,

					"email"=>$reg_email,

					"mobile"=>$mobile,
					
					"password"=>$password,

					"type"=>"facebook",

					);

		$smarty->assign("data",$data);

		$smarty->display("callback.html");

	}

}else{
	//error_reporting(90);
	if (isset($_GET['code'])) {
	  //echo "pelase wait...";
	  $client->authenticate($_GET['code']);
		//echo "<br />spelase wait...";
	  $_SESSION['token'] = $client->getAccessToken();
		
	  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	
	}
	if ($client->getAccessToken()) {   //google user

		$user = $oauth2->userinfo->get();

		//echo "<pre>";print_r($user);exit;

		$reg_email=$user["email"];

		$fname=$user["name"];

		$mobile=$user["mobile"];

		$gender=0;

		if($user["gender"]=="male")$gender=1;

		$google_id=$user["id"];

		$password=randomFix(8);

		$utype='user';

		//if(findage($birthDate)>18)$atype=1;

		$vCode=md5(microtime());

		$checkMEmail=$db->getRow("select id,username,email,utype,avatar from ".SITE_USER." where google='".$google_id."' or email='".$reg_email."'");

		if(is_array($checkMEmail) && count($checkMEmail)>0)

		{

			$_SESSION["user"]["uid"]=$checkMEmail["id"];

			$_SESSION["user"]["uname"]=$checkMEmail["username"];

			$_SESSION["user"]["email"]=$checkMEmail["email"];

			$_SESSION["user"]["utype"]=$checkMEmail["utype"];

			$aryNewFinalData=array("is_online"=>1,"google"=>$google_id);

			if($checkMEmail["avatar"]=="" || $checkMEmail["avatar"]=="man.png" ){

				$lnewfile=md5(microtime()).".jpg";

				$content = file_get_contents($user['picture']);

				//echo URL_ROOT."uploads/avatar/".$lnewfile;

				file_put_contents(PATH_UPLOAD."avatar".DS.$lnewfile, $content);

				$aryNewFinalData['avatar']=$lnewfile;

				$file=URL_ROOT."uploads/avatar/w/200/".$lnewfile;

				$watermark=URL_ROOT."uploads/avatar/w/100/watermark.png";

				smart_waterMark( $file, $watermark);

			}

			//echo "<pre>"; print_r($aryNewFinalData);exit;

			$db->updateAry(SITE_USER,$aryNewFinalData," where id='".$_SESSION["user"]["uid"]."'");

			redirect(URL_ROOT."dashboard.html");

		}
		else{

			$username=preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(" ","",$fname));

			$lnewfile=md5($username).".jpg";

			$content = file_get_contents($user['picture']);

			file_put_contents("uploads/avatar/".$lnewfile, $content);

			$file=URL_ROOT."uploads/avatar/w/200/".$lnewfile;

			$watermark=URL_ROOT."uploads/avatar/w/100/watermark.png";

			smart_waterMark($file, $watermark);
			
			$data=array("photo"=>$user['picture'],
			
						"avatar"=>$lnewfile,

						"fullname"=>$fname,

						"callbackid"=>$google_id,

						"username"=>$username,

						"gender"=>$gender,

						"email"=>$reg_email,

						"mobile"=>$mobile,

						"password"=>base64_encode(randomFix(6)),

						"type"=>"google",

						);

			$smarty->assign("data",$data);

			$smarty->display("callback.html");

		}
		
	}

}

?>



