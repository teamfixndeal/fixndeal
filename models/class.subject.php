<?php
class subject{
	function student_class()
	{
		global $db;
		$getClass=$db->getRows("select * from ".CCLASS." where status=1");
		return $getClass;
	}
	function nonUserExam($array)
	{
		global $db,$objindex,$LinksDetails;
		$data=array();$r_id=randomFix(6);
		
		$chkEmail=$db->getVal("select email from ".STUDENT." where email='".$array['email']."'");
		$chkNonUserEmail=$db->getVal("select email from ".NON_USER." where email='".$array['email']."'");
		if(isset($array["fullname"]) && $array["fullname"]==""){
			$data["msg"]="Your name cannot blank";
			$data["status"]="error";
		}elseif(isset($array["email"]) && $array["email"]==""){
			$data["msg"]="Your name cannot blank";
			$data["status"]="error";
		}elseif(isset($array["mobile"]) && $array["mobile"]==""){
			$data["msg"]="Your mobile number cannot blank";
			$data["status"]="error";
		}elseif(isset($array["detail"]) && $array["detail"]==""){
			$data["msg"]="Detail cannot blank";
			$data["status"]="error";
		}elseif(isset($array["course"]) && $array["course"]==""){
			$data["msg"]="Please Select Course";
			$data["status"]="error";
		}elseif(isset($array["stream"]) && $array["stream"]==""){
			$data["msg"]="Please Select Stream";
			$data["status"]="error";
		}elseif($chkEmail!=""){
			$data["msg"]="You are already registered as student. Please login with credential";
			$data["status"]="error";
		}elseif($chkNonUserEmail!=""){
			$data["msg"]="Email ID already exsist";
			$data["status"]="error";
		}else{
			$aryData=array('fullname' => $array['fullname'],
						   'email'	  => $array['email'],
						   'mobile'	  => $array['mobile'],
						   'detail'	  => $array['detail'],
						   'cclass'	  => $array['course'],
						   'csubject' => $array['stream'],
						   'examid'	  => $array['exam']
			);
			$flgn=$db->insertAry(NON_USER,$aryData);
			if(!is_null($flgn))
			{

				$examname=$db->getVal(" select name from ".PAPER." where id='".$array['exam']."' and status='1'");
//$objindex->get_exam_name($array["exam"]);
				$sub_name=$db->getVal("select subject_name from ".SUBJECT." where id='".$array['stream']."' and status='1'");
//$objindex->get_sub_name($array["stream"]);
				$class_name=$db->getVal("select class_name from ".CCLASS." where id='".$array['course']."' and status='1'");
//$objindex->get_class_name($array["course"]);
				
				$data["msg"]="Please click on attempt free test ";
;
				$data["status"]="ok";
				$data["eid"]=$array['exam'];
				$_SESSION['non_user']=1;
				$_SESSION["user"]["uid"]=$flgn;
				$subject1="New User ".$array['fullname']." atteming Exam";
				$usermailAry=array('[FULLNAME]'=>$aryData['fullname'],
				 				   '[SITENAME]'=>$LinksDetails["site_name"],
								   '[EMAIL]'=>$aryData["email"],
								   '[EXAM]'=>$examname,
	);

				mymail($LinksDetails['contact_email'],$array['email']," You had attended exam :".$examname." on ".$LinksDetails["site_name"],"","ONLINE_TEST_USER",$usermailAry);



/*****************admin mailAry************/


                             $AdminmailAry=array(
				                      "[FULLNAME]"=>$aryData['fullname'],
				                      "[SITENAME]"=>$LinksDetails["site_name"],
				                      "[EXAM]"=>$examname,
				                      "[EMAIL]"=>$aryData["email"]
				                );
                                mymail($LinksDetails['contact_email'],$LinksDetails['contact_email'],"New User ".$array['fullname']." atteming Exam","","ONLINE_TEST_ADMIN",$AdminmailAry);

 //********* mailing and sms section *********//	

		
			}
			else{
				$data["msg"]=$db->getErMsg();
				$data["status"]="error";
			}
		}
		return $data;
	}
	function online_admission($array)
	{
		global $db;$data=array();$r_id=randomFix(6);
		$chkEmail=$db->getVal("select email from ".STUDENT." where email='".$array['email']."'");
		if(isset($array["fullname"]) && $array["fullname"]==""){
			$data["msg"]="Your name cannot blank";
			$data["status"]="error";
		}elseif(isset($array["email"]) && $array["email"]==""){
			$data["msg"]="Your name cannot blank";
			$data["status"]="error";
		}elseif(isset($array["mobile"]) && $array["mobile"]==""){
			$data["msg"]="Your mobile number cannot blank";
			$data["status"]="error";
		}elseif(isset($array["detail_address"]) && $array["detail_address"]==""){
			$data["msg"]="Please Specify Address";
			$data["status"]="error";
		}elseif(isset($array["course"]) && $array["course"]==""){
			$data["msg"]="Please Select Course";
			$data["status"]="error";
		}elseif(isset($array["progrm"]) && $array["progrm"]==""){
			$data["msg"]="Please Select Program";
			$data["status"]="error";
		}elseif(isset($array["stream"]) && $array["stream"]==""){
			$data["msg"]="Please Select Stream";
			$data["status"]="error";
		}elseif($chkEmail!=""){
			$data["msg"]="You are already registered as student. Please login with credential";
			$data["status"]="error";
		}elseif($chkNonUserEmail!=""){
			$data["msg"]="Email ID already exsist";
			$data["status"]="error";
		}else{
			$aryData=array('fullname' => $array['fullname'],
						   'email'	  => $array['email'],
						   'mobile'	  => $array['mobile'],
						   'detail_address'	  => $array['detail_address'],
						   'cclass'	  => $array['course'],
						   'csubject' => $array['stream'],
						   'progrm'	  => $array['progrm'],
						   'remarks'  => $array['remarks'],
						   'status'   => 0
			);
			$flgn=$db->insertAry(ADMISSION,$aryData);
			if(!is_null($flgn))
			{
				$data["msg"]="Your Request Sent to our prtal. We will contact you shortly";
				$data["status"]="ok";
				$data["url"]=URL_ROOT;
				$subject1="New admisiion ".$array['fullname'];
				$mailAry=array('[NAME]'=>$array['fullname'],
							   '[EMAIL]'=>$array['email'],
							   '[MOBILE]'=>$array['mobile'],
				);
				mymail($LinksDetails["admin_email"],'ankush.mukati2603@gmail.com',$subject1,$body,"ONLINE_TEST",$mailAry);
			}
			else{
				$data["msg"]=$db->getErMsg();
				$data["status"]="error";
			}
		}
		return $data;
	}
}