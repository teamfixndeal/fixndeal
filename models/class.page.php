<?php

class page{

	function mymenu($type){

		global $db;

		$menuAry=array();

		$menu=$db->getRows("select linkname,heading,id from ".CMS." where underof=0 and $type=1 and status=1 and language='".$_SESSION["lang"]."' order by lorder");

		$menuAry["count"]=count($menu);

		if(is_array($menu) && count($menu)>0){$i=0;

			foreach($menu as $m){$i++;

				$menuAry["menu"][$i]=$m;

				$menuAry["menu"][$i]["url"]=href("page.php","page_id=".$m["id"]);

				$menuAry["menu"][$i]["detail"]=$this->pageDetail($m["linkname"]);

				$submenu=$db->getRows("select linkname,heading,id from ".CMS." where underof=".$m["id"]." and language='".$_SESSION["lang"]."' and status=1 order by lorder");

				$menuAry["menu"][$i]["count"]=count($submenu);

				if(is_array($submenu) && count($submenu)>0){$j=0;

					foreach($submenu as $sm){$j++;

						$menuAry["menu"][$i]["menu"][$j]=$sm;

						$menuAry["menu"][$i]["menu"][$j]["url"]=href("page.php","page_id=".$sm["id"]);

						$menuAry["menu"][$i]["menu"][$j]["detail"]=$this->pageDetail($sm["linkname"]);

						$subsubmenu=$db->getRows("select linkname,heading,id from ".CMS." where underof=".$sm["id"]." and language='".$_SESSION["lang"]."' and status=1 order by lorder");

						$menuAry["menu"][$i]["menu"][$j]["count"]=count($subsubmenu);

						if(is_array($subsubmenu) && count($subsubmenu)>0){$k=0;

							

							foreach($subsubmenu as $ssm){$k++;

								$menuAry["menu"][$i]["menu"][$j]["menu"][$k]=$ssm;

								$menuAry["menu"][$i]["menu"][$j]["menu"][$k]["url"]=href("page.php","page_id=".$ssm["id"]);

								$menuAry["menu"][$i]["menu"][$j]["detail"][$k]["url"]=$this->pageDetail($ssm["linkname"]);

							}

						}

					}

				}

			}

		}

		return $menuAry;

	}

	function pageDetail($pname){

		global $db;

		$pagename=str_replace("-"," ",str_replace("~","/",str_replace("and","&",$pname)));

		$detail=$db->getRow("SELECT * FROM ".CMS." WHERE linkname='".$pagename."' and language='".$_SESSION["lang"]."'");

		if(trim($detail["external"])!="")

		$detail["external"]="extra/".$detail["external"];

		$detail["query"]=$db->getLastQuery();

		$detail["details"]=unPOST($detail["pbody"]);
		
		
		$detail["pbody"]=trim(strip_tags(unPOST($detail["pbody"])));
		
		
		$detail["short_description"]=unPOST($detail["short_description"]);

		return $detail;

	}

	

	/*function pageDetail($pname){

		global $db;

		$pagename=str_replace("-"," ",str_replace("~","/",str_replace("and","&",$pname)));

		$detail=$db->getRow("SELECT * FROM ".CMS." WHERE linkname='".$pagename."'");

		

		$detail["query"]=$db->getLastQuery();

		$detail["pbody"]=unPOST($detail["pbody"]);

		$detail["short_description"]=unPOST($detail["short_description"]);



		return $detail;

	}*/

	

	

	function contentDetail($pid){

		global $db;

		$detail=$db->getRow("SELECT * FROM ".CONTENT." WHERE id='".$pid."' and language='".$_SESSION["lang"]."'");

		

		$detail["query"]=$db->getLastQuery();

		$detail["content"]=unPOST($detail["content"]);

		$detail["extra"]=json_encode($detail["extra"],true);

		return $detail;

	}

	

	

}





?>