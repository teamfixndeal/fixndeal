<?php 
include_once("config.php");
$code=explode("-",base64_decode($_GET['code']));
//print_r($code);exit;
$newuser=array();
$newuser =$db->getRow("SELECT * FROM ".SITE_USER." WHERE vcode ='".$code[0]."'");
if($newuser['email_verify']==1)
{
	$_SESSION['msg']='Dear user your account is already verified';
	redirect(href("index.php"));

}
elseif($newuser['email_verify']==0)
{
	$data= array();$data['vcode']='';$data['email_verify']=1;
	$flgUp=$db->updateAry(SITE_USER,$data,"where email='".$code[1]."'");
	$body="";
	$sms="Dear ".$newuser["fullname"]." you have successfully verified your email.";
	$aryEmail=array("[NAME]"=>$newuser["fullname"]);
	mymail($LinksDetails['mail_sender_email'],$newuser["email"],$subject,$body,"VERIFIED",$aryEmail);
	mysms($newuser["mobile"],$sms,0); //sms to user
	redirect(href("index.php"));
	
}
else{
	$_SESSION["error"]="Verification link has been expired";
	redirect(href("index.php"));
}

?>
