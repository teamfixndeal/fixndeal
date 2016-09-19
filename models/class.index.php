<?php
class index
{
	function slider($type="HEADER")
	{
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRows("select * from ".SLIDER." where  status=1  and type='".$type."' order by lorder");
		if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
			foreach($contentDetail as $cdetail){$i++;
				$conDetail[$i]=$cdetail;
				$conDetail[$i]["no"]=$i;
				$galleryDetail=$db->getRows("select content from ".SLIDER_CONTENT." where sliderid='".$cdetail["id"]."' and language='".$_SESSION["lang"]."'");$j=0;
				if(is_array($galleryDetail) && count($galleryDetail)>0){$k=62;$start=2000;
					foreach($galleryDetail as $gDetail){$j++;
						$conDetail[$i]["detailInfo"][$j]=$gDetail;
						//$conDetail[$i]["detailInfo"][$j]["detail"]=nl2br($gDetail["detail"]);
						$k=$k+32;$start=$start+200;
						$conDetail[$i]["detailInfo"][$j]["start"]=$start;
						$conDetail[$i]["detailInfo"][$j]["k"]=$k;
					}
				}
			}
		}
		return $conDetail;
	}
	
	function extra_content($type)
	{
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRows("select * from ".CONTENT." where type='".$type."' and status=1 order by lorder ASC");
		//$conDetail["query"]=$db->getLastQuery();
		if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
			foreach($contentDetail as $cdetail){
				$conDetail[$i]=$cdetail;
				$conDetail[$i]["nonhtml"]=trim(strip_tags(unPOST($cdetail["content"])));
				$conDetail[$i]["no"]=$i;
				$conDetail[$i]["type"]=$db->getVal("select linkname from ".CONTENT_HEAD." where heading='".$type."' and status=1"); 
				$conDetail[$i]["content"]=ucfirst(unPOST($cdetail["content"]));
				$conDetail[$i]["Name"]=unPOST($cdetail["Name"]);
				$conDetail[$i]["url"]=href("newupdates.php","id=".$cdetail["id"]."&type=".$cdetail["type"]);
				//$conDetail[$i]["url"]="newupdates.php?id=".$cdetail["id"]."-".$cdetail["type"];
				$conDetail[$i]["social"]=json_decode($cdetail["extra"],true);
				$i++;
			}
		}
		return $conDetail;
	}
	
	function getInnerCategory($id){
		global $db;
		$categoryList=array();
		$productcat=$db->getRows("select c.name,c.id,c.image,(select count(id) from `".POSTS."` where productcat=c.id and status=1 and is_parent=0 and ptype='1') as total_product from ".CATEGORY." as c where c.status=1 and c.is_parent='".$id."'");
		$l=0;
		if(is_array($productcat) && count($productcat)>0){$k=0;
			foreach($productcat as $pcat){$l++;
				$categoryList[$l]=$pcat;
				$brandcat=$db->getRows("select c.name,c.id,c.image,(select count(id) from `".POSTS."` where brandcat=c.id and status=1 and is_parent=0 and ptype='1') as total_product from ".CATEGORY." as c where c.status=1 and c.is_parent='".$pcat["id"]."'");
				$m=0;
				if(is_array($brandcat) && count($brandcat)>0){$k=0;
					foreach($brandcat as $bcat){$m++;
						$categoryList[$l]["brandcat"][$m]=$bcat;
						$modelcat=$db->getRows("select c.name,c.id,c.image,(select count(id) from `".POSTS."` where modelcat=c.id and status=1 and is_parent=0 and ptype='1') as total_product from ".CATEGORY." as c where c.status=1 and c.is_parent='".$bcat["id"]."'");
						$n=0;
						if(is_array($modelcat) && count($modelcat)>0){$k=0;
							foreach($modelcat as $mcat){$n++;
								$categoryList[$l]["brandcat"][$m]["modelcat"][$n]=$mcat;
							}
						}

					}
				}
			}
		}
		return $categoryList;
	}
	
	function getCategory()
	{
		global $db;
		$categoryList=array();
		$category=$db->getRows("select c.name,c.id,c.image,(select count(id) from `".POSTS."` where category=c.id and status=1 and is_parent=0 and ptype='1') as total_product from ".CATEGORY." as c where c.status=1 and c.is_parent=0 ");
		if(is_array($category) && count($category)>0){$i=0;
			foreach($category as $cat){$i++;
				$categoryList[$i]=$cat;
				$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",unPOST($cat["name"]))));
				$categoryList[$i]["url"]=URL_ROOT."$pagename/category.html";
				$subcategory=$db->getRows("select c.name,c.id,c.image,(select count(id) from `".POSTS."` where subcat=c.id and status=1 and is_parent=0 and ptype='1') as total_product from ".CATEGORY." as c where c.status=1 and c.is_parent='".$cat["id"]."'");
				$j=0;
				if(is_array($subcategory) && count($subcategory)>0){$k=0;
					foreach($subcategory as $subcat){$k++;
						$categoryList[$i]["subcat"][$k]=$subcat;
					/*	
						$productcat=$db->getRows("select c.name,c.id,c.image,(select count(id) from `".POSTS."` where productcat=c.id and status=1 and is_parent=0 and ptype='1') as total_product from ".CATEGORY." as c where c.status=1 and c.is_parent='".$subcat["id"]."'");
						$l=0;
						if(is_array($productcat) && count($productcat)>0){$k=0;
							foreach($productcat as $pcat){$l++;
								$categoryList[$i]["subcat"][$k]["productcat"][$l]=$pcat;
								$brandcat=$db->getRows("select c.name,c.id,c.image,(select count(id) from `".POSTS."` where brandcat=c.id and status=1 and is_parent=0 and ptype='1') as total_product from ".CATEGORY." as c where c.status=1 and c.is_parent='".$pcat["id"]."'");
								$m=0;
								if(is_array($brandcat) && count($brandcat)>0){$k=0;
									foreach($brandcat as $bcat){$m++;
										$categoryList[$i]["subcat"][$k]["productcat"][$l]["brandcat"][$m]=$bcat;
										$modelcat=$db->getRows("select c.name,c.id,c.image,(select count(id) from `".POSTS."` where modelcat=c.id and status=1 and is_parent=0 and ptype='1') as total_product from ".CATEGORY." as c where c.status=1 and c.is_parent='".$bcat["id"]."'");
										$n=0;
										if(is_array($modelcat) && count($modelcat)>0){$k=0;
											foreach($modelcat as $mcat){$n++;
												$categoryList[$i]["subcat"][$k]["productcat"][$l]["brandcat"][$m]["modelcat"][$n]=$mcat;
											}
										}

									}
								}
							}
						}
					*/
					}
				}
				
			}
		}
		
		return $categoryList;
	}
	
	function getCity($countryId)
	{
		global $db;
	
		$category= $db->getRows("select cityName from ".CATEGORY." where status=1 and countryID='".$countryId."' ");
		
		return $category;
	}
	
	function review($type)
	{
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRows("select * from ".REVIEWS." where  language='".$_SESSION["lang"]."' and status=1");
		if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
			foreach($contentDetail as $cdetail){$i++;
				$conDetail[$i]=$cdetail;
				if($i%2==0)$conDetail[$i]["slide"]="slide-left";else $conDetail[$i]["slide"]="slide-right";
				$conDetail[$i]["nonhtml"]=strip_tags(unPOST($cdetail["content"]));
				$conDetail[$i]["no"]=$i;
				$conDetail[$i]["content"]=unPOST($cdetail["content"]);
				$conDetail[$i]["social"]=json_decode($cdetail["extra"],true);
			}
		}
		return $conDetail;
	}
	
	function new_updates($type)
	{
		global $db;
		$conDetail=array();
		$cdetail = $db->getRow("select * from ".CONTENT." where  id='".$type."' and status=1");
		if(is_array($cdetail) && count($cdetail)>0){
			$conDetail=$cdetail;
			$conDetail["nonhtml"]=strip_tags(unPOST($cdetail["content"]));
			//$conDetail["content"]=unPOST($cdetail["content"]);
			$conDetail["content"]=ucfirst(strtolower(unPOST($cdetail["content"])));
			$conDetail["Name"]=ucfirst(strtolower($cdetail["Name"]));
			$conDetail["social"]=json_decode($cdetail["extra"],true);
		}
		return $conDetail;
	}
	
	function new_jobs($type)
	{
		global $db;
		$conDetail=array();
		$cdetail = $db->getRow("select * from ".CONTENT." where  id='".$type."' and status=1");
		if(is_array($cdetail) && count($cdetail)>0){
			$conDetail=$cdetail;
			$conDetail["nonhtml"]=strip_tags(unPOST($cdetail["content"]));
			$conDetail["content"]=unPOST($cdetail["content"]);
			$conDetail["social"]=json_decode($cdetail["extra"],true);
		}
		return $conDetail;
	}
	
	function new_forms($type)
	{
		global $db;
		$conDetail=array();
		$cdetail = $db->getRow("select * from ".CONTENT." where  id='".$type."' and status=1");
		if(is_array($cdetail) && count($cdetail)>0){
			$conDetail=$cdetail;
			$conDetail["nonhtml"]=strip_tags(unPOST($cdetail["content"]));
			$conDetail["content"]=unPOST($cdetail["content"]);
			$conDetail["social"]=json_decode($cdetail["extra"],true);
		}
		return $conDetail;
	}
	
	function student_response($type)
	{
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRows("select * from ".CONTENT." where  id='".$type."' and status=1");
		if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
			foreach($contentDetail as $cdetail){$i++;
				$conDetail[$i]=$cdetail;
				if($i%2==0)$conDetail[$i]["slide"]="slide-left";else $conDetail[$i]["slide"]="slide-right";
				$conDetail[$i]["nonhtml"]=strip_tags(unPOST($cdetail["content"]));
				$conDetail[$i]["no"]=$i;
				$conDetail[$i]["content"]=unPOST($cdetail["content"]);
				$conDetail[$i]["social"]=json_decode($cdetail["extra"],true);
			}
		}
		return $conDetail;
	}
	
	function profile($type)
	{
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRows("select * from ".STUDENT." where id='".$type."' and status=1");
		if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
			foreach($contentDetail as $cdetail){$i++;
				$conDetail[$i]=$cdetail;
				$conDetail[$i]["no"]=$i;
				$class_name=$db->getVal("select class_name from ".CCLASS." where id='".$cdetail["class"]."' and status='1'");
				$subject_name=$db->getVal("select subject_name from ".SUBJECT." where id='".$cdetail["subject"]."' and status='1'");
				$added_by=$db->getVal("select fullname from ".SITE_USER." where id='".$cdetail["added_by"]."' and status='1'");
				$conDetail[$i]["added_byid"]=$added_by;
				$conDetail[$i]["class"]=$class_name;
				$conDetail[$i]["subject"]=$subject_name;
			}
		}
		return $conDetail;
	}
	
	function stu_notice($type)
	{
		global $db;
		$conDetail=array();
if(trim($type)=="")
{
$type=$_SESSION['user']['uid'];
}		
//		$class=$db->getVal("select class from ".STUDENT." where id=".$type );

		$class=$db->getRow("select * from ".STUDENT." where id=".$type );
		
		$contentDetail =$db->getRows("select * from ".STUDENT_NOTICE." where class=".$class['class']." and  (subject=0 or subject in (".$class['subject'].")) and status=1 order by date DESC ");


		if(is_array($contentDetail) && count($contentDetail)>0)
		{
			foreach($contentDetail as $cDetail)
			{
//echo"<pre>";print_r($cDetail);exit;		
	
				$studentid=explode(",",$cDetail["student_id"]);
				if(!in_array($type,$studentid))
				{
				
				 $conDetail[]=$cDetail;
				}
			}
		}
		//echo $db->getLastQuery()."<pre>";print_r($contentDetail);exit;
		return $conDetail;
	}
	
	function assignment($type)
	{
		global $db;
		$conDetail=array();
//		$class=$db->getVal("select class from ".STUDENT." where id=".$type );

		$stu_detail=$db->getRow("select * from ".STUDENT." where id=".$type );

//$stu_sub=explode(',',$stu_detail['subject']);		
		
		$contentDetail = $db->getRows("select * from ".ASSIGNMENT." where class=".$stu_detail['class']." and subject IN (".$stu_detail['subject'].")" );
		
		if(is_array($contentDetail) && count($contentDetail)>0){
			
				//$conDetail=$contentDetail;
				foreach($contentDetail as $cdtl )
				{
								$eary=explode(',',$cdtl['student_id']);
						if(!in_array($type,$eary))
						{
						$conDetail[]=$cdtl;
						}
					
				}
						
		}
		return $conDetail;
	}
	function viewassignment($type)
	{
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRow("select * from ".ASSIGNMENT." where id='".$type."' and status=1");
		if(is_array($contentDetail) && count($contentDetail)>0){
			
				//$conDetail=$contentDetail;
				$conDetail=$contentDetail;
						
		}
		return $conDetail;
	}
	
	function viewnotice($type)
	{
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRow("select * from ".STUDENT_NOTICE." where id='".$type."' and status=1");
		if(is_array($contentDetail) && count($contentDetail)>0){
			
				//$conDetail=$contentDetail;
				$conDetail=$contentDetail;
						
		}
		return $conDetail;
	}
	
	function viewcomplain($type)
	{
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRow("select * from ".COMPLAIN." where id='".$type."' and status=1");
		if(is_array($contentDetail) && count($contentDetail)>0){
			
				//$conDetail=$contentDetail;
				$conDetail=$contentDetail;
						
		}
		return $conDetail;
	}
	
	function viewvacancies($type)
	{
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRow("select * from ".CONTENT." where id='".$type."' and status=1");
		if(is_array($contentDetail) && count($contentDetail)>0){
			
				//$conDetail=$contentDetail;
				$conDetail=$contentDetail;
				$conDetail["content"]=unPOST($contentDetail["content"]);		
		}
		return $conDetail;
	}
	
	
	function viewapproch($type)
	{
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRow("select * from ".CONTENT." where id='".$type."' and status=1");
		if(is_array($contentDetail) && count($contentDetail)>0){
			//$conDetail=$contentDetail;
			$conDetail=$contentDetail;
			$conDetail["content"]=unPOST($contentDetail["content"]);
						
		}
		return $conDetail;
	}
	
	
	function student_profile($type)
	{
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRows("select * from ".STUDENT." where id='".$type."' and status=1");
		if(is_array($contentDetail) && count($contentDetail)>0){$i=0;
			foreach($contentDetail as $cdetail){$i++;
				$conDetail[$i]=$cdetail;
				$conDetail[$i]["no"]=$i;
				$class_name=$db->getVal("select class_name from ".CCLASS." where id='".$cdetail["class"]."' and status='1'");
				$subject_name=$db->getVal("select subject_name from ".SUBJECT." where id='".$cdetail["subject"]."' and status='1'");
				
				$conDetail[$i]["class"]=$class_name;
				$conDetail[$i]["subject"]=$subject_name;
			}
		}
		return $conDetail;
	}
	
		
	function get_papers($class,$subject,$uid=0)
	{
		global $db;
		//echo "$class $subject";exit;
		if($uid==0)
		{
			$uid=$_SESSION['user']['uid'];	
		}
		$conDetail=array();
		$conDetail = $db->getRows(" select * from ".PAPER." where class=".$class." and (subject in ($subject) or subject=0 ) and status=1  order by lorder ASC");

		//echo "<pre>".$class."<br />".$subject."<br />".$db->getErMsg().$db->getLastQuery()."<br />";print_r($contentDetail);exit;
		if(is_array($conDetail) && count($conDetail)>0){
			foreach($conDetail as $cd)
			{
				$no_apprive=$db->getVal("select id from ".DISALOCATE." where paper_id=".$cd["id"]." and student_id=".$uid);
				if($no_apprive==""){
					$cd['sub_name']=$this->get_sub_name($cd['subject']);
					$cd['class_name']=$this->get_class_name($cd['class']);
					$checkattnd=$db->getRow("select * from ".RESULT." where pid=".$cd['id']." and user_id=".$uid  );
					// echo "<pre>".$cd['id']."||".$uid;print_r($checkattnd); echo $db->getLastQuery();exit;
					if(!empty($checkattnd) )
					{
						if($checkattnd['status']==1)
						{
							$reattempt=$db->getVal(" select count(*) from ".REATTEMPT." where status='1' and pid=".$checkattnd['pid']." and student_id=".$uid." ");		
							//echo"<pre> ttt";print_r($reattempt);exit;		
							/*$cd['attempted']=$db->getLastQuery();*/
							if($reattempt>0)
							{
								$cd['attempted']=3;	
							}else{
								$cd['attempted']=1;	
							}
						
						}else{
							
							$diff=$this->timediff($checkattnd['exp_date']);
							
							$cd['attempted']=1;
							if($diff=='false')
							{
								$this->updtppr($checkattnd['pid'],$checkattnd['user_id']);
							}else { 
								$cd['attempted']=0;
							}
						} 
					}else{
						$cd['attempted']=0;
					}
					//echo "<pre>";print_r($checkattnd);exit;
					//echo $checkattnd['pid'];exit;
					$cdtl[]=$cd;
				}
			}
		}
		//echo "<pre>".$class."<br />".$subject."<br />".$db->getErMsg().$db->getLastQuery()."<br />";print_r($cdtl);exit;	
		return $cdtl ;
	
	
	}
	
	function updtppr($pid,$uid)
	{  global $db;
	
	$arry=array('status'=>1);
	$update=$db->updateAry(EXAMDETAILS,$arry," where pid=$pid and user_id=$uid");
	
		if($update>0)
		{
			$all_q=$db->getRows("select * from ".EXAMDETAILS." where pid=$pid and user_id=$uid and status=1");
	
	$ttl=0;
$c=array();	
			if($all_q>0)
				{	
					foreach($all_q as $qs)
						{
							$rs=checkans($qs['qid'],$qs['answer']);
							if($rs>0)
							{
								$c[]=$qs['qid'];
							}else{
								$w[]=$qs['qid'];
							}
							
						 $ttl++;	
						}
					
					$uc['ans_status']=1;
					for($ca=0;$ca<count($c);$ca++)
					{
					  $updt=$db->updateAry(EXAMDETAILS,$uc," where pid=$pid and user_id=$uid and qid=".$c[$ca]);
						
					}
								
					$uw['ans_status']=0;
					for($wa=0;$wa<count($w);$wa++)
					{
						$updt=$db->updateAry(EXAMDETAILS,$uw," where pid=$pid and user_id=$uid and qid=".$w[$wa]);
						
					}
					$status['status']=1	;
					$updt=$db->updateAry(RESULT,$status," where pid=$pid and user_id=$uid " );
					if($updt>0)
					{	
						//redirect("result_view.php?pid=".$pid."&uid=".$uid);	 
					}else{
						echo "<hr>error sss".$db->getLastQuery().$db->getErMsg();exit;
					 }
				
				}
			//else{echo "<hr>error hhh".$db->getLastQuery().$db->getLastQuery();}
				
		}else{
		$count_ed=$db->getVal("select count(*) from ".EXAMDETAILS." where pid=$pid and user_id=$uid");			
		if($count_ed==0)
			{
				$update=$db->updateAry(RESULT,$arry," where pid=$pid and user_id=$uid");
			}	
	    }
	//else{echo "error ".$db->getLastQuery().$db->getErMsg();}
	 }
	function timediff($e)
	{
	global $db;
		$odt=strtotime($e)-strtotime(date('Y-m-d H:i:s'));
		if($odt>0){$odt="true";}else{$odt="false";}
	 return $odt;	
	}
	
	function total_fee($type)
	{
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRow("select * from ".FEE_COLLECT." where student_id='".$type."' and status=1 ");
		if(is_array($contentDetail) && count($contentDetail)>0){
			
				$conDetail=$contentDetail;
						
		}
		return $conDetail;
	}
	
	
	function all_transation($type)
	{
		global $db;
		$conDetail=array();
		$fees_Form=$db->getRows("SELECT * FROM ".FEE_DETAIL." WHERE student_id='".$type."'");
  		$conDetail=$fees_Form;
		return $conDetail;
	}
	
	
	function complain($type)
	{
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRows("select * from ".COMPLAIN." where student_id='".$type."' ORDER BY udate DESC" );
		if(is_array($contentDetail) && count($contentDetail)>0){
			
				$conDetail=$contentDetail;
						
		}
		return $conDetail;
	}
	
	
	function content($type)
	{
		//echo "error==>";echo"<pre>";print_r($type['type']);exit;
		global $db;
		$conDetail=array();
		if($type['type']=='complain'){
		$contentDetail = $db->getRow("select * from ".COMPLAIN." where id='".$type['id']."' and status=1 ");
		if(is_array($contentDetail) && count($contentDetail)>0){
			
				$conDetail=$contentDetail;
						
			}
		}
		return $conDetail;
	}
	
	
	function get_paper_detail($id)
	{
		global $db;
		
		
		$conDetail=array();
		$contentDetail = $db->getRow(" select * from ".PAPER." where id=".$id);
		if(is_array($contentDetail) && count($contentDetail)>0){
		$conDetail=$contentDetail;
		
		
		}else{
		$conDetail=$db->getLastQuery();
		}
		return $conDetail ;
		
		
	}
	
	
	function get_class_name($id)
	{
		global $db;
		$class_name=$db->getVal("select class_name from ".CCLASS." where id='".$id."' and status='1'");
		
	   return $class_name;		
	}

	function get_exam_name($id)
	{
		global $db;
		$exam_name=$db->getVal(" select name from ".PAPER." where id='".$id."' and status='1'");
		
		return $exam_name;
	}

	
	
	function get_ques($pid)
	{	
		global $db;
		$conDetail=array();
		$contentDetail = $db->getVal(" select questions from ".PAPER." where id=".$pid);
		
		//return $contentDetail;exit;
		
		$qlist="";
		$questionList=json_decode($contentDetail,true);
		if(is_array($questionList) && count($questionList)>0){
			$i=0;
			foreach($questionList as $q){$i++;
				
				$qDetail=$db->getRow("select * from ".QUESTION." where id=".$q['question']);
				$qlist[$i]["marks"]=$q["marks"];
				$qlist[$i]["qid"]=$q['question'];
				$quesdcds=json_decode($qDetail['question'],true);
		
				$qlist[$i]["question"]=$quesdcds['question'];		
				$qlist[$i]["type"]=$quesdcds['type'];
		
 				$alloptions=json_decode($qDetail['options'],true);
				$options=array();
				foreach($alloptions as $opt)
				{
					$options[]=$opt['answer'];
				}
				$qlist[$i]["options"]=$options;
				$qlist[$i]['answer']=$qDetail['answer'];
				$mysn=$db->getVal("select answer from ".EXAMDETAILS." where qid='".$q['question']."' and user_id='".$_SESSION["user"]["uid"]."' and pid='".$pid."'");			 		
				if($mysn=="A")
					$qlist[$i]['myanswer']=$options[0];
				elseif($mysn=="B")
					$qlist[$i]['myanswer']=$options[1];
				elseif($mysn=="C")
					$qlist[$i]['myanswer']=$options[2];
				elseif($mysn=="D")
					$qlist[$i]['myanswer']=$options[3];
				//$qlist[$i]["quesstion"]=$q["marks"];
				//$qlist[$i]["options"]=$q["marks"];
			}
		}
		
		return $qlist	;
    }
	
	
	function cname()
	{
		global $db;
		$clas_name=$db->getRows("select * from ".CCLASS." where  status='1'");
		
		return $clas_name;
	}
	
	function get_sub_name($id)
	{
		global $db;
		$subject_name=$db->getVal("select subject_name from ".SUBJECT." where id='".$id."' and status='1'");
		
		return $subject_name;
	}
	function getname($uid){
		
		global $db;
		 if($uid==0 || $uid==1){
				 $names="SuperAdmin";
				 }else {
	$names=$db->getVal(" select fullname from ".SITE_USER." where id=".$_SESSION['admin']['uid']);
				 if($names=="") $names="SubAdmin";
				 	}
					
	return $names;	
		}
		
	function getrole($id)
	{
	 global $db;
	 $role=$db->getRow("select * from ".ROLL." where id=".$id);
		
	return $role['name'];	
	}
	
	
	function studentName($id)
	{
	 global $db;
	 $stu_name=$db->getVal("select student_name from ".STUDENT." where id=".$id);
		
	return  $stu_name;	
	}


	function getAdmin($id)
	{
	 global $db;

	 	 $admin_id=$db->getVal("select added_by from ".STUDENT." where id=".$id);
//	 	 return  $admin_id;	


		 if($admin_id==0 || $admin_id==1)
		 {
			$admin_name="SuperAdmin";
		 }else{
			$admin_name=$db->getVal("select fullname from ".SITE_USER." where id=".$admin_id);
		 }	 	 
	 return  $admin_name;	
	}
	
	
	function profiles($type)
	{
		global $db;
		$conDetail=array();
		$contentDetail = $db->getRow("select * from ".SITE_USER." where id='".$type."' ");
		
		return $contentDetail;
	}
	
	
	

}
?>