<?php 
include_once("config.php");
//echo $LinksDetails["admin_email"];
//print_r($_SESSION);exit;
//print_r($_GET);
	$newuser =$db->getVal("SELECT email FROM ".SITE_USER." WHERE vcode ='".$_GET['code']."'");

	$code=$_GET['code'];
		if($newuser!='')
		{
			redirect(URL_ROOT."changepassword.php?vcode=".$code);
			}
		else{
			
			
			echo "This Link Has Expired  ";
			
			}
		
		
?>
