<?php
class extra{
	function contact($array){
		$mob="/^[789][0-9]{9}$/";
		$pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$"; 
		global $db;global $LinksDetails;$data=array();
		if(isset($array["name"]) && $array["name"]==""){
			$data["msg"]="Your name cannot blank";
			$data["status"]="error";
		}elseif(isset($array["email"]) && $array["email"]==""){
			$data["msg"]="Your email cannot blank";
			$data["status"]="error";
		}elseif(isset($array["phone"]) && $array["phone"]==""){
			$data["msg"]="Your phone no cannot blank";
			$data["status"]="error";
		}elseif (!preg_match_all($mob, $array["phone"])){	
			$data["msg"]="Mobile Number should be start with 7,8,9 and of 10 digit";
			$data["status"]="error";

		}elseif (!preg_match_all($pattern, $array["email"])){
			$data["msg"]="Please Enter Valid Email ID";
			$data["status"]="error";
		}
		elseif(isset($array["comments"]) && $array["comments"]==""){
			$data["msg"]="Please Leave your comment";
			$data["status"]="error";
		}else{
			$insAry=array(  "name"=>$array["name"],
							"email"=>$array["email"],
							"mobile"=>$array["phone"],
							"status"=>1,
							"comments"=>$array["comments"]);
			$ins=$db->insertAry(CONTACT,$insAry);
			$body="
					Dear Admin<br />
					<br />
					New User want to contact to you on your site.<br />
					<br />
					User details  are:<br />
					<br />
					Name : ".$array["name"]."<br />
					Email : ".$array["email"]."<br />
					phone : ".$array["phone"]."<br />
					Contact : ".$array["contact"]."<br />
					<br />
					<br />
					";
			mymail($LinksDetails["mail_sender_email"],$LinksDetails["contact_email"]," Notice ! New User want to contact to you on your site ".$LinksDetails["site_name"],$body,"CONTACT");
			$body="
					Dear ".$array["name"]."<br />
					<br />
					Thank you for contacting us.<br />
					<br />
					Your request has been successfully posted on our site ".$LinksDetails["site_name"]." We will get back you soon...
					<br />
					<br />
					";
			mymail($LinksDetails["mail_sender_email"],$array["email"]," Thank you for contacting us",$body,"CONTACT");
			notification($body,"contact",$_SESSION["user"]["uid"],$adDetail['added_by'],PATH_ADMIN."?page_id=Contact-Us.html");
			//return $data['error']=$db->getErMsg();
			$data["msg"]="Your request has been successfully posted on our site We will get back you soon...";
			//$data['msg']=$db->getErMsg();
			$data["status"]="ok";
		}
		return $data;
	}
	
	function faq(){
		global $db;
		$faqArray=array();
		$rw=$db->getRows("select id,category_name from  ".FAQ_CATEGORY." where status=1");
		if(is_array($rw) && count($rw)>0)
		{
			$j=0;
			foreach($rw as $row)
			{ $j++;
				$faqArray["category"][$j]["id"]=$row["id"];
				$faqArray["category"][$j]["name"]=$row["category_name"];
				$faq=$db->getRows("select id,question,answer from ".FAQS." where faq_category_id='".$row['id']."'");
				if(is_array($faq) && count($faq)>0)
				{	
					foreach($faq as $fq)
					{
						$faqArray["category"][$j]["faq"][]=$fq;
					}
				}
			}
		}
		return $faqArray;
	}
	function exportMe($filename,$table,$fieldArray="",$where=""){
		$csv_terminated = "\n";
		$csv_separator = ",";
		$csv_enclosed = '"';
		$csv_escaped = "\\";
		$filter="*";
		if(is_array($fieldArray) && count($fieldArray)>0){
			$filt=array();
			foreach($fieldArray as $key => $value){
				$filt[]=$key." as ".$value;
			}
			$filter=implode(",",$filt);
			//echo $filter;print_r($fieldArray);exit;
		}
		$sql_query = "select $filter from $table $where";
		//echo $sql_query;exit;
		// Gets the data from the database
		$result = mysql_query($sql_query)or die(mysql_error()."<br>Last Query : <strong>".$sql_query."</strong>");
		$fields_cnt = mysql_num_fields($result); 
		$schema_insert = '';
		for ($i = 0; $i < $fields_cnt; $i++)
		{
			$l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,
				stripslashes(mysql_field_name($result, $i))) . $csv_enclosed;
			$schema_insert .= $l;
			$schema_insert .= $csv_separator;
		} // end for
	 
		$out = trim(substr($schema_insert, 0, -1));
		$out .= $csv_terminated;
	 
		// Format the data
		while ($row = mysql_fetch_array($result))
		{
			$schema_insert = '';
			for ($j = 0; $j < $fields_cnt; $j++)
			{
				if ($row[$j] == '0' || $row[$j] != '')
				{
	 
					if ($csv_enclosed == '')
					{
						$schema_insert .= $row[$j];
					} else
					{
						$schema_insert .= $csv_enclosed .
						str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $row[$j]) . $csv_enclosed;
					}
				} else
				{
					$schema_insert .= '';
				}
	 
				if ($j < $fields_cnt - 1)
				{
					$schema_insert .= $csv_separator;
				}
			} // end for
	 
			$out .= $schema_insert;
			$out .= $csv_terminated;
		} // end while
		//header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		//header("Content-Length: " . strlen($out));
		// Output to browser with appropriate mime type, you choose ;)
		header('Content-type: application/csv');
		//header("Content-type: text/x-csv");
		//header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=$filename");
		echo $out;
		exit;
	}
	function forgot($email){
		global $db;
//		return $email;
		
		if(trim($email)==""){
			$status=array("status"=>"error","msg"=>"Please enter email");
		}elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$status=array("status"=>"error","msg"=>"Please enter valid email id eg. example@domainname.com");
		}else{
			$logdata=$db->getRow("select * from ".STUDENT." where email='".$email."'");
			if(is_array($logdata) && count($logdata)>0){
				$vCode=md5(microtime());
				$db->updateAry(STUDENT,array("vcode"=>$vCode),"where id=".$logdata["0"]);
				$body="Dear ".$logdata["username"]."<br />
				Sorry you have forgotten your password at Indiagts. <br />
				 Your Login Id is: ".$_POST["email"]."       <br />
				To Reset your password, please '<a href='".URL_ROOT."resetPassword.php?code=".$vCode."'>click here</a><br />
				<br />
				 If you are unable to click on the link above, please paste this link in your browser window: <br />
				<a href='".URL_ROOT."resetPassword.php?code=".$vCode."'>click Here</a> <br />
				";
				mymail($LinksDetails["mail_sender_email"],$email,"Notification ! Sorry ".$logdata["username"]." for  losing your password ",$body,"FORGOT");
				$status=array("status"=>"success","msg"=>"Reset Password link has been sent to your email id");		
			}else{
				$status=array("status"=>"error","msg"=>"Invalid email Please try again");
			}			
		}
		return $status;
	}
	function resetPassword($POST){
		global $db;
		
		//$status=array("status"=>"error","msg"=>"Your reset Password link has been expired");exit;
		if(!isset($POST["pass"]) || trim($POST["pass"])==""){
			$status=array("status"=>"error","msg"=>"Please Enter password");
		}elseif (!preg_match_all('$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$', $POST["pass"])){
			$status=array("status"=>"error","msg"=>"Input Password must contain min 8 characters which contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character");
		}elseif(!isset($POST["cpass"]) || trim($POST["cpass"])==""){
			$status=array("status"=>"error","msg"=>"Please Enter Confirm password");
		}elseif($POST["cpass"]!=$POST["pass"]){
			$status=array("status"=>"error","msg"=>"Confirm password must equeal to New Password");
		}else{
			$id=$db->getRow("select id,student_name,email from ".STUDENT." where vcode='".$POST["code"]."'");
			//echo $db->getLastQuery();exit;
			if(is_array($id) && count($id)>0){
				$logdata=$db->updateAry(STUDENT,array("password"=>$POST["pass"],"vcode"=>""),"where id=".$id["id"]);
				$body="
					Dear ".$id["username"]."<br />
					<br />
					Are You Forgot password on favorchat.<br />
					<br />
					Your can Login to our site with  Login ID: <strong>".$id["email"]."</strong><br />
					<br />
					<br />
					<br />
					Your Password has been successfully reset<br />
					<br />
					<br />
					<br />
					";
					mymail($LinksDetails["mail_sender_email"],$id["email"],"Notification ! password  has been reset successfully on ".$LinksDetails["site_name"],$body,"REGISTRATION");
					$status=array("status"=>"success","msg"=>"Your Password has been successfully reset","url"=>URL_ROOT);
				
			}else{
				$status=array("status"=>"error","msg"=>"Your reset Password link has been expired");//.$db->getLastQuery();
			}
		}
		return $status;
	}

	
	function forgota($email)
	{if($array['rolid']=="student")
		{ 
		 
		global $db;
//		return $email;
		
		if(trim($email)==""){
			$status=array("status"=>"error","msg"=>"Please enter email");
		}elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$status=array("status"=>"error","msg"=>"Please enter valid email id eg. example@domainname.com");
		}else{
			$logdata=$db->getRow("select * from ".STUDENT." where email='".$email."'");
			if(is_array($logdata) && count($logdata)>0){
				$vCode=md5(microtime());
				$db->updateAry(STUDENT,array("vcode"=>$vCode),"where id=".$logdata["0"]);
				$body="Dear ".$logdata["username"]."<br />
				Sorry you have forgotten your password at Indiagts. <br />
				 Your Login Id is: ".$_POST["email"]."       <br />
				To Reset your password, please '<a href='".URL_ROOT."resetPassword.php?code=".$vCode."'>click here</a><br />
				<br />
				 If you are unable to click on the link above, please paste this link in your browser window: <br />
				<a href='".URL_ROOT."resetPassword.php?code=".$vCode."'>click Here</a> <br />
				";
				mymail($LinksDetails["mail_sender_email"],$email,"Notification ! Sorry ".$logdata["username"]." for  losing your password ",$body,"FORGOT");
				$status=array("status"=>"success","msg"=>"Reset Password link has been sent to your email id");		
			}else{
				$status=array("status"=>"error","msg"=>"Invalid email Please try again");
			}			
		}
		return $status;
	}
	else{ 
		 
		global $db;
//		return $email;
		
		if(trim($email)==""){
			$status=array("status"=>"error","msg"=>"Please enter email");
		}elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$status=array("status"=>"error","msg"=>"Please enter valid email id eg. example@domainname.com");
		}else{
			$logdata=$db->getRow("select * from ".SITE_USER." where email='".$email."'");
			if(is_array($logdata) && count($logdata)>0){
				$vCode=md5(microtime());
				$db->updateAry(SITE_USER,array("vcode"=>$vCode),"where id=".$logdata["0"]);
				$body="Dear ".$logdata["username"]."<br />
				Sorry you have forgotten your password at Indiagts. <br />
				 Your Login Id is: ".$_POST["email"]."       <br />
				To Reset your password, please '<a href='".URL_ROOT."resetPassword.php?code=".$vCode."'>click here</a><br />
				<br />
				 If you are unable to click on the link above, please paste this link in your browser window: <br />
				<a href='".URL_ROOT."resetPassword.php?code=".$vCode."'>click Here</a> <br />
				";
				mymail($LinksDetails["mail_sender_email"],$email,"Notification ! Sorry ".$logdata["username"]." for  losing your password ",$body,"FORGOT");
				$status=array("status"=>"success","msg"=>"Reset Password link has been sent to your email id");		
			}else{
				$status=array("status"=>"error","msg"=>"Invalid email Please try again");
			}			
		}
		return $status;
	}
	}
	
	function resetPassworda($POST){
		global $db;
		{if($array['rolid']=="student"){
		//$status=array("status"=>"error","msg"=>"Your reset Password link has been expired");exit;
		if(!isset($POST["pass"]) || trim($POST["pass"])==""){
			$status=array("status"=>"error","msg"=>"Please Enter password");
		}elseif (!preg_match_all('$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$', $POST["pass"])){
			$status=array("status"=>"error","msg"=>"Input Password must contain min 8 characters which contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character");
		}elseif(!isset($POST["cpass"]) || trim($POST["cpass"])==""){
			$status=array("status"=>"error","msg"=>"Please Enter Confirm password");
		}elseif($POST["cpass"]!=$POST["pass"]){
			$status=array("status"=>"error","msg"=>"Confirm password must equeal to New Password");
		}else{
			$id=$db->getRow("select id,student_name,email from ".STUDENT." where vcode='".$POST["code"]."'");
			//echo $db->getLastQuery();exit;
			if(is_array($id) && count($id)>0){
				$logdata=$db->updateAry(STUDENT,array("password"=>$POST["pass"],"vcode"=>""),"where id=".$id["id"]);
				$body="
					Dear ".$id["username"]."<br />
					<br />
					Are You Forgot password on favorchat.<br />
					<br />
					Your can Login to our site with  Login ID: <strong>".$id["email"]."</strong><br />
					<br />
					<br />
					<br />
					Your Password has been successfully reset<br />
					<br />
					<br />
					<br />
					";
					mymail($LinksDetails["mail_sender_email"],$id["email"],"Notification ! password  has been reset successfully on ".$LinksDetails["site_name"],$body,"REGISTRATION");
					$status=array("status"=>"success","msg"=>"Your Password has been successfully reset","url"=>URL_ROOT);
				
			}else{
				$status=array("status"=>"error","msg"=>"Your reset Password link has been expired");//.$db->getLastQuery();
			}
		}
		return $status;
	}
	else
	{
		
		
		if(!isset($POST["pass"]) || trim($POST["pass"])==""){
			$status=array("status"=>"error","msg"=>"Please Enter password");
		}elseif (!preg_match_all('$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$', $POST["pass"])){
			$status=array("status"=>"error","msg"=>"Input Password must contain min 8 characters which contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character");
		}elseif(!isset($POST["cpass"]) || trim($POST["cpass"])==""){
			$status=array("status"=>"error","msg"=>"Please Enter Confirm password");
		}elseif($POST["cpass"]!=$POST["pass"]){
			$status=array("status"=>"error","msg"=>"Confirm password must equeal to New Password");
		}else{
			$id=$db->getRow("select id,username,email from ".SITE_USER." where vcode='".$POST["code"]."'");
			//echo $db->getLastQuery();exit;
			if(is_array($id) && count($id)>0){
				$logdata=$db->updateAry(SITE_USER,array("pass"=>$POST["pass"],"vcode"=>""),"where id=".$id["id"]);
				$body="
					Dear ".$id["username"]."<br />
					<br />
					Are You Forgot password on favorchat.<br />
					<br />
					Your can Login to our site with  Login ID: <strong>".$id["email"]."</strong><br />
					<br />
					<br />
					<br />
					Your Password has been successfully reset<br />
					<br />
					<br />
					<br />
					";
					mymail($LinksDetails["mail_sender_email"],$id["email"],"Notification ! password  has been reset successfully on ".$LinksDetails["site_name"],$body,"FORGOT");
					//echo "Your Password Updated Successfully Click <a href='".URL_ROOT."' Here To Login";
					$status=array("status"=>"success","msg"=>"Your Password has been successfully reset","url"=>URL_ROOT);
			}else{
				$status=array("status"=>"error","msg"=>"Your reset Password link has been expired");//.$db->getLastQuery();
			}
		}
		return $status;
	
		
		}
	
		}
	}
	function getCountryList(){
		global $db;
		$countryList=$db->getRows("select * from citystatecountry where nametype='c' ");
		return $countryList;
	}
	function getStateList(){
		global $db;
		$countryList=$db->getRows("select DISTINCT state from tblcitylist ");
		return $countryList;
	}
	function getCatList($idS){
		global $db;			
		$id="0";
		if(isset($idS) && $idS!="")$id=$idS;
		$category=$db->getRows("select c.*,(select count(id) from `".POSTS."` where category=c.id) as total_product from ".CATEGORY." as c where c.status=1 and c.is_parent= ".$id);
		if(is_array($category) && count($category)>0){$i=0;
			$categoryList["status"]="success";
			$categoryList["msg"]="";$allCat=array();
			foreach($category as $cat){$i++;
			
				$allCat[]=array("name"=>$cat["name"],"id"=>$cat["id"],"image"=>URL_ROOT."uploads/category/h/60/".$cat["image"],"product"=>$db->getVal("select count(id) from ".POSTS. " where subcat='".$cat["id"]."'"));
			
					
			
			
			}
			$categoryList["data"]=$allCat;
		}else{
			$categoryList["status"]="error";
			$categoryList["msg"]="Recod not found";
		}
		return ($categoryList);	
	}	

}