<?php
$mysql_user = 'fixnd2rg_mss';//'ms2fun';
$password = "123@qwe";
$database_host = "localhost";
$database = "fixnd2rg_ms_fixndeal";
/*$mysql_user = 'root';//'ms2fun';
$password = "root";//"123@qwe";
$database_host = "localhost";
$database = "fixndeal";*/
define("GOOGLE_API_KEY", "AIzaSyDuBVNokeRcK0kBJtp_knHXpiozWysHU78");
$connect = mysql_connect($database_host , $mysql_user , $password )  or die ("Couldnt connect to sever ".$database_host."-".$database."-".$password."-".$mysql_user);
$db=mysql_select_db($database) or die("Couldnt find  to db ".mysql_error());
//	mss_vchat_site_user
$LinksDetails=array();
$arySetData=getRows("select * from deal_settings");
if(is_array($arySetData) && count($arySetData)>0)
{
	foreach($arySetData as $iSetData)
	{
		$LinksDetails[$iSetData['field']]=unPOST($iSetData['value']);
	}
}
function POST($i, $trim = false)
{ 
	if(isset($_POST[$i]))$i=$_POST[$i];
	if ($trim)
		$i = trim($i);
	if (!get_magic_quotes_gpc())
		$i = addslashes($i);
	$i = rtrim($i);
	$look = array('&', '#', '<', '>', '"', '\'', '(', ')', '%');
	$safe = array('&amp;', '&#35;', '&lt;', '&gt;', '&quot;', '&#39;', '&#40;', '&#41;', '&#37;');
	$i = str_replace($look, $safe, $i);
	//$i = htmlentities($i);
	return $i;
}
function fetchSetting()
{
	$aryReturn=array();
	$strSetting='';
	
		
		$arySetData=getRows("select * from deal_settings");
		if(is_array($arySetData) && count($arySetData)>0)
		{
			foreach($arySetData as $iSetData)
			{
				$aryReturn[$iSetData['field']]=unPOST($iSetData['value']);
			}
		}
	return $aryReturn;
}
function unPOST($i)
{
	global $db;
	$look = array('&', '#', '<', '>', '"', '\'', '(', ')', '%');
	$safe = array('&amp;', '&#35;', '&lt;', '&gt;', '&quot;', '&#39;', '&#40;', '&#41;', '&#37;');
	$i = str_replace($safe, $look, $i);
	$msg=$i;
	return stripslashes($msg);//.$db->getErMsg();
}

function diff($timestamp,$type="min"){
	global $db;
	$diff =-(int)strtotime($timestamp)+(int)strtotime(date("Y-m-d H:i:g"))  ;
	$value="";
	if($type=="min")
    	$value = floor($diff/(60));		//min
	if($type=="hour")
		$value = floor($diff/(60*60));		//hour
	elseif($type=="day")
		$value = floor($diff/(60*60*24));		//day
	elseif($type=="month")
		$value = floor($diff/(60*60*24*30));		//month
	elseif($type=="year")
		$value = floor($diff/(60*60*24*30*12));		//year
		
    return $value;
}//input format: Y-m-d
function dateadd($day,$toadd){
	$tmp = explode("-",$day);
	$dadate = mktime(0,0,0,$tmp[1],$tmp[2]+($toadd),$tmp[1]);
	return date('Y-m-d H:i:s',$dadate);
}
function update($table,$data,$condition=""){
	$fields="";
	$values = "";
	foreach($data as $key => $val)
	{
		$fields.=$key."='".$val."',";
	}
	$fields = substr($fields,0,strlen($fields)-1);
	$qry="update $table set $fields $condition";
	$q = mysql_query($qry)or die("Update Error.".mysql_error()."<strong>Last Query : </strong><pre>$qry</pre>");
	if($q)
	{
	return $q;
	}
}
function insert($table,$data){
	
	$fields="";
	$values = "";
	foreach($data as $key => $val)
	{
		$fields.=$key.',';
		$values.="'".$val."',";
	}
	$fields = substr($fields,0,strlen($fields)-1);
	$values = substr($values,0,strlen($values)-1);
	$query="insert into $table ($fields) values($values)";
	$q = mysql_query($query) or die("Insert Error.".mysql_error()."<br />Last Query : ".$query);
	return mysql_insert_id();
}
function query($sql){
	$recSet=mysql_query($sql);
	if($recSet)
	{
		return $recSet;
	}
	else
	{
		return NULL;
	}
}
function getRows($sql){
	$aryResult=array();
	
	$result=query($sql);
	
	if(!is_null($result)) 
	{ 
		while($row=mysql_fetch_array($result)) { $aryResult[]=$row; }
		return $aryResult;
	}
	else
	{
		return NULL;
	}
}
//method to fetch just a single record
function getRow($sql){
	$aryResult=getRows($sql);
	if(is_array($aryResult))
	{
		if(count($aryResult)==0)
		{
			return array();
		}
		else
		{
			return $aryResult[0];
		}
	}
	else
	{
		return NULL;
	}
}
//function to fetch just a single field
function getVal($sql,$erVal=NULL){
	$aryResult=getRow($sql);
	if(is_array($aryResult) && count($aryResult)>0)
	{
		return $aryResult[0];
	}
	else
	{
		return $erVal;
	}
}
function notification($notice,$type,$id,$link,$section='',$uid='',$status='1'){
		//notification type-: newsfeed
		if($uid=="");	
		if($notice!="" && $type!="" && $id!="" && $link!=""){
			$incData=array("notice"=>$notice,
							"type"=>$type, // Admin or User or Project or feed
							"uid"=>$uid, //userID
							"status"=>$status,
							"link"=>$link, //Bid or project url
							"section"=>$section, //primary key for Project,bid-TABLE NAME etc
							"link_id"=>$id //receiver ID
			);
			$res=insert("deal_notification",$incData);
		}
		return $res;
}
function mymail($from,$to,$subject,$body,$type="COMMON",$ary=array()){	
	global $db;
	$qry="select * from deal_mailmsg where msg_for='".$type."'";
	$res=mysql_query($qry);
	$row=mysql_fetch_array($res);
	//$msgs=mysql_fetch_array($qry);
	if(is_array($ary) && count($ary)>0){
		foreach($ary as $key=>$val){
			$arr_tpl_vars[]=$key;
			$arr_tpl_data[]=$val;
		}
	}else{
		$arr_tpl_vars=array('[MESSAGE]','[ADMIN]','[LOGIN]','[SITE]', '[DATE]', '[SUBJECT]');
		$arr_tpl_data = array(nl2br($body), $LinksDetails["admin_email"], URL_ROOT.'dashboard', URL_ROOT, date('d/m/Y'), $subject);
	}
	$e_msg = str_replace($arr_tpl_vars, $arr_tpl_data, $row["msg"]);
	$e_sub = str_replace($arr_tpl_vars, $arr_tpl_data, $row["subject"]);
	if($row["from_email"]!="")$from=$row["from_email"];
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: '.$from.'<'.$from.'>' . "\r\n";
	@mail($to,$e_sub,$e_msg,$headers);
	return $from."<br/>".$to."<br/>".$e_sub."<br/>".$e_msg;
}
function notification($notice,$type,$status='1',$added_by='',$id='',$url=''){
	global $db;
	/*$notice=notice or diescription
$type="user widthdraw desposite bid or ads"
$status='0 for read already 1 for admin 2 for user'
$added_by='session id who add this notice'
$id=if 1 then primary key of action
    if 2 then userid who need to recieve
$url='redirect url'*/
	$for="admin";
	if($status=="2")$for="user";
	$incData=array("notice"=>$notice,
					"nfor"=>$for,
				   "type"=>$type,
				   "status"=>$status,
				   "added_by"=>$added_by,
				   "pid"=>$id,
				   "url"=>$url
	);
	$res=insert("deal_notification",$incData);
	if($status=="2"){
		$regid=getVal("select regId from deal_site_user where id='".$id."'");
		if($regid!="")
		push_notification($regid,$notice);
	}
	return $res;
}
function push_notification($registatoin_ids, $message, $Image="") {
	$registrationIds = array($registatoin_ids);
	// prep the bundle
	$msg = array
	(
		'message'       => $message,
		'image'         => $Image,
	);
	
	$fields = array
	(
		'registration_ids'  => $registrationIds,
		'data'              => $msg
	);
	
	$headers = array
	(
		'Authorization: key=' . GOOGLE_API_KEY,
		'Content-Type: application/json'
	);
	
	$ch = curl_init();
	curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
	curl_setopt( $ch,CURLOPT_POST, true );
	curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
	$result = curl_exec($ch );
	curl_close( $ch );		
	echo $result;
}
function mypackageDetail($id){
	$st=getRow("select * from deal_user_membership where uid='".$id."' and status=1");
	if(is_array($st) && count($st)>0){
		$st["name"]=getVal("select name from deal_membership where id='".$st["mid"]."'");
		return $st;
	}else{
		$st=getVal("select id from deal_user_membership where uid='".$_SESSION["user"]["uid"]."'");
		if($st==""){
			$defaultPlan=getRow("select * from deal_membership where is_default=1 and status=1");
			$IncAry=array("uid"=>$id,

					"mid"=>$PackageId,

					"price"=>$st["price"],

					"ads"=>$st["ads"],
					
					"featured"=>$st["featured"],
					
					"Refress"=>$st["Refress"],
					
					"Highlight"=>$st["Highlight"],
					
					"featured_color"=>$st["featured_color"],
					
					"featured_text"=>$st["featured_text"],

					"day"=>$st["day"],

					"status"=>1

					);
			$pac=insert($defaultPlan,$IncAry);
			$st=$defaultPlan;
		}
	}
	return $st;
}
$qry="select is_online,last_active,id,username,email,dob from deal_site_users";
$result = getRows($qry);
//print_r($row);exit;
foreach($result as $row)
{
	$dif= diff($row['last_active']);
	if($dif>60 && $row["is_online"]==1){
		update("deal_site_users",array("is_online"=>"2","last_active"=>date("Y-m-d H:m:s"))," where id=".$row["id"]);
		$sqN = "INSERT INTO deal_login_history SET uname='" . mysql_real_escape_string($row["username"]) . "', userid='" . mysql_real_escape_string($row["id"]) . "', email='" . mysql_real_escape_string($row["email"]) . "', f_ip='" . mysql_real_escape_string($_SERVER['REMOTE_ADDR']) . "', ldate=NOW()";
		mysql_query($sqN);
	}
	$mypackageDetail=mypackageDetail($id);
	$now = time(); // or your date as well
    $your_date = strtotime($mypackageDetail['ndate']);
	$end_date = strtotime($mypackageDetail['ndate']." +".$mypackageDetail['day']." days");
    $datediff = $end_date-$now ;
    $remainingdays=floor($datediff/(60*60*24));
	if($remainingdays<=0){
		updateAry("deal_user_membership",array("status"=>"0")," where uid='".$_SESSION["user"]["uid"]."'");
	}
}
