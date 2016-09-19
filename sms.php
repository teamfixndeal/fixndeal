<?php
include("config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Find Message</title>
</head>
<body>
<form method="post" action="">
Enter Mobile No <input type="tel" value="" name="mobile" /><input type="submit" />
</form>
<table border="1">
<?php
if($_POST){
	$detaildata=$db->getRows("select * from deal_sms_history where smsto='".$_POST["mobile"]."'");
	if(is_array($detaildata) && count($detaildata)>0){
		foreach($detaildata as $dData){
			?><tr><td><?php echo $dData["udate"] ?></td><td><?php echo $dData["sms_detail"];  ?></td></tr><?php
		}
	}
}
?>
</table>
</body>
</html>