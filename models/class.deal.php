<?php



class deal



{







	function getProductRating($pid){



		global $db;



		$list=$db->getVal("select star from ".REVIEWS." where post_id='".$pid."'");



		



		return $list;



		}

		

	function getcomment($pid,$Usid){



		global $db;



		$list=$db->getVal("select comment from ".REVIEWS." where post_id='".$pid."' and reviewfor='".$Usid."'");



		



		return $list;



		}	

		

	function getRGiver($pid,$Usid){



		global $db;



		$list=$db->getVal("select added_by from ".REVIEWS." where post_id='".$pid."' and reviewfor='".$Usid."'");



		$listu=$db->getVal("select fullname from ".SITE_USER." where id='".$list."'");



		return $listu;



		}	



		function getChatUserList($userid){



		global $db;



		//$userid="ankushmukati";



		$Data=$db->getRows("select DISTINCT mfrom,mto  from deal_chatbk where mfrom='".$userid."' or mto='".$userid."' ");



		$i=-1;



		foreach ($Data as $List){$i++;



		$List=$Data;



		$Data[$i]['avatar']=$db->getVal("select avatar from ".SITE_USER." where username='".$List[$i]['mto']."'");



		$Data[$i]['last']=$db->getVal("select message from deal_chatbk where mfrom='".$userid."' or mto='".$userid."' order by sent DESC");



		//$Data[$i]['last']=$db->getLastQuery();



		//$Data[$i]['w']=$List[$i]['mto'];



		}



		return $Data;



		



		}



		



	function fetchMessage($from,$to){



		//from=user id 



		//to=your id



		



		global $db;



		//$ListMessage=$db->getRows("select m.*,u.fullname,u.avatar from deal_chatbk as m ,".SITE_USER." as u where ((m.mto=".$to." and m.mfrom=".$from." and u.id=m.mto) or (m.mfrom=".$to." and m.mto=".$from." and u.id=m.mto)) order by m.sent");



		$ListMessage=$db->getRows("select * from deal_chatbk where (mto='".$to."' and mfrom='".$from."' ) or (mfrom='".$to."' and mto='".$from."' ) order by sent");



		//echo $db->getLastQuery();



		$ary=array("status"=>1);



		//	$ins=$db->updateAry(MESSAGE,$ary," where mto=".$to."");



		//echo $db->getLastQuery();



		return($ListMessage);



	}



	function addfav($pid){



		global $db;$mtype="";



		$chk=$db->getVal("select id from ".FAV." where pid='".$pid."' and userid='".$_SESSION['user']['uid']."' ");	



		if($chk=="")



		{



			$aryData=array(



						"pid"=>$pid,



						"userid"=>$_SESSION['user']['uid'],



						"status"=>'1'



			);



			$ins=$db->insertAry(FAV,$aryData);



			if(!is_null($ins)){



	



				$status=array("status"=>"success","rtype"=>"add","msg"=>"Added to Favourites","type"=>"div");	



			}else



			{



				$status=array("status"=>"error","msg"=>$db->getErMsg().$db->getLastQuery());



			}



		}



		else{



			$del=$db->delete(FAV," where id='".$chk."' ");	



			if(!is_null($chk)){



				$status=array("status"=>"success","rtype"=>"remove","msg"=>"Deleted from Favourites","type"=>"div");



			}else{



				$status=array("status"=>"error","msg"=>"");



			}



		}



		return $status;



	}



	function savePackage($PackageId,$id){



	global $db;



	$st=$db->getRow("select * from ".MEMBERSHIPS." where id='".$PackageId."'");



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



	$balance=getBalance();



	if($balance["balance"]>$st["price"] || $st["price"]==0 || $st["is_default"]==1){



		$ins=$db->updateAry(USERMEMBERSHIP,array("status"=>0),"where uid=".$id);



		$inc=$db->insertAry(USERMEMBERSHIP,$IncAry);



		//echo $db->getLastQuery().$db->getErMsg();exit;



		//if(myDetail("payment_by")=="0")

		{



			if($st["is_default"]==0){//make entry only for non default package



				withdraw($st["price"],"Admin","Update Package from your old plan to <strong>".$st["name"]."</strong> Plan");



			}



		}



		



		return $inc=array("status"=>$inc,



				"message"=>$db->getErMsg()



		);



		



	}else{



		return $inc=array("status"=>0,



					"message"=>"You do not have sufficient balance for add package. Click <a href='".href('diposit.php')."'>here</a> to make deposit"



		);



	}



	}







/*	function buymembership($mid,$price,$day){



		global $db;$mtype="";



	$chk=$db->getVal("select id from ".USERMEMBERSHIP." where mid='".$mid."' and status=1 and userid='".$_SESSION['user']['uid']."' ");	



			$aryData=array(



						"mid"=>$mid,



						"day"=>$day,



						"price"=>$price,



						"uid"=>$_SESSION['user']['uid'],



						"status"=>'1'



			);



			$ins=$db->insertAry(USERMEMBERSHIP,$aryData);



			if(!is_null($ins)){



	



				$status=array("status"=>"success","rtype"=>"add","msg"=>"Added ","type"=>"div");	



			}else



			{



				$status=array("status"=>"error","msg"=>$db->getErMsg().$db->getLastQuery());



			}



		 



		return $status;



	}*/



	function getSoldReview($userid){



		global $db;







		$id=$db->getVal("select id from ".SITE_USER." where username='".$userid."'");



	$statusListf=$db->getRows("select * from ".POSTS." where added_by='".$id."' and underf=0 ");

		

	$statusList=$db->getRows("select * from ".REVIEWS." where reviewfor='".$id."' and status=3 ");

				

//	$statusList=$db->getRows("select r.*,u.*,p.* from ".REVIEWS." as r,".SITE_USER." as u,".POSTS." as p where r.reviewfor='".$id."' and u.username='".$userid."' and  p.added_by='".$id."'");



		//echo $db->getLastQuery();print_r($statusList);exit;



		//return $statusList;



		$status=array();



		if(count($statusList)>0){ 



		$i=-1;



		foreach ($statusList as $spList)



		{$i++;



			$status["product"][$i]=$spList;



				$status['product'][$i]['title']=$db->getVal("select title from ".POSTS." where id='".$spList['post_id']."'");

				

			$status['product'][$i]['rgiver']=$db->getVal("select fullname from ".SITE_USER." where id='".$spList['added_by']."'");

			$status['product'][$i]['image']=$db->getVal("select image from ".POSTS." where id='".$spList['post_id']."'");

			

				

				

				

				

			$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$spList['title'])));



			$status['product'][$i]['url']=URL_ROOT."product/".$spList['id']."-".$pagename.".html";



		}





		//$status['product']['uratquery']="gi";



		}



		return $status;



		



	}



	function getAccountHistory(){



		global $db;



	$conDetail=array();



	//$pcount = $db->getVal("select count(id) from ".SEARCH_HISTORY." where  uid='".$_SESSION['user']['uid']."'");



	$startV=$_REQUEST['startV'];



	$endV=$_REQUEST['endV'];



	//$ProDetail["totPost"]=$pcount;



	$contentDetail = $db->getRows("select * from ".TRANS." where userid='".$_SESSION['user']['uid']."' OR fromid='".$_SESSION['user']['uid']."' limit $startV, $endV ");



	$ProDetail["query"]=$db->getLastQuery();



	$ProDetail["ncount"]=count($contentDetail);



	if(is_array($contentDetail) && count($contentDetail)>0){$i=0;



		foreach($contentDetail as $pd){$i++;



			$ProDetail["Result"][$i]=$pd;



			$ProDetail["Result"][$i]["i"]=$i;



			$ProDetail["Result"][$i]["fname"]=$db->getVal("select fullname from ".SITE_USER." where id='".$pd['userid']."' ");



			$ProDetail["Result"][$i]["tname"]=$db->getVal("select fullname from ".SITE_USER." where id='".$pd['fromid']."' ");



			//$ProDetail["Result"][$i]["detail"]=$pd['detail'];



			$ProDetail["Result"][$i]['images']=$image;



			$ProDetail["Result"][$i]['offers']=$objdeal->getOffers($pd["id"]);



		}



	}



	$ProDetail["status"]="searchTHistory";







return $ProDetail;



		



		



		



		}



	function buymembership($mid,$uid){



		global $db;



		$st=$db->getRow("select * from ".MEMBERSHIPS." where id='".$mid."'");



		



		$balance=getBalance($uid);



		if($balance["balance"]>$st["price"] || $st["price"]==0){



			$ins=$db->updateAry(USERMEMBERSHIP,array("status"=>0),"where uid=".$_SESSION["user"]["uid"]);



			$IncAry=array(	"uid"=>$uid,



						"mid"=>$mid,



						"price"=>$st["price"],



						"day"=>$st["day"],



						"featured"=>$st["featured"],



						"Refress"=>$st["Refress"],



						"Highlight"=>$st["Highlight"],



						"featured_color"=>$st["featured_color"],



						"featured_text"=>$st["featured_text"],



						"ads"=>$st["ads"],



						"status"=>1



					);



			$inc=$db->insertAry(USERMEMBERSHIP,$IncAry);

			notification("Update Package from your old plan to <strong>".$st["name"]."</strong> Plan",'membership','2',$_SESSION['user']['uid'],$_SESSION['user']['uid']);

			if(myDetail("payment_by")=="0"){



				if($st["is_default"]==0){//make entry only for non default package



					withdraw($st["price"],$uid,"Update Package from your old plan to <strong>".$st["name"]."</strong> Plan");

				}



			}



			$url="memberships.html";

			

		



			$status=array("status"=>"success","rtype"=>"add","msg"=>"Membership Added","type"=>"url","url"=>$url);



		}else{



			$status=array("status"=>"error","msg"=>"insufficient Balance ");



		}



		return $status;



	}







	function cheackAdsCount($userid){



		global $db;



		



		$query="SELECT COUNT( id ) FROM ".POSTS." WHERE MONTH( udate ) = MONTH( CURDATE( ) ) AND YEAR( udate ) = YEAR( CURDATE( ) ) AND added_by =".$userid." LIMIT 0 , 30";



		$count=$db->getVal($query);



		//echo $db->getLastQuery();



		return $count;



	}



	



	function getMembershipsbk(){



		global $db;



		



		$list=$db->getRows("select * from ".MEMBERSHIPS." where status=1");



		



		return($list);



		



		}

		

		

		function getMemberships(){



		global $db;



		

		$conDetail=array();



		$contentDetail = $db->getRows("select * from ".MCAT." where status='1'");



		if(is_array($contentDetail) && count($contentDetail)>0){$i=-1;



			foreach($contentDetail as $pd){$i++;



				$ProDetail[$i]=$pd;



				$ProDetail[$i]["i"]=$i;







				//$ProDetail[$i]["url"]==href("view_page.php","product_id=".$pd["id"]);



				$ProDetail[$i]['memberships']=$db->getRows("select * from ".MEMBERSHIPS." where status=1 and mcat='".$pd['id']."'");



			}



		}



		return $ProDetail;



		



		}	



		



	function acceptBid($pid){ 



		global $db;$mtype="";



		//$chk=$db->getVal("select id from ".FAV." where pid='".$pid."' and userid='".$_SESSION['user']['uid']."' ");	



			$underofid=$db->getVal("select underof from ".POSTS." where id=".$pid."");



			$aryData=array(



						"status"=>'3'



			);



			$ins=$db->updateAry(POSTS,$aryData, "where id=".$underofid."" );



			$ins=$db->updateAry(POSTS,array("status"=>3), "where id=".$pid."" );



			$rpoint=$db->getVal("select price from ".COINSDETAIL." where title=bidaccept ");



			$pointAry=array("fromid"=>'ADMIN',"userid"=>$_SESSION['user']['uid'],"type"=>"deposit","amount"=>$rpoint,"status"=>'1');



			$ins=$db->insertAry(TRANS,$pointAry);



	



			$mainuid=$db->getVal("select added_by from ".POSTS." where id=".$pid);



			$acceptAddedby=$db->getVal("select added_by from ".POSTS." where status=3 and id=".$pid."");



			$mobileno=$db->getVal("select mobile from ".SITE_USER." where id=".$acceptAddedby."");



			$pname=$db->getVal("select title from ".POSTS." where id=".$pid."");



			



			$notice="<strong>$pname</strong><br />Your Offer has been Accpeted";



			notification($notice,"Accpeted",2,$_SESSION['user']['uid'],$mainuid,URL_ROOT."product/".$pid."-".$pname.".html"); // notification to admin



		



			$Stat=0;



			$sms="Your bid accepted for $pname";



			$notaccepted=$db->getRows("select id,addeb_by from ".POSTS." where underof=".$underofid." and status!=3");	 



			$i=0;



			 foreach ($notaccepted as $naccepted)



			{



				$aryData=array(



						



						"status"=>'4'



			);



			$ins=$db->updateAry(POSTS,$aryData, "where id=".$naccepted['id']."" );



				$i++;



				$mobile=$db->getVal("select mobile from ".SITE_USER." where id=".$naccepted['added_by']."");



				$sm="The Bid is Rejected for $pname";



				$Stat=0;







				mysms($mobile,$sm,$Stat);



				



				} 



			



			mysms($mobileno,$sms,$Stat);



			if(!is_null($ins)){



	



				$status=array("status"=>"success","rtype"=>"add","msg"=>"Accepted","type"=>"div");	



			}else



			{



				$status=array("status"=>"error","msg"=>$db->getErMsg().$db->getLastQuery());



			}



		return $status;



	} 



	



	function buysellreview($detail,$star,$pid){



		global $db;



		$ad=$db->getVal("select added_by from ".POSTS." where id=".$pid."");



		$chk=$db->getVal("select id from ".REVIEWS." where post_id=".$pid." and added_by=".$_SESSION['user']['uid']."");



		if($chk>0){



	



				$status=array("status"=>"error","rtype"=>"add","msg"=>"You Can not Review Again","type"=>"div");	



			}else{



			



		



		//$chk=$db->getVal("select id from ".FAV." where pid='".$pid."' and userid='".$_SESSION['user']['uid']."' ");	



			if($ad==$_SESSION['user']['uid']){



		$type="2";



		}else{



			$type="1";



			}



			$reviewfor=$db->getVal("select added_by from ".POSTS." where id='".$pid."'");



			$aryData=array(



						"comment"=>$detail,



						"added_by"=>$_SESSION['user']['uid'],



						"reviewfor"=>$reviewfor,



						"post_id"=>$pid,



						"star"=>$star,



						"type"=>$type,



						"status"=>'3'



			);



			$ins=$db->insertAry(REVIEWS,$aryData);



			



			if(!is_null($ins)){



				$status=array("status"=>"success","rtype"=>"add","msg"=>"Submitted","type"=>"url","url"=>URL_ROOT);	



			}else



			{



				$status=array("status"=>"error","msg"=>$db->getErMsg().$db->getLastQuery());



			}



			}



		return $status;



		}



	



	function deleteBid($pid){



		global $db; 



		$bidderuid=$db->getVal("select added_by from ".POSTS." where id=".$pid);



		$biddermob=$db->getVal("select mobile from ".SITE_USER." where id='".$bidderuid."'");



			//echo $db->getLastQuery();



			$underof=$db->getVal("select underof from ".POSTS." where id=".$pid);



			$maiuid=$db->getVal("select added_by from ".POSTS." where id=".$underof);



			$pname=$db->getVal("select title from ".POSTS." where id=".$underof);



			$sms="Your Offer is cancelled for $pname";



			notification($sms,"Canceloffer",2,$_SESSION['user']['uid'],$bidderuid,URL_ROOT."product/".$pid."-".$pname.".html"); 



		$ins=$db->delete(POSTS," where id='".$pid."'");



		



		if(!is_null($ins)){



			mysms($biddermob,$sms,0); //sms to post owner



	



		$status=array("status"=>"success","msg"=>"Your Offer is cancelled successfully ","type"=>"url","url"=>"");



		}else{



			$status=array("status"=>"error","msg"=>$db->getErMsg());



		}







		return $status;



	}	



	



	function OfferDeal($POST){



		global $db; global $LinksDetails;



		if(!isset($POST["category"]) || trim($POST["category"])==""){



			$status=array("status"=>"error","msg"=>"Please Select category");



		}



		elseif(!isset($POST["postTitle"]) || trim($POST["postTitle"])==""){



			$status=array("status"=>"error","msg"=>"Please Enter Name");



		}



		



		else{



			$aryData=array(



						"title"=>$POST['postTitle'],



						"category"=>$POST['category'],



						"underof"=>$POST['product_id'],



						"ptype"=>'2',



						"subcat"=>$POST['subcatnames'],



						"country"=>$db->getVal("select country from ".SITE_USER." where id=".$_SESSION['user']['uid'].""),



						"state"=>$db->getVal("select state from ".SITE_USER." where id=".$_SESSION['user']['uid'].""),



						"streetname"=>$db->getVal("select address from ".SITE_USER." where id=".$_SESSION['user']['uid'].""),



						"added_by"=>$_SESSION['user']['uid'],



						"status"=>2,



						



						);



						



						



			for($i=0;$i<count($FILES['file']['name']);$i++)



			{



				//$imgname=implode(",",$FILES['uploadFile']['name']);



				



				$lfilename = basename($FILES['file']['name'][$i]);



				



				$lext = strtolower(substr($lfilename, strrpos($lfilename, '.')+1));



				



				if(in_array($lext,array('jpeg','jpg','gif','png')))



				{



					$lnewfile=md5(microtime()).".".$lext;



					



					if(move_uploaded_file($FILES['file']['tmp_name'][$i],PATH_UPLOAD."ads".DS.$lnewfile))



					{



						$imgfilename[]=$lnewfile;



						/*$file=URL_ROOT."uploads/ads/126/94/".$lnewfile;



						$watermark=URL_ROOT."uploads/media/w/100/watermark.png";



						smart_waterMark( $file, $watermark);*/



						$file=URL_ROOT."uploads/ads/w/638/".$lnewfile;



						$watermark=URL_ROOT."uploads/media/w/250/watermark.png";



						smart_waterMark( $file, $watermark);



					



					}				



				}



	}



	



			$aryData['image']=implode(",",$imgfilename);



			$ins=$db->insertAry(POSTS,$aryData);





			



			if(!is_null($ins)){



				$status=array("status"=>"success","msg"=>"Your Offer is submitted ","type"=>"url","url"=>URL_ROOT."dashboard.html");



			}else{



				$status=array("status"=>"error","msg"=>$db->getErMsg());



			}



		}



		



		



		



		return $status;



		



		



		



		}







	function cancelOffer($POST){



		global $db; 



		$ownerDetail=$db->getRow("select u.fullname,u.mobile from ".SITE_USER." as u,".POSTS." as p where p.added_by=u.id and p.id=(select underof from ".POSTS." where id='".$POST["id"]."')");



		$ins=$db->delete(POSTS," where id='".$POST['id']."'");



		if(!is_null($ins)){



			$sms="Dear ".$ownerDetail["fullname"].", offer removed by user on your ad ";



			mysms($ownerDetail["mobile"],$sms,0); //sms to post owner



			$status=array("status"=>"success","msg"=>"Your Offer is cancelled successfully ","type"=>"url","url"=>"");



		}else{



			$status=array("status"=>"error","msg"=>$db->getErMsg());



		}



		return $status;



	}







	function removeDeal($POST){



		global $db; 



		$title=$db->getVal("select title from ".POSTS." where id='".$POST["id"]."'");



		$ownerDetail=$db->getRows("select added_by from ".POSTS." where underof='".$POST["id"]."'");



		$ins=$db->delete(POSTS," where id='".$POST['id']."'");



		if(!is_null($ins)){



			foreach($ownerDetail as $od){



				$myd=$db->getRow("select fullname,mobile from ".SITE_USER." where id='".$od['id']."'");



				$sms="Dear ".$myd["fullname"].",deal (".$title.") has been removed";



				mysms($myd["mobile"],$sms,0); //sms to user who make offer



			}



		$status=array("status"=>"success","msg"=>"Your Offer is cancelled successfully ","type"=>"url","url"=>"");



		}else{



			$status=array("status"=>"error","msg"=>$db->getErMsg());



		}



		return $status;



	}







	function closeDeal($POST){



		global $db; 



		$title=$db->getVal("select title from ".POSTS." where id='".$POST["id"]."'");



		$ownerDetail=$db->getRows("select added_by from ".POSTS." where underof='".$POST["id"]."'");



		$ins=$db->updateAry(POSTS,array("status"=>2)," where id='".$POST['id']."'");



		if(!is_null($ins)){



			foreach($ownerDetail as $od){



				$myd=$db->getRow("select fullname,mobile from ".SITE_USER." where id='".$od['id']."'");



				$sms="Dear ".$myd["fullname"].",deal (".$title.") has been closed";



				mysms($myd["mobile"],$sms,0); //sms to user who make offer



			}



		$status=array("status"=>"success","msg"=>"Your Offer is cancelled successfully ","type"=>"url","url"=>"");



		}else{



			$status=array("status"=>"error","msg"=>$db->getErMsg());



		}



		return $status;



	}



	



	function BuySell($POST){



		global $db,$LinksDetails,$objuser;



		



			$userdetail=$objuser->userdetail($_SESSION["user"]["uid"],"*");



			



			$uidnew=$db->getVal("select added_by from ".POSTS." where id='".$POST['product_id']."'");



			//echo $db->getLastQuery();



			$mainuser=$objuser->userdetail($uidnew);



			$title=$streetname=$locality=$zip="none";



			if($POST['title']!="")$title=$POST['title'];



			if($POST['streetname']!="")$title=$POST['streetname'];



			if($POST['image']==""){$img='noimage.jpg';}else{$img=$POST['image'];}



			$aryData=array( "title"=>$title,



							"underof"=>$POST['product_id'],



							"ptype"=>2,



							"dtype"=>$POST['dtype'],



							"added_by"=>$_SESSION['user']['uid'],



							"status"=>1,



							"category"=>$POST["category"],



							"subcat"=>$POST["subcatnames"],



							"smalldes"=>$POST["smalldes"],



							"streetname"=>$streetname,



							"country"=>$userdetail['country'],



							"state"=>$userdetail['state'],



							"city"=>$userdetail['city'],



							"locality"=>$locality,



							"zip"=>$zip,



							"price"=>$POST['price'],



							"image"=>$img



						);



		



		$ins=$db->insertAry(POSTS,$aryData);



			//$ins='1';



		



			if(!is_null($ins)){



		



				$adDetail=$db->getRow("select * from ".POSTS." where id='".$POST['product_id']."'");



				$aname=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$adDetail['title'])));



				$adurl=URL_ROOT."product/".$POST['product_id']."-".$pagename.".html";



				$subject="";$body="";



				// Mail & SMS to User who have placed an offer



				$sms="Dear ".$userdetail["fullname"]." your offer successfully submitted on ad- ".$adDetail['title'];



				$aryEmail=array("[NAME]"=>$userdetail["fullname"],



								"[ADTITLE]"=>$adDetail['title'],



								"[LINK]"=>$adurl);



				mymail($LinksDetails['mail_sender_email'],$userdetail["email"],$subject,$body,"OFFER_BY_USER",$aryEmail);



				//mysms($userdetail["mobile"],$sms,0); //sms to user



				



				// Mail & SMS to User against whom offer being made



				$mdetail=$db->getRow("select * from ".SITE_USER." where id='".$adDetail['added_by']."'");



				$sms1="Dear ".$mdetail["fullname"].", you have an new offer on your ad- ".$adDetail['title'];



				$aryEmail1=array("[NAME]"=>$mdetail["fullname"],



								 "[ADTITLE]"=>$adDetail['title'],



								 "[LINK]"=>$adurl);



				mymail($LinksDetails['mail_sender_email'],$mdetail["email"],$subject,$body,"OFFER_TO_OWNER",$aryEmail1);



				mysms($mainuser['mobile'],$sms1,0); //sms to user



				



				$notice=$adDetail['title']."<br />".$_SESSION["user"]["fullname"]." Make a new offer";



				$Stat=0;



				$datanew=$db->getVal("select added_by from ".POSTS." where  id='".$aryData['underof']."'");



				//echo $db->getLastQuery();



				notification($sms1,"offer",2,$_SESSION["user"]["uid"],$adDetail['added_by'],URL_ROOT."product/".$adDetail["id"]."-".$adDetail["title"].".html"); 



				



			$status=array("status"=>"success","msg"=>"Your Offer is submitted","type"=>"url","url"=>URL_ROOT);







				



			}else{



				$status=array("status"=>"error","msg"=>$db->getErMsg());



			}



			return $status;



		



	}



	



	function getOffers($id){



		global $db;



		$pid=$db->getVal("select id from ".POSTS." where id='".$id."'");



		$status=$db->getRows("select * from ".POSTS." where  underof='".$pid."' and ptype='2'");



		



		if(count($status)>0){ 



		$i=-1;



		foreach ($status as $spList)



		{$i++;



			$Udetail=$db->getRow("select fullname,avatar,mobile from ".SITE_USER." where id=".$spList['added_by']." ");



			$status[$i]['fullname']=$Udetail['fullname'];



			$status[$i]['avatar']=$Udetail['avatar'];



			$status[$i]['mobile']=$Udetail['mobile'];



			$status[$i]['udate']=ago($status['udate']);	



		}



		}



		return $status;



		}







	function parentproname($id){



		global $db;



		//$pid=$db->getVal("select id from ".POSTS." where id='".$id."'");



		$status=$db->getRows("select title from ".POSTS." where  is_parent='".$id."' ");



		



		



		return $status;



		}		



		



		



	function FavoritesList($userid){



		global $db;



		$idlist=$db->getRows("select * from ".FAV." where userid=".$userid."");



		foreach ($idlist as  $List)



		{



			$status=$this->getSingleDetailList($List['pid']);;



			$cdetail[]=$status;



		}



		return $cdetail;	



	}



	function MyPostingListinactive($userid){



		global $db;



		$contentDetail=$db->getRows("select * from ".POSTS." where added_by='".$userid."' and status='0'");



		



		if(is_array($contentDetail) && count($contentDetail)>0){$i=0;



			foreach($contentDetail as $pd){$i++;



				$ProDetail[$i]=$pd;



				$ProDetail[$i]['url']=URL_ROOT."product/".$pd['id']."-".$pd['title'].".html";



			}



		}



		return $ProDetail;



		}



		



		



    function MyPostingList($userid,$status){



		global $db;



		$contentDetail=$db->getRows("select * from ".POSTS." where added_by='".$userid."' and status='".$status."' and underof='0' and is_parent='0'");



		



		if(is_array($contentDetail) && count($contentDetail)>0){$i=0;



			foreach($contentDetail as $pd){$i++;



				$ProDetail[$i]=$pd;



				$ProDetail[$i]['url']=URL_ROOT."product/".$pd['id']."-".$pd['title'].".html";



			}



		}



		return $ProDetail;



		}



		



	function chkmobile($POST){



		global $db;$vl="error";



		$getMob=$db->getVal("select id from ".SITE_USER." where mobile='".$POST['id']."'");



		if($getMob==""){



			$vl="success";	



		}



		return array("status"=>$vl);



	}



	function chkemail($POST){



		global $db;$vl="error";



		$getMob=$db->getVal("select id from ".SITE_USER." where email='".$POST['id']."'");



		if($getMob==""){



			$vl="success";	



		}



		return array("status"=>$vl);



	}



	function chktitle($POST){



		global $db;$vl="error";



		$getMob=$db->getVal("select id from ".POSTS." where title='".$POST['id']."'");



		if($getMob==""){



			$vl="success";	



		}



		return array("status"=>$vl);



	}



	function make_a_deal_bk($POST){



		//echo "<pre>";print_r($POST);exit;



		global $db; global $LinksDetails;$error=false;



		if(!isset($_SESSION['user']['uid'])){



			$chExist=$db->getVal("select id from ".SITE_USER." where mobile='".$POST["mobile"]."'");



			if(trim($POST["fullname"])==""){



				$status=array("status"=>"error","msg"=>"Please Enter Full Name");$error=true;



			}



			elseif(trim($POST["mobile"])==""){



				$status=array("status"=>"error","msg"=>"Please Enter Mobile");$error=true;



			}elseif(!is_numeric($POST["mobile"])){



				$status=array("status"=>"error","msg"=>"Please Enter Mobile");$error=true;



			}elseif($chExist!=""){



				$status=array("status"=>"error","msg"=>"Mobile Number ALready Exist. Please login and Re-submit the Ad.");$error=true;



			}



		}



		



		//echo "sage 2";exit;



		if(!isset($POST["dtype"]) || trim($POST["dtype"])==""){



			$status=array("status"=>"error","msg"=>"Please Select category");$error=true;



		}elseif(!isset($POST["dtype"]) || trim($POST["dtype"])==""){



			$status=array("status"=>"error","msg"=>"Please Select Type");$error=true;



		}elseif(!isset($POST["title"]) || trim($POST["title"])==""){



			$status=array("status"=>"error","msg"=>"Please Enter Title");$error=true;



		}elseif(!isset($POST["smalldes"]) || trim($POST["smalldes"])==""){



			$status=array("status"=>"error","msg"=>"Please Enter Description");$error=true;



		}

		//echo "sage 3";exit;



		if($error==false){



			//echo "sage 4";exit;



			$added_by=$_SESSION['user']['uid'];



			$underof=0;



			$p_age=$POST['pyear']."|".$POST['pmonth'];



			if(isset($POST["underof"]) && $POST["underof"]!="")$underof=$POST["underof"];



			if($POST["dtype"]=="1")$dtype="Only Cash";



			elseif($POST["dtype"]=="2")$dtype="Exchange";



			elseif($POST["dtype"]=="3")$dtype="Exchange And Cash";



			elseif($POST["dtype"]=="4")$dtype="For Sale";



			elseif($POST["dtype"]=="5")$dtype="Buy Now";



			//echo "<pre>" ;print_r($POST);exit;



			for($i=0;$i<=count($POST['category']);$i++)



			{



				$underof='0';







				$aryData=array( "category"=>$POST["category"][$i],



							"subcat"=>$POST["subcat"][$i],



							"productcat"=>$POST["productcat"][$i],



							"brandcat"=>$POST["brandcat"][$i],



							"modelcat"=>$POST["modelcat"][$i],



							"title"=>$POST["title"],



							"dtype"=>$POST["dtype"],



							"ptype"=>1,



							"smalldes"=>$POST["smalldes"],



							"streetname"=>$POST["streetname"],



							"smalldes"=>$POST["smalldes"],



							"added_by"=>$added_by,



							"country"=>$POST['country'],



							



							"state"=>$POST['statenames'],



							"city"=>$POST['citynames'],



							"locality"=>$POST['locality'],



							"zip"=>$POST['zip'],



							"status"=>0,



							"price"=>$POST['price'],



							"image"=>$POST['image'],



							"iteration" => $POST["iteration"],



							"p_age"		=> $p_age,



							"p_condition"	=> $POST["p_condition"],



							"returntype"=>$POST["returntype"],



							"categoryEx"=>$POST["categoryEx"],



							"subcatEx"=>$POST["subcatEx"],



							"productcatEx"=>$POST["productcatEx"],



							"brandcatEx"=>$POST["brandcatEx"],



							"underof"=>$underof,



							"DescriptionEx"=>$POST["descriptionEx"],



							

			);



				



				



			//echo "<pre>" ;print_r($aryData);exit;



			



			/*$aryData=array( "category"=>$POST["category"],



							"subcat"=>$POST["subcat"],



							"productcat"=>$POST["productcat"],



							"brandcat"=>$POST["brandcat"],



							"modelcat"=>$POST["modelcat"],



							"title"=>$POST["title"],



							"dtype"=>$POST["dtype"],



							"ptype"=>1,



							"smalldes"=>$POST["smalldes"],



							"streetname"=>$POST["streetname"],



							"smalldes"=>$POST["smalldes"],



							"added_by"=>$added_by,



							"country"=>$POST['country'],



							"underof"=>$underof,



							"state"=>$POST['statenames'],



							"city"=>$POST['citynames'],



							"locality"=>$POST['locality'],



							"zip"=>$POST['zip'],



							"status"=>0,



							"price"=>$POST['price'],



							"image"=>$POST['image'],



							"iteration" => $POST["iteration"],



							"p_age"		=> $p_age,



							"p_condition"	=> $POST["p_condition"],



							"returntype"=>$POST["returntype"],



							"categoryEx"=>$POST["categoryEx"],



							"subcatEx"=>$POST["subcatEx"],



							"productcatEx"=>$POST["productcatEx"],



							"brandcatEx"=>$POST["brandcatEx"],



							



							"DescriptionEx"=>$POST["descriptionEx"],



							"valuation"	=> $POST["valuation"]



			);*/



			if(isset($POST['ctype']) && $POST['ctype']=="upgradedeal")



			{



				//echo "stage8";exit;



				$aryData["ptype"]=1;



				if($i=='0'){



				$aryData["underof"]=0;}else{



					$aryData["underof"]=$ins;



					}



				$ins=$db->insertAry(POSTS,$aryData);



				$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$POST["title"])));



				$postlink=URL_ROOT."product/".$ins."-".$pagename.".html";



				$sms="Dear ".$POST["fullname"].", Your post have been successfully submitted. We are reviewing your ad and will active it soon.";



				$notice="New Post from ".$_SESSION["user"]["fullname"];



				$Stat=0;$img=explode(",",$POST['image']);



				$imgUrl=URL_ROOT."media/ads/".$img;



				mysms($POST["mobile"],$sms,$Stat);// sms to user



				notification($notice,"ads/post",1,$_SESSION["user"]["uid"],$added_by['added_by'],URL_ROOT."product/".$adDetail["id"]."-".$adDetail["title"].".html"); // notification to admin



				$aryEmail=array("[NAME]"=>$POST["fullname"],



								"[TITLE]"=>$POST["title"],



								"[DETAIL]"=>$POST["smalldes"],



								"[STATUS]"=>"Inactive",



								"[IMAGES]"=>$imgUrl,



								"[DEALTYPE]"=>$dtype,



								"[POSTLINK]"=>$postlink);



				mymail($LinksDetails['mail_sender_email'],$POST["email"],$subject,$body,"UPGRADEPOSTAD",$aryEmail);



			}



			



			



			elseif(isset($POST['pid']) && $POST['pid']!="")



			{



				$ins=$db->updateAry(POSTS,$aryData," where id='".$POST['pid']."'");



				$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$POST["title"])));



				$postlink=URL_ROOT."product/".$ins."-".$pagename.".html";



				$sms="Dear ".$POST["fullname"].", Your post have been updated successfully. We are reviewing your ad and will active it soon.";



				$notice="New Post from ".$_SESSION["user"]["fullname"];



				$Stat=0;$img=explode(",",$POST['image']);



				$imgUrl=URL_ROOT."media/ads/".$img;



				mysms($POST["mobile"],$sms,$Stat);// sms to user



				notification($notice,"Post",1,$_SESSION["user"]["uid"],$POST['pid'],$postlink); // notification to admin



				$aryEmail=array("[NAME]"=>$POST["fullname"],



								"[TITLE]"=>$POST["title"],



								"[DETAIL]"=>$POST["smalldes"],



								"[STATUS]"=>"Inactive",



								"[IMAGES]"=>$imgUrl,



								"[DEALTYPE]"=>$dtype,



								"[POSTLINK]"=>$postlink);



				mymail($LinksDetails['mail_sender_email'],$POST["email"],$subject,$body,"EDITPOSTAD",$aryEmail);



			}



			else{



				



				if(!isset($_SESSION['user']['uid']) && ($i=='0'))



				{



					//echo "stage9";exit;



					$p=randomFix(6);



					



					$username=preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(" ","",$POST["fullname"]));



					$insData=$db->insertAry(SITE_USER,array("fullname"=>$POST["fullname"],"mobile"=>$POST["mobile"],"username"=>$username."_".$p,"email"=>$POST["email"],"pass"=>$p,"role"=>7));	



					$body="";



					$otp=rand(100000,999999);



					$sms="Dear ".$POST["fullname"]." you have successfully registered with ".$LinksDetails["site_name"].". Your Login Detail are: ID:".$POST["mobile"]." and Pass: ".$p;



					$url=URL_ROOT."login.html";



					$code=base64_encode($otp."-".$POST['email']);



					$verifyurl=URL_ROOT."verification.php?code=".$code;



					$notice="New user :".$POST["fullname"].": registered with ".$LinksDetails["site_name"];



					$aryEmail=array("[NAME]"=>$POST["fullname"],



									"[SITENAME]"=>$LinksDetails["site_name"],



									"[LOGIN]"=>$POST["mobile"],



									"[PASSWORD]"=>$p,



									"[LINK]"=>$url,



									"[VERIFYLINK]"=>$verifyurl);



					mymail($LinksDetails['mail_sender_email'],$POST["email"],$subject,$body,"REGISTRATION",$aryEmail);



					mysms($POST["mobile"],$sms,0); //sms to user



					notification($notice,"newuser",1,$_SESSION["user"]["uid"],$added_by['added_by'],$verifyurl); //notification to admin fro new user



					$$added_by=$insData;



					//$added_by['underof']='0';



				



				}



					//				$added_by['added_by']=$insData;



									



					



				//echo "<pre>";print_r($aryData);echo $db->getLastQuery();exit;



				$ins=$db->insertAry(POSTS,$aryData);



				$underofary=$ins;				



				$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$POST["title"])));



				$postlink=URL_ROOT."product/".$ins."-".$pagename.".html";



				$sms="Dear ".$POST["fullname"].", Your post have been successfully submitted. We are reviewing your ad and will active it soon.";



				$notice="New Post from ".$_SESSION["user"]["fullname"];



				$Stat=0;$img=explode(",",$POST['image']);



				$imgUrl=URL_ROOT."media/ads/".$img;



				mysms($POST["mobile"],$sms,$Stat);// sms to user



				notification($notice,"post",1,$_SESSION["user"]["uid"],$added_by['added_by'],$postlink); // notification to admin



				$aryEmail=array("[NAME]"=>$POST["fullname"],



								"[TITLE]"=>$POST["title"],



								"[DETAIL]"=>$POST["smalldes"],



								"[STATUS]"=>"Inactive",



								"[IMAGES]"=>$imgUrl,



								"[DEALTYPE]"=>$dtype,



								"[POSTLINK]"=>$postlink);



				mymail($LinksDetails['mail_sender_email'],$_SESSION["user"]["email"],$subject,$body,"POSTAD",$aryEmail);



				



			}



			}



			if(!is_null($ins))



			{



				$status=array("status"=>"success","msg"=>"Your Ad is Submitted","type"=>"url","url"=>URL_ROOT."success.html");



			}else{



				$status=array("status"=>"error","msg"=>$db->getErMsg());



			}



		}



		return $status;



	}



	



	function make_a_dealbk($POST,$FILES)



	{



		



		



		global $db; global $LinksDetails;



		if($POST['dtype'=='6']){



			for($i=1;$i<=count($POST['category']);$i++)



			{



				$aryData=array( "category"=>$POST["category"][$i],



							"subcat"=>$POST["subcat"][$i],



							"productcat"=>$POST["productcat"][$i],



							"brandcat"=>$POST["brandcat"],



							"modelcat"=>$POST["modelcat"],



							"title"=>$POST["title"],



							"dtype"=>$POST["dtype"],



							"ptype"=>1,



							"smalldes"=>$POST["smalldes"],



							"streetname"=>$POST["streetname"],



							"smalldes"=>$POST["smalldes"],



							"added_by"=>$added_by,



							"country"=>$POST['country'],



							"underof"=>$ins,



							"state"=>$POST['statenames'],



							"city"=>$POST['citynames'],



							"locality"=>$POST['locality'],



							"zip"=>$POST['zip'],



							"status"=>0,



							"price"=>$POST['price'],



							"image"=>$POST['image'],



							"iteration" => $POST["iteration"],



							"p_age"		=> $p_age,



							"p_condition"	=> $POST["p_condition"],



							"returntype"=>$POST["returntype"],



							"categoryEx"=>$POST["categoryEx"],



							"subcatEx"=>$POST["subcatEx"],



							"productcatEx"=>$POST["productcatEx"],



							"brandcatEx"=>$POST["brandcatEx"],



							



							"DescriptionEx"=>$POST["descriptionEx"],



							

			);



				



				



				



			



			



			/*$aryData=array( "category"=>$POST["category"],



							"subcat"=>$POST["subcat"],



							"productcat"=>$POST["productcat"],



							"brandcat"=>$POST["brandcat"],



							"modelcat"=>$POST["modelcat"],



							"title"=>$POST["title"],



							"dtype"=>$POST["dtype"],



							"ptype"=>1,



							"smalldes"=>$POST["smalldes"],



							"streetname"=>$POST["streetname"],



							"smalldes"=>$POST["smalldes"],



							"added_by"=>$added_by,



							"country"=>$POST['country'],



							"underof"=>$underof,



							"state"=>$POST['statenames'],



							"city"=>$POST['citynames'],



							"locality"=>$POST['locality'],



							"zip"=>$POST['zip'],



							"status"=>0,



							"price"=>$POST['price'],



							"image"=>$POST['image'],



							"iteration" => $POST["iteration"],



							"p_age"		=> $p_age,



							"p_condition"	=> $POST["p_condition"],



							"returntype"=>$POST["returntype"],



							"categoryEx"=>$POST["categoryEx"],



							"subcatEx"=>$POST["subcatEx"],



							"productcatEx"=>$POST["productcatEx"],



							"brandcatEx"=>$POST["brandcatEx"],



							



							"DescriptionEx"=>$POST["descriptionEx"],



							"valuation"	=> $POST["valuation"]



			);*/



			if(isset($POST['ctype']) && $POST['ctype']=="upgradedeal")



			{



				$aryData["ptype"]=1;



				$aryData["underof"]=0;



				$ins=$db->insertAry(POSTS,$aryData);



				$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$POST["title"])));



				$postlink=URL_ROOT."product/".$ins."-".$pagename.".html";



				$sms="Dear ".$POST["fullname"].", Your post have been successfully submitted. We are reviewing your ad and will active it soon.";



				$notice="New Post from ".$_SESSION["user"]["fullname"];



				$Stat=0;$img=explode(",",$POST['image']);



				$imgUrl=URL_ROOT."media/ads/".$img;



				mysms($POST["mobile"],$sms,$Stat);// sms to user



				notification($notice,"ads",1,$_SESSION["user"]["uid"],$added_by['added_by'],$postlink); // notification to admin



				$aryEmail=array("[NAME]"=>$POST["fullname"],



								"[TITLE]"=>$POST["title"],



								"[DETAIL]"=>$POST["smalldes"],



								"[STATUS]"=>"Inactive",



								"[IMAGES]"=>$imgUrl,



								"[DEALTYPE]"=>$dtype,



								"[POSTLINK]"=>$postlink);



				mymail($LinksDetails['mail_sender_email'],$POST["email"],$subject,$body,"UPGRADEPOSTAD",$aryEmail);



			}



			



			



			elseif(isset($POST['pid']) && $POST['pid']!="")



			{



				$ins=$db->updateAry(POSTS,$aryData," where id='".$POST['pid']."'");



				$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$POST["title"])));



				$postlink=URL_ROOT."product/".$ins."-".$pagename.".html";



				$sms="Dear ".$POST["fullname"].", Your post have been updated successfully. We are reviewing your ad and will active it soon.";



				$notice="New Post from ".$_SESSION["user"]["fullname"];



				$Stat=0;$img=explode(",",$POST['image']);



				$imgUrl=URL_ROOT."media/ads/".$img;



				mysms($POST["mobile"],$sms,$Stat);// sms to user



				notification($notice,"ads",1,$_SESSION["user"]["uid"],$added_by['added_by'],$postlink); // notification to admin



				$aryEmail=array("[NAME]"=>$POST["fullname"],



								"[TITLE]"=>$POST["title"],



								"[DETAIL]"=>$POST["smalldes"],



								"[STATUS]"=>"Inactive",



								"[IMAGES]"=>$imgUrl,



								"[DEALTYPE]"=>$dtype,



								"[POSTLINK]"=>$postlink);



				mymail($LinksDetails['mail_sender_email'],$POST["email"],$subject,$body,"EDITPOSTAD",$aryEmail);



			}



			else{



				if(!isset($_SESSION['user']['uid']))



				{



					$p=randomFix(6);



					



					$username=preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(" ","",$POST["fullname"]));



					$insData=$db->insertAry(SITE_USER,array("fullname"=>$POST["fullname"],"mobile"=>$POST["mobile"],"username"=>$username."_".$p,"email"=>$POST["email"],"pass"=>$p,"role"=>7));	



					$body="";



					$otp=rand(100000,999999);



					$sms="Dear ".$POST["fullname"]." you have successfully registered with ".$LinksDetails["site_name"].". Your Login Detail are: ID:".$POST["mobile"]." and Pass: ".$p;



					$url=URL_ROOT."login.html";



					$code=base64_encode($otp."-".$POST['email']);



					$verifyurl=URL_ROOT."verification.php?code=".$code;



					$notice="New user :".$POST["fullname"].": registered with ".$LinksDetails["site_name"];



					$aryEmail=array("[NAME]"=>$POST["fullname"],



									"[SITENAME]"=>$LinksDetails["site_name"],



									"[LOGIN]"=>$POST["mobile"],



									"[PASSWORD]"=>$p,



									"[LINK]"=>$url,



									"[VERIFYLINK]"=>$verifyurl);



					mymail($LinksDetails['mail_sender_email'],$POST["email"],$subject,$body,"REGISTRATION",$aryEmail);



					mysms($POST["mobile"],$sms,0); //sms to user



					notification($notice,"newuser",1,$_SESSION["user"]["uid"],$added_by['added_by'],$verifyurl); //notification to admin fro new user



					$aryData["added_by"]=$insData;



				}



				



				$ins=$db->insertAry(POSTS,$aryData);



				$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$POST["title"])));



				$postlink=URL_ROOT."product/".$ins."-".$pagename.".html";



				$sms="Dear ".$POST["fullname"].", Your post have been successfully submitted. We are reviewing your ad and will active it soon.";



				$notice="New Post from ".$_SESSION["user"]["fullname"];



				$Stat=0;$img=explode(",",$POST['image']);



				$imgUrl=URL_ROOT."media/ads/".$img;



				mysms($POST["mobile"],$sms,$Stat);// sms to user



				notification($notice,"ads",1,$_SESSION["user"]["uid"],$added_by['added_by'],$postlink); // notification to admin



				$aryEmail=array("[NAME]"=>$POST["fullname"],



								"[TITLE]"=>$POST["title"],



								"[DETAIL]"=>$POST["smalldes"],



								"[STATUS]"=>"Inactive",



								"[IMAGES]"=>$imgUrl,



								"[DEALTYPE]"=>$dtype,



								"[POSTLINK]"=>$postlink);



				mymail($LinksDetails['mail_sender_email'],$_SESSION["user"]["email"],$subject,$body,"POSTAD",$aryEmail);



				



			}



			}



			



			



			}else



		



		if(!isset($POST["category"]) || trim($POST["category"])==""){



			$status=array("status"=>"error","msg"=>"Please Select category");



		}



		elseif(!isset($POST["dtype"]) || trim($POST["dtype"])==""){



			$status=array("status"=>"error","msg"=>"Please Select Type");



		}



		elseif(!isset($POST["smalldes"]) || trim($POST["smalldes"])==""){



			$status=array("status"=>"error","msg"=>"Please Enter Description");



		}



		



		else{



			$aryData=array(



						"category"=>$POST["category"],



						"title"=>$POST["title"],



						"dtype"=>$POST["dtype"],



						"smalldes"=>$POST["smalldes"],



						"streetname"=>$POST["streetname"],



						"smalldes"=>$POST["smalldes"],



						"added_by"=>$_SESSION['user']['uid'],



						"country"=>$POST['country'],



						"state"=>$POST['state'],



						"city"=>$POST['city'],



						"status"=>2,



						"subcat"=>$POST['subcatnames']



						);



						



						



			for($i=0;$i<count($FILES['file']['name']);$i++)



			{



				



				



				//$imgname=implode(",",$FILES['uploadFile']['name']);



				



				$lfilename = basename($FILES['file']['name'][$i]);



				



				$lext = strtolower(substr($lfilename, strrpos($lfilename, '.')+1));



				



				if(in_array($lext,array('jpeg','jpg','gif','png')))



				{



					$lnewfile=md5(microtime()).".".$lext;



					



					if(move_uploaded_file($FILES['file']['tmp_name'][$i],PATH_UPLOAD."media".DS.$lnewfile))



					{



						$imgfilename[]=$lnewfile;



					



					}				}



	}



	



			$aryData['image']=implode(",",$imgfilename);



			$ins=$db->insertAry(POSTS,$aryData);



			



			if(!is_null($ins)){



				$status=array("status"=>"success","msg"=>"Your Ad is submitted and  will be active in 2 hours","type"=>"url","url"=>URL_ROOT."dashboard.html");



			}else{



				$status=array("status"=>"error","msg"=>$db->getErMsg());



			}



		}



		return $status;



	}



	



	function make_a_deal($POST){



		global $db; global $LinksDetails;$error=false;$inserAryValue=0;

		$islogin=1;

		if(!isset($_SESSION['user']['uid'])){

			$islogin=0;

			 



			$chExist=$db->getVal("select id from ".SITE_USER." where mobile='".$POST["mobile"]."'");



			if(trim($POST["fullname"])==""){



				$status=array("status"=>"error","msg"=>"Please Enter Full Name");$error=true;



			}



			elseif(trim($POST["mobile"])==""){



				$status=array("status"=>"error","msg"=>"Please Enter Mobile");$error=true;



			}elseif(!is_numeric($POST["mobile"])){



				$status=array("status"=>"error","msg"=>"Please Enter Mobile");$error=true;



			}elseif($chExist!=""){



				$status=array("status"=>"error","msg"=>"Mobile Number ALready Exist. Please login and Re-submit the Ad.");$error=true;



			}



		}


		if(!isset($POST["dtype"]) || trim($POST["dtype"])==""){



			$status=array("status"=>"error","msg"=>"Please Select category");$error=true;



		}elseif(!isset($POST["dtype"]) || trim($POST["dtype"])==""){



			$status=array("status"=>"error","msg"=>"Please Select Type");$error=true;



		}
		elseif(!isset($POST["title"]) || trim($POST["title"])==""){



			$status=array("status"=>"error","msg"=>"Please Enter Title");$error=true;



		}
		elseif(!isset($POST["smalldes"]) || trim($POST["smalldes"])==""){



			$status=array("status"=>"error","msg"=>"Please Enter Description");$error=true;



		}



		if($error==false){



			$added_by=$_SESSION['user']['uid'];



			$underof=0;



			



			if(isset($POST["underof"]) && $POST["underof"]!="")$underof=$POST["underof"];



			if($POST["dtype"]=="1")$dtype="Only Cash";



			elseif($POST["dtype"]=="2")$dtype="Exchange";



			elseif($POST["dtype"]=="3")$dtype="Exchange And Cash";



			elseif($POST["dtype"]=="4")$dtype="For Sale";



			elseif($POST["dtype"]=="5")$dtype="Buy Now";



			$aryData=array( "category"=>$POST["category"],



							"subcat"=>$POST["subcatnames"],



							"title"=>$POST["title"],



							"dtype"=>$POST["dtype"],



							"ptype"=>1,



							"smalldes"=>$POST["smalldes"],



							"streetname"=>$POST["streetname"],



							"smalldes"=>$POST["smalldes"],



							"added_by"=>$added_by,



							"country"=>$POST['country'],



							"underof"=>$underof,



							"state"=>$POST['statenames'],



							"city"=>$POST['citynames'],



							"locality"=>$POST['locality'],



							"zip"=>$POST['zip'],



							"status"=>0,



							"price"=>$POST['price'],



							"image"=>$POST['image'],



							"iteration" => $POST["iteration"],



							"p_age"		=> $p_age,



							"p_condition"	=> $POST["p_condition"],



		

			);



			if(isset($POST['ctype']) && $POST['ctype']=="upgradedeal")



			{



				$aryData["ptype"]=1;



				$aryData["underof"]=0;



			$alertList=$db->getRows("select * from ".CUSTOM_REQUEST." where brandcatEx=".$POST['brandcat']."");



			//echo "<pre>";print_r($alertList);exit;



				$ins=$db->insertAry(POSTS,$aryData);



				$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$POST["title"])));



				$postlink=URL_ROOT."product/".$ins."-".$pagename.".html";



				$sms="Dear ".$POST["fullname"].", Your post have been successfully submitted. We are reviewing your ad and will active it soon.";



				$notice="New Post from ".$_SESSION["user"]["fullname"];



				$Stat=0;$img=explode(",",$POST['image']);



				$imgUrl=URL_ROOT."media/ads/".$img;



				mysms($POST["mobile"],$sms,$Stat);// sms to user



				notification($notice,"ads",1,$_SESSION["user"]["uid"],$added_by['added_by'],$postlink); // notification to admin



				$aryEmail=array("[NAME]"=>$POST["fullname"],



								"[TITLE]"=>$POST["title"],



								"[DETAIL]"=>$POST["smalldes"],



								"[STATUS]"=>"Inactive",



								"[IMAGES]"=>$imgUrl,



								"[DEALTYPE]"=>$dtype,



								"[POSTLINK]"=>$postlink);



				mymail($LinksDetails['mail_sender_email'],$POST["email"],$subject,$body,"UPGRADEPOSTAD",$aryEmail);



			}



			elseif(isset($POST['pid']) && $POST['pid']!="")



			{



				$ins=$db->updateAry(POSTS,$aryData," where id='".$POST['pid']."'");



				$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$POST["title"])));



				$postlink=URL_ROOT."product/".$ins."-".$pagename.".html";



				$sms="Dear ".$POST["fullname"].", Your post have been updated successfully. We are reviewing your ad and will active it soon.";



				$notice="New Post from ".$_SESSION["user"]["fullname"];



				$Stat=0;$img=explode(",",$POST['image']);



				$imgUrl=URL_ROOT."media/ads/".$img;



				mysms($POST["mobile"],$sms,$Stat);// sms to user



				notification($notice,"ads",1,$_SESSION["user"]["uid"],$added_by['added_by'],$postlink); // notification to admin



				$aryEmail=array("[NAME]"=>$POST["fullname"],



								"[TITLE]"=>$POST["title"],



								"[DETAIL]"=>$POST["smalldes"],



								"[STATUS]"=>"Inactive",



								"[IMAGES]"=>$imgUrl,



								"[DEALTYPE]"=>$dtype,



								"[POSTLINK]"=>$postlink);



				mymail($LinksDetails['mail_sender_email'],$POST["email"],$subject,$body,"EDITPOSTAD",$aryEmail);



			}



			else{



				if(!isset($_SESSION['user']['uid']))



				{



					$p=randomFix(6);



					$username=preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(" ","",$POST["fullname"]));



					$insData=$db->insertAry(SITE_USER,array("fullname"=>$POST["fullname"],"mobile"=>$POST["mobile"],"username"=>$username."_".$p,"email"=>$POST["email"],"pass"=>$p,"role"=>7));	



					$_SESSION["user"]["uid"]=$insData;



					$_SESSION["user"]["uname"]=$username;



					$_SESSION["user"]["fullname"]=$POST["fullname"];



					$_SESSION["user"]["email"]=$POST["email"];



					$ua=getBrowser();



					$yourbrowser= $ua['name'] . " " . $ua['version'] ;



					$historyData=array('login_ip'=>$_SERVER['REMOTE_ADDR'],



									  'login_browser' =>$yourbrowser,



									  'userid' =>$insData,



									  'uname' => $username,



									  'email' =>$POST["email"],



									  'ldate' => date("Y-m-d H:i:s"));



					$db->updateAry(SITE_USER,array("is_online"=>1)," where id='".$_SESSION["user"]["uid"]."'");



					$db->insertAry(LOGIN_HISTORY,$historyData);



					$Stat=0;



					$defaultPlan=$db->getVal("select id from ".MEMBERSHIPS." where is_default=1 and status=1");



					$pac=$this->savePackage($defaultPlan,$insData);



					



					$body="";



					$otp=rand(100000,999999);



					$sms="Dear ".$POST["fullname"]." you have successfully registered with ".$LinksDetails["site_name"].". Your Login Detail are: ID:".$POST["mobile"]." and Pass: ".$p;



					$url=URL_ROOT."login.html";



					$code=base64_encode($otp."-".$POST['email']);



					$verifyurl=URL_ROOT."verification.php?code=".$code;



					$notice="New user :".$POST["fullname"].": registered with ".$LinksDetails["site_name"];



					$aryEmail=array("[NAME]"=>$POST["fullname"],



									"[SITENAME]"=>$LinksDetails["site_name"],



									"[LOGIN]"=>$POST["mobile"],



									"[PASSWORD]"=>$p,



									"[LINK]"=>$url,



									"[VERIFYLINK]"=>$verifyurl);



					mymail($LinksDetails['mail_sender_email'],$POST["email"],$subject,$body,"REGISTRATION",$aryEmail);



					mysms($POST["mobile"],$sms,0); //sms to user



					notification($notice,"newuser",1,$_SESSION["user"]["uid"],$added_by['added_by'],$verifyurl); //notification to admin fro new user



					$aryData["added_by"]=$insData;



				}



				$mem=$db->getVal("select ads from ".USERMEMBERSHIP." where uid=".$_SESSION["user"]['uid']." and status=1");	



				if($mem>0){



					if(is_array($POST["category"]) && count($POST)>0){



						$aryData=array( "title"=>$POST["title"],



								"dtype"=>$POST["dtype"],



								"ptype"=>1,



								"streetname"=>$POST["streetname"],



								"added_by"=>$added_by,



								"country"=>$POST['country'],



								"underof"=>$underof,



								"state"=>$POST['statenames'],



								"smalldes"=>$POST['smalldes'],



								"city"=>$POST['citynames'],



								"locality"=>$POST['locality'],



								"zip"=>$POST['zip'],



								"status"=>0,



								"price"=>$POST['price'],



								"image"=>$POST['image']



						);



						for($k=0; $k<count($POST["category"]); $k++){



							//echo "hello";exit;



							$YearOn = date('Y', strtotime('-'.$POST['pyear'][$k].' years'));



							//date('Y',strtotime(date("Y-m-d", time()) . " - ".($POST['pyear']*365)." day"));



							$p_age=$YearOn."/".$POST['pmonth'][$k]."/1";



							$fina=$aryData;



							$fina["is_parent"]=$inserAryValue;



							$fina["category"]=$POST["category"][$k];



							$fina["subcat"]=$POST["subcat"][$k];



							$fina["productcat"]=$POST["productcat"][$k];



							$fina["brandcat"]=$POST["brandcat"][$k];



							$fina["modelcat"]=$POST["modelcat"][$k];



							$fina["iteration"]=$POST["iteration"][$k];



							$fina["p_age"]=$p_age;



						

							$fina["p_condition"]=$POST["p_condition"][$k];



							$ins=$db->insertAry(POSTS,$fina);



							//criteria_name//cat_id//criteria_value



							$criteria_i=$POST["criteria_i"][$k];



							if(is_array($POST["criteria_id_".$criteria_i]) && count($POST["criteria_id_".$criteria_i])>0){



								for($l=0;$l<count($POST["criteria_id_".$criteria_i]);$l++){



									$criteriaAry=array("post_id"=>$ins,



														"criteria_id"=>$POST["criteria_id_".$criteria_i][$l],



														"value"=>$POST["criteria_value_".$criteria_i][$l]);



									$db->insertAry(POST_CATEGORY_CRITERIA,$criteriaAry);		



								}



							}



							if($inserAryValue==0)$inserAryValue=$ins;



						}



					}



					$db->query("update ".USERMEMBERSHIP." set ads=ads-1 where uid=".$_SESSION["user"]['uid']." and status=1");



					$alertList=$db->getRows("select * from ".CUSTOM_REQUEST." where brandcatEx='".$POST['brandcat'][$k]."' or subcatEx='".$POST['subcat'][$k]."'");



					$postlink=URL_ROOT."product/".$inserAryValue."-".$pagename.".html";



					foreach($alertList as $List){



						$sms=$POST["fullname"]." Add New Ads ".$POST["title"];



						$Stat=0;



						$imgUrl=URL_ROOT."media/ads/".$img;



						mysms($List["mobile"],$sms,$Stat);// sms to user



						$subject="New Post On ".$LinksDetails['sitename']." cheack on site";



						//notification($sms,"ads",1,$added_by,'',$postlink); // notification to admin



						$aryEmail=array();



						mymail($LinksDetails['mail_sender_email'],$List['email'],$subject,$body,"COMMON",$aryEmail);



					}



					



					$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$POST["title"])));



					$sms="Dear ".$POST["fullname"].", Your post have been successfully submitted. We are reviewing your ad and will active it soon.";



					$notice="New Post from ".$_SESSION["user"]["fullname"];



					$Stat=0;$img=explode(",",$POST['image']);



					$imgUrl=URL_ROOT."media/ads/".$img;



					mysms($POST["mobile"],$sms,$Stat);// sms to user



					notification($notice,"ads",1,$_SESSION["user"]["uid"],$added_by['added_by'],$postlink); // notification to admin



					$aryEmail=array("[NAME]"=>$POST["fullname"],



									"[TITLE]"=>$POST["title"],



									"[DETAIL]"=>$POST["smalldes"],



									"[STATUS]"=>"Inactive",



									"[IMAGES]"=>$imgUrl,



									"[DEALTYPE]"=>$dtype,



									"[POSTLINK]"=>$postlink);



					mymail($LinksDetails['mail_sender_email'],$_SESSION["user"]["email"],$subject,$body,"POSTAD",$aryEmail);



				}else{



					return array("status"=>"error","msg"=>"Please upgrade your membership to Post your ads");



				}



			}



			if(!is_null($ins))



			{

				if($islogin==1){



				$status=array("status"=>"success","msg"=>"Your Ad is Submitted","type"=>"url","url"=>URL_ROOT."success.html");

				}else{

					$srta='7';

					$otp=randomFix(6);

					$sms="your otp for fixndeal is $otp";

					mysms($POST['mobile'],$sms,$srta);

					$db->updateAry(SITE_USER,$aryData," where added_by='".$_SESSION["user"]["uid"]."' and id=".$POST['product_id']."");

					

					$status=array("status"=>"success","msg"=>"Your Ad is Submitted","type"=>"url","url"=>URL_ROOT."otpverify.html");		

							

					

					}



			}else{



				$status=array("status"=>"error","msg"=>$db->getErMsg());



			}



		}



		return $status;



	}



	function makeOfferADeal($POST){



		



		global $db; global $LinksDetails;



		if(!isset($POST["sdesc"]) || trim($POST["sdesc"])==""){



			$status=array("status"=>"error","msg"=>"Please Enter Description");



		}



		



		else{



			$aryData=array(



						"smalldes"=>$POST["sdesc"],



						"dtype"=>$POST['dtype'],



						"status"=>2,



						"ptype"=>'1',



						"underof"=>'0'



						



						



						);



						



			$ins=$db->updateAry(POSTS,$aryData," where added_by='".$_SESSION["user"]["uid"]."' and id=".$POST['product_id']."");



			



			if(!is_null($ins)){



				$status=array("status"=>"success","msg"=>"Your Offer is submitted As deal and will be active in 2 hours","type"=>"url","url"=>URL_ROOT."offers.html");



			}else{



				$status=array("status"=>"error","msg"=>$db->getErMsg());



			}



		}



		return $status;



	}



	function CreateAlert($POST){



		global $db; global $LinksDetails;



		if(!isset($POST["category"]) || trim($POST["category"])==""){



			$status=array("status"=>"error","msg"=>"Please Select category");



			



		}



		elseif(!isset($POST["subcat"]) || trim($POST["subcat"])==""){



			$status=array("status"=>"error","msg"=>"Please Select sub category");



		}



		elseif(!isset($POST["dtype"]) || trim($POST["dtype"])==""){



			$status=array("status"=>"error","msg"=>"Please Select Type");



		}



		elseif(!isset($POST["mobile"]) || trim($POST["mobile"])==""){



			$status=array("status"=>"error","msg"=>"Please Enter mobile");



		}



				elseif(!isset($POST["email"]) || trim($POST["email"])==""){



			$status=array("status"=>"error","msg"=>"Please Enter email");



		}



		



		



		else{



			$aryData=array(



						"category"=>$POST["category"],



						"subcat"=>$POST["subcat"],



						"dtype"=>$POST["dtype"],



						"price"=>$POST["price"],



						"locality"=>$POST["locality"],



						"email"=>$POST["email"],



						"mobile"=>$POST["mobile"],



						"created_by"=>$_SESSION['user']['uid'],



						



						"status"=>2



						);



			$ins=$db->insertAry(ALERT,$aryData);



			



			if($ins!=""){



           $status=array("status"=>"success","msg"=>"Your Ad is submitted and  will be active in 2 hours","url"=>URL_ROOT."dashboard.html");



			}else{



				$status=array("status"=>"error","msg"=>$db->getErMsg());



			}



		}



		return $status;



	



		



		



		



		}	



	function getPosts($id){



		global $db;



		$conDetail=array();



		$contentDetail = $db->getRows("select * from ".POSTS." where  added_by='".$id."' and ptype='1' and underof='0' and is_parent='1'");



		if(is_array($contentDetail) && count($contentDetail)>0){$i=0;



			foreach($contentDetail as $pd){$i++;



				$ProDetail[$i]=$pd;



				$ProDetail[$i]["i"]=$i;



				$ProDetail[$i]["url"]=href("view_page.php","product_id=".$pd["id"]);



				//$ProDetail[$i]["url"]==href("view_page.php","product_id=".$pd["id"]);



				$image=explode(",",$pd['image']);



				$ProDetail[$i]['images']=$image;



				$ProDetail[$i]['offers']=$this->getOffers($pd["id"]);



			}



		}



		return $ProDetail;



		



		



		}



	function editPosts($id){



		global $db;



		$conDetail=array();



		$contentDetail = $db->getRow("select * from ".POSTS." where  id='".$id."' and added_by='".$_SESSION['user']['uid']."'");



		if(is_array($contentDetail) && count($contentDetail)>0){$i=0;



			$conDetail=$contentDetail;



			$image=explode(",",$contentDetail['image']);



			$conDetail['images']=$image;



		}



		return $conDetail;



	}



	function getMyOffersPost($id){



		global $db;



		$conDetail=array();



		$contentDetail = $db->getRows("select * from ".POSTS." where  added_by='".$id."' and ptype='2' ");



		if(is_array($contentDetail) && count($contentDetail)>0){$i=0;



			foreach($contentDetail as $pd){$i++;



				$ProDetail[$i]=$pd;



				$ProDetail[$i]["i"]=$i;



				$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$pd['title'])));



				$ProDetail["url"]=URL_ROOT."product/".$pd["id"]."-".$pagename.".html";



				$ProDetail[$i]["url"]=href("view_page.php","product_id=".$pd["id"]);



				$image=explode(",",$pd['image']);



				$ProDetail[$i]['images']=$image;



			}



		}



		return $ProDetail;



	}



	function getSingleDetailList($id){



		global $db;$ProDetail=$ProductList=array();



		global $LinksDetails;



		$ProductList = $db->getRow("select p.*,u.username,u.fullname,u.email,u.mobile,cat.name as category_name,subcat.name as subcategory_name,cat.id as category_id,subcat.id as subcategory_id from ".POSTS." as p,".SITE_USER." as u,".CATEGORY." as cat,".CATEGORY." as subcat  where p.id=$id and u.id=p.added_by and p.category=cat.id and p.subcat=subcat.id");



		$ProDetail=$ProductList;



		$ProDetail["status"]="success";		 



		//echo "<pre>";print_r($ProductDetail);echo $db->getLastQuery()."//".$db->getErMsg();exit;



		$image=explode(",",$ProductDetail['image']);



		//$ProDetail=$ProductDetail;//echo ($ProductDetail["new_price"]);exit;



		$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$ProductList['title'])));



		$ProDetail["url"]=URL_ROOT."product/".$id."-".$pagename.".html";



		$ProDetail["images"]=$image;



		//echo "<pre>";print_r($ProDetail);



		return $ProDetail;



	



		



	}



	function getProducDetail($id){



		global $db;$ProDetail=$ProductList=array();



		global $LinksDetails;



		/*$ProductList = $db->getRow("



		SELECT p.*,u.udate as joinDate,u.username,u.fullname,u.email,u.mobile,u.avatar,u.mob_verify,cat.name as category_name,subcat.name as subcategory_name,cat.id as category_id,subcat.id as subcategory_id 



		FROM ".POSTS." as p,".SITE_USER." as u,".CATEGORY." as cat,".CATEGORY." as subcat 



		WHERE p.id=$id and u.id=p.added_by and p.category=cat.id and p.subcat=subcat.id");*/



		$ProductList = $db->getRow("

		SELECT p.*,u.udate as joinDate,u.username,u.show_photo,u.show_number,u.id as userid,u.fullname,u.email,u.mobile,u.avatar,u.is_online,u.mob_verify



		FROM ".POSTS." as p,".SITE_USER." as u 



		WHERE p.id=$id and u.id=p.added_by");

			$ProDetail["mquery"]=$db->getLastQuery();;

			$ProDetail["detail"]=$ProductList;



			$ProDetail["detail"]["category_name"]=$db->getVal("select name from ".CATEGORY." where id=".$ProductList["category"]);



			//$objdeal->getAvgRating($ProductList['id']);



			$ProDetail["detail"]["rat"]=$this->getAvgRating($ProductList['userid']);;



			$ProDetail["detail"]["category_id"]=$ProductList["category"];



			$ProDetail["detail"]["subcategory_name"]=$db->getVal("select name from ".CATEGORY." where id=".$ProductList["subcat"]);



			$ProDetail["detail"]["subcategory_id"]=$ProductList["subcat"];



			$ProDetail["detail"]["query"]=$db->getLastQuery()."<br />".$db->getErMsg();



			$ProDetail["detail"]["lastlogin"]=$db->getVal("select ldate from ".LOGIN_HISTORY." where userid='".$ProductList["added_by"]."' order by ldate DESC limit 0,1");



			$ProDetail["status"]="success";		 



			$image=explode(",",$ProductList['image']);



			$age=explode("|",$ProductList["p_age"]);



			$ProDetail["detail"]["checkOfferPosted"]=$db->getVal("select id from ".POSTS." where underof='".$ProductList['id']."' and added_by='".$_SESSION['user']['uid']."'");



			$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$ProductList['title'])));



			$ProDetail["detail"]["url"]=URL_ROOT."product/".$id."-".$pagename.".html";



			$ProDetail["detail"]["images"]=$image;



			$ProDetail["detail"]["yr"]=$age[0];



			$ProDetail["detail"]["mn"]=$age[1];



			$ProDetail["detail"]["offers"]=$this->getOffers($ProductList["id"]);



			if($ProductList["dtype"]=="1")$ProDetail["detail"]["dealtype"]="Only Cash";



			elseif($ProductList["dtype"]=="2")$ProDetail["detail"]["dealtype"]="Exchange";



			elseif($ProductList["dtype"]=="3")$ProDetail["detail"]["dealtype"]="Exchange And Cash";



			elseif($ProductList["dtype"]=="4")$ProDetail["detail"]["dealtype"]="For Sale";



			elseif($ProductList["dtype"]=="5")$ProDetail["detail"]["dealtype"]="Buy Now";



			elseif($ProductList["dtype"]=="6")$ProDetail["detail"]["dealtype"]="Bulk Sell";



			



			if($ProductList["p_condition"]=="1")$ProDetail["detail"]["p_condition"]="Working";



			elseif($ProductList["p_condition"]=="2")$ProDetail["detail"]["p_condition"]="Not Working";



			elseif($ProductList["p_condition"]=="3")$ProDetail["detail"]["p_condition"]="Damaged";



			



			if($ProductList["iteration"]=="1")$ProDetail["detail"]["iteration"]="First Hand";



			elseif($ProductList["iteration"]=="2")$ProDetail["detail"]["iteration"]="Second Hand";



			elseif($ProductList["iteration"]=="3")$ProDetail["detail"]["iteration"]="Third Hand";



			elseif($ProductList["iteration"]=="3")$ProDetail["detail"]["iteration"]="More";



			

			return $ProDetail;



	}	



    function Trendingads(){



		global $db;$ProDetail=array();global $LinksDetails;



		$ProductDetail = $db->getRows("select * from ".POSTS." where status=1 and ptype=1 and is_parent='0' ORDER BY total_view DESC LIMIT 20 ");



		if(is_array($ProductDetail) && count($ProductDetail)>0){$i=0;



			foreach($ProductDetail as $pd){$i++;



				$ProDetail[$i]=$pd;



				$ProDetail[$i]["i"]=$i;



				$image=explode(",",$pd['image']);



				$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$pd['title'])));



				$ProDetail[$i]["url"]=URL_ROOT."product/".$pd["id"]."-".$pagename.".html";



				$ProDetail[$i]['images']=$image;



				if($pd["dtype"]=="1")$ProDetail[$i]["dtype"]="Only Cash";



				elseif($pd["dtype"]=="2")$ProDetail[$i]["dtype"]="Exchange";



				elseif($pd["dtype"]=="3")$ProDetail[$i]["dtype"]="Exchange And Cash";



				elseif($pd["dtype"]=="4")$ProDetail[$i]["dtype"]="For Sale";



				elseif($pd["dtype"]=="5")$ProDetail[$i]["dtype"]="Buy Now";



				elseif($pd["dtype"]=="6")$ProDetail[$i]["dtype"]="Other";



			}



		}



		 return $ProDetail;



	}



	function MostPopular(){



		 global $db;$ProDetail=array();global $LinksDetails;



		 $ProductDetail = $db->getRows("select * from ".POSTS." where status=1 and ptype=1 and is_parent='0' ORDER BY udate DESC LIMIT 20 ");



		if(is_array($ProductDetail) && count($ProductDetail)>0){$i=0;



			foreach($ProductDetail as $pd){$i++;



				$ProDetail[$i]=$pd;



				$ProDetail[$i]["i"]=$i;



				$image=explode(",",$pd['image']);



				$pagename=str_replace(" ","-",str_replace("/","~",str_replace("&","and",$pd['title'])));



				$ProDetail[$i]["url"]=URL_ROOT."product/".$pd["id"]."-".$pagename.".html";



				$ProDetail[$i]['images']=$image;



				if($pd["dtype"]=="1")$ProDetail[$i]["dtype"]="Only Cash";



				elseif($pd["dtype"]=="2")$ProDetail[$i]["dtype"]="Exchange";



				elseif($pd["dtype"]=="3")$ProDetail[$i]["dtype"]="Exchange And Cash";



				elseif($pd["dtype"]=="4")$ProDetail[$i]["dtype"]="For Sale";



				elseif($pd["dtype"]=="5")$ProDetail[$i]["dtype"]="Buy Now";



				elseif($pd["dtype"]=="6")$ProDetail[$i]["dtype"]="Bulk Sell";



			}



		}



		return $ProDetail;



	}



	function getOTP($id){



		global $db;



		$userid=$id['id'];$otp=rand(100000,999999);



		if(is_numeric($userid)){



			$sms="OTP for FixNdeal is '$otp'. Please do ot share with any one.";



			$Stat=0;



			mysms($userid,$sms,$Stat);



			$db->updateAry(SITE_USER,array('otp'=>$otp,'mob_verify'=>0)," where mobile='".$userid."'");	



			return $status=array("status"=>"success","msg"=>"OTP for FixNdeal is sent to you number please check//".$db->getLastQuery(),"type"=>"div");



		}else{



			$db->updateAry(SITE_USER,array('otp'=>$otp,'mob_verfiy'=>0)," where email='".$userid."'");	



		}



		



	}



	function sendSms($sms,$to,$mfrom){







	global $db;	



	$aryData=array("mfrom"=>$mfrom,"mto"=>$to,"message"=>$sms);



	$flgn=$db->insertAry("deal_chatbk",$aryData);



		if(!is_null($flgn)){



			$status=array("status"=>"success","msg"=>"Sent Successfully","type"=>"url","url"=>"");



		}else{



			



			$status=array("status"=>"error","msg"=>"Error: ".$db->getErMsg());



		}



		



		return $status;



	



	



	}



	function custom_request($POST){



		global $db;



		$aryData=array("deal_type"		=>	$POST["dtype"],



					   "minprice"		=>	$POST["minprice"],



					   "maxprice"		=>	$POST["maxprice"],



					   "city"			=>	$POST["city"],



					   "locality"		=>	$POST["locality"],



					   "email"			=>	$POST["email"],



					   "mobile"			=>	$POST["mobile"],



					   "categoryEx"=>$POST["categoryAl"],



						"subcatEx"=>$POST["subcatAl"],



						"productcatEx"=>$POST["productcatAl"],



						"brandcatEx"=>$POST["brandcatAl"],



						"DescriptionEx"=>$POST["descriptionEx"],



		);



		$flgn=$db->insertAry(CUSTOM_REQUEST,$aryData);



		if(!is_null($flgn)){



			$body="";



			$sms="Dear ".$POST["email"]." we have received your requirement. We notify you once we have deal according to your requirement.";



			$aryEmail=array("[NAME]"	=>	$POST["email"]);



			$msg="we have received new requirement from user. Here is Detail:";



			mymail($LinksDetails['mail_sender_email'],$POST["email"],$subject,$body,"REQUIREMENT",$aryEmail);



			mysms($POST["mobile"],$sms,0); //sms to user



			$status=array("status"=>"success","msg"=>"Your Requirement Submit Successfully","type"=>"url","url"=>"");



		}else{



			$status=array("status"=>"error","msg"=>"Error: ".$db->getErMsg());



		}



		return $status;



		



	}



	function getAvgRating($pid) 



    { 



		$tot=0;



		global $db;



		$count=$db->getVal("select count(id) from ".REVIEWS." where reviewfor='".$pid."'");



		$tot=$db->getVal("select SUM(star) from ".REVIEWS." where reviewfor='".$pid."'");



		if($count>0){



			$new_count=$count*5;



			



			$new_count=$tot*5/$new_count;



			return $new_count;	



		}else{



			return 0;



		}



    }



}



	



	