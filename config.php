<?php 

ob_start();

@session_start();

ini_set('display_errors', 1); error_reporting(E_ALL);

error_reporting(0);

date_default_timezone_set('Asia/Kolkata');

$varAdminFolder="MsAdmin";

if($_SERVER["SERVER_NAME"]=="mssinfotech.co.uk" || $_SERVER["SERVER_NAME"]=="www.mssinfotech.co.uk"){

	$mysql_user = 'mssinfot_fixndea';//'ms2fun';

	$password = "Welcome@123";//"123@qwe";

	$database_host = "localhost";

	$database = "mssinfot_fixndeals";

	define("URL_ROOT","http://mssinfotech.co.uk/fixndeal/");

}elseif($_SERVER["SERVER_NAME"]=="fixndeal.com" || $_SERVER["SERVER_NAME"]=="www.fixndeal.com"){

	$mysql_user = 'fixnd2rg_mss';//'ms2fun';

	$password = "123@qwe";

	$database_host = "localhost";

	$database = "fixnd2rg_ms_fixndeal";

	define("URL_ROOT","http://www.fixndeal.com/");

}elseif($_SERVER["SERVER_NAME"]=="m.fixndeal.com" || $_SERVER["SERVER_NAME"]=="www.m.fixndeal.com"){

	$mysql_user = 'fixnd2rg_mss';//'ms2fun';

	$password = "123@qwe";

	$database_host = "localhost";

	$database = "fixnd2rg_ms_fixndeal";

	define("URL_ROOT","http://www.m.fixndeal.com/");

}else{

	$mysql_user = 'root';//'ms2fun';

	$password = '';//"123@qwe";

	$database_host = "localhost";

	$database = "fixndeal";

	define("URL_ROOT","http://".$_SERVER["HTTP_HOST"]."/fixndeal/");

}

$table_prefix="deal_";

//echo PATH_ROOT;exit;

define("DS",DIRECTORY_SEPARATOR);

define("PATH_ROOT",dirname(__FILE__));

define("PATH_LIB",PATH_ROOT.DS."function".DS);

define("TABLE_PREFIX",$table_prefix);

/*************language connect*************/

$lang = 'en';$city = '';

$_SESSION['lang']=$lang;

require_once(PATH_ROOT.DS."language".DS."en.php");

//require_once(PATH_ROOT.DS."language".DS.$lang.".php");

/*************language connect*************/

require_once(PATH_LIB."class.database.php");

$db=new MySqlDb($database_host,$mysql_user,$password,$database);

require_once(PATH_LIB.'mailer/PHPMailerAutoload.php');

require_once(PATH_LIB.'simplexlsx.class.php');

require_once(PATH_LIB."table.php");

require_once(PATH_LIB."functions.php");

$LinksDetails=fetchSetting();

define('PAGE_PER_NO',6); 

//define("URL_ROOT","http://".$_SERVER["SERVER_NAME"]."/");

define("URL_IMAGES",URL_ROOT."images/");

define("PATH_ADMIN",URL_ROOT.$varAdminFolder."/");

define("ADMIN_JS",PATH_ADMIN."js/");

define("ADMIN_CSS",PATH_ADMIN."css/");

define("URL_ADMIN_IMGAGES",URL_ROOT."assets/");

/*************mss connect*************/

define('API_KEY','mss123456789demo');

//echo @file_get_contents("http://mssinfotech.com/?action=".$_SERVER["HTTP_HOST"]);

/*************mss config for email and connect*************/

//*********** Constent for define image ****************//

define("IMG",URL_ROOT."function/timthumb.php?src=");

//************ Constent for define image *********//

define('MAIL_USERNAME',$LinksDetails["mail_Username"]);

define('MAIL_PASSWORD',$LinksDetails["mail_Password"]);

define('MAIL_SENDER_NAME',$LinksDetails["mail_sender_name"]);

define('MAIL_SENDER_EMAIL',$LinksDetails["mail_sender_email"]);

define('MAIL_SMTPSECURE',$LinksDetails["mail_SMTPSecure"]);

define('MAIL_HOST',$LinksDetails["mail_Host"]);

define('MAIL_PORT',$LinksDetails["mail_Port"]);

######### edit details facebook ##########

//Call Facebook API



include_once(PATH_LIB."facebook".DS."facebook.php"); //include facebook SDK

$appId = '1126657240718875'; //Facebook App ID

$appSecret = 'b1b70ce8edb4d15c5b5a63c858f9c0e6'; // Facebook App Secret

$return_url = URL_ROOT."callback.php";  //return url (url to script)

$homeurl = URL_ROOT;  //return to home

$fbPermissions = 'email, user_birthday, user_location, user_about_me, user_hometown, public_profile, user_friends'; 

$facebook = new Facebook(array(

  'appId'  => $appId,

  'secret' => $appSecret,

  'cookie' => true,

  'domain' => URL_ROOT

));

$fbuser = $facebook->getUser();

if ($fbuser) {

	$user_profile = $facebook->api('/me');

}else{

	$loginUrl = $facebook->getLoginUrl(array( 'scope' => $fbPermissions ));

}

############## edit detail for google  ############################

include_once(PATH_LIB."google".DS."src".DS."Google_Client.php");

include_once(PATH_LIB."google".DS."src".DS."contrib".DS."Google_Oauth2Service.php");

$client = new Google_Client();

$client->setApplicationName("Google UserInfo PHP Starter Application");

$oauth2 = new Google_Oauth2Service($client);

if ($client->getAccessToken()) {

  $user = $oauth2->userinfo->get();

  $content = $user;

  $_SESSION['token'] = $client->getAccessToken();

} else {

  $authUrl = $client->createAuthUrl();

}

############## edit detail for Linkedin  ############################

   

define("GOOGLE_API_KEY", "AIzaSyDuBVNokeRcK0kBJtp_knHXpiozWysHU78"); 

// Place your Google API Key   

############## edit detail for Linkedin  ############################

require PATH_LIB.DS.'Smarty.class.php';//echo "yes";

/****************************************Smarty Config*******************************/////////

$smarty = new Smarty;

//$smarty->force_compile = true;

$smarty->debugging = false;

$smarty->caching = false;

$smarty->cache_lifetime = 1;

$theme="default";

if($_SERVER["SERVER_NAME"]=="m.fixndeal.com" || $_SERVER["SERVER_NAME"]=="www.m.fixndeal.com"){

    $theme="mobile";// Do something for only mobile users

	if(isset($_GET["regId"]) && $_GET["regId"]!=""){

		$data=array("regId"=>$_GET["regId"],

					"mobileid"=>$_GET["mobileid"],

					"mobilename"=>$_GET["mobilename"]);

		$id=$db->getVal("select id from ".PUSH_NOTIFICATION." where regId='".$gcm_regid."'");

        if($id==""){

			$id = $db->insertAry(PUSH_NOTIFICATION,$data);

		}

		$time = time() + (24*3600*365) ;	

		setcookie( "regId", $_GET["regId"], $time );

	}

}
if(!isMobile() && ($_SERVER["SERVER_NAME"]=="m.fixndeal.com" || $_SERVER["SERVER_NAME"]=="www.m.fixndeal.com")){
	$urlMe=str_replace("m.fixndeal.com","fixndeal.com",curPageURL());
	//echo $urlMe."<br />".curPageURL();exit;
	redirect($urlMe);
}
if(isMobile() && ($_SERVER["SERVER_NAME"]=="fixndeal.com" || $_SERVER["SERVER_NAME"]=="www.fixndeal.com")){
	$url=str_replace("fixndeal.com","m.fixndeal.com",curPageURL());
	redirect($url);
}
$dirname = "templates/".$theme;

$smarty->template_dir=$dirname;

$smarty->assign("loginUrl",$loginUrl);   //for facebook

$smarty->assign("authUrl",$authUrl);     //for google

$smarty->assign("LinksDetails",$LinksDetails);

$smarty->assign("URL_ROOT",URL_ROOT.$dirname."/");

$smarty->assign("ROOT",URL_ROOT);

$smarty->assign("REQUEST", $_REQUEST);

$smarty->assign("SESSION", $_SESSION);

$smarty->assign("SERVER", $_SERVER);

$smarty->assign("COOKIE", $_COOKIE);

if(isset($_SESSION["msg"]))unset($_SESSION["msg"]);

if(isset($_SESSION["error"]))unset($_SESSION["error"]);

$pagename=strtolower(basename($_SERVER['SCRIPT_NAME']));

$smarty->assign("pagename",$pagename);

/****************************************Smarty Config*******************************/////////

define("URL_ADMIN",URL_ROOT.$varAdminFolder."/");

define("URL_ADMIN_HOME",URL_ADMIN."index.php");

define("URL_ADMIN_CSS",URL_ADMIN."css/");

define("URL_ADMIN_JS",URL_ADMIN."js/");

define("URL_ADMIN_IMG",URL_ADMIN."images/");

define("SELF",basename($_SERVER['PHP_SELF']));

define("PATH_UPLOAD",PATH_ROOT.DS."uploads".DS);

define("PATH_MEDIA",PATH_ROOT.DS."uploads".DS."media".DS);

define("PATH_UPLOAD_PHOTO",PATH_UPLOAD."images".DS);

addvisit();

//date_default_timezone_set($LinksDetails["timezone"]);

/****************************************Classes Config*******************************/////////



include_once(PATH_LIB . "Pagination.php");

$objPagination = new Pagination();

include_once(PATH_ROOT . "/models/class.deal.php");

$objdeal = new deal();

include_once(PATH_ROOT . "/models/class.page.php");

$objpage = new page();

include_once(PATH_ROOT . "/models/class.index.php");

$objindex = new index();

include_once(PATH_ROOT . "/models/class.extra.php");

$objextra = new extra();

include_once(PATH_ROOT . "/models/class.user.php");

$objuser = new user();

include_once(PATH_ROOT . "/models/class.login_signup.php");

$objlogin_signup = new login_signup();



/****************************************Classes Comman Config*******************************/////////

//if(isset($_SESSION["user"]["uid"]))$smarty->assign("Detail", $objuser->userdetail($_SESSION["user"]["uid"]));

if(isset($_SESSION["user"]["uid"])){

	$smarty->assign("balancedetail",getBalance($_SESSION['user']['uid']));

	$uDetail=$objuser->userdetail($_SESSION["user"]["uid"]);

	$smarty->assign("mpackage",mypackage());

	$mypackageDetail=mypackageDetail();

	$smarty->assign("mypackage",$mypackageDetail);

	$now = time(); // or your date as well

    $your_date = strtotime($mypackageDetail['ndate']);

	$end_date = strtotime($mypackageDetail['ndate']." +".$mypackageDetail['day']." days");

    $datediff = $end_date-$now ;

    $remainingdays=floor($datediff/(60*60*24));

	if($remainingdays<=0){

		//echo $remainingdays."";exit;

		$db->updateAry(USERMEMBERSHIP,array("status"=>"0")," where uid='".$_SESSION["user"]["uid"]."'");

	}

	$smarty->assign("remainingdays",$remainingdays);

	$smarty->assign("userdetail", $uDetail);

	$smarty->assign("uDetail",$uDetail);

	$smarty->assign("Notification",$objuser->fetchNotification());

	//echo "<pre>";print_r($mypackage);exit;

}

$catListA=$objindex->getCategory();

$smarty->assign("category",$catListA); 

$smarty->assign("menu_header",$objpage->mymenu("header"));

$fmenu=$objpage->mymenu("footer");

$smarty->assign("menu_footer",$fmenu);

$smarty->assign("menu_left",$objpage->mymenu("m_left"));

$filterAry=array();

if(isset($_GET["city"])){$city=$_GET["city"];$_SESSION["city"]=$city;$time = time() + (24*3600*365) ;	setcookie( "city", $city, $time );$smarty->assign("city",$city);}

if(isset($_SESSION["city"])){$city=$_SESSION["city"];$smarty->assign("city",$city);}

elseif(isset($_COOKIE['city'])){$city=$_COOKIE["city"];$_SESSION["city"]=$city;$smarty->assign("city",$city);$time = time() + (24*3600*365) ;	setcookie( "city", $city, $time );}

elseif(isset($_GET["latitude"]) && isset($_GET["longitude"]) && !isset($_GET["city"])){		

	$geocode_stats = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=".$_GET["latitude"].",".$_GET["longitude"]."&sensor=false");

	$result='';

	$output_deals = json_decode($geocode_stats); 

	//print_r($output_deals);

	if($output_deals->status=='OK'){

	  $address_components=$output_deals->results[0]->address_components;

		

	  //print_r($address_components);

	

	  for($i=0;$i<count($address_components);++$i){

		if(array("locality", "political")==$address_components[$i]->types){

		  $result=$address_components[$i]->short_name;

		  break;

		}

	  }

	

	}

	if($result!=""){

	$city=$result;

		$_SESSION["city"]=$city;

		$smarty->assign("city",$city);$time = time() + (24*3600*365) ;	

		setcookie( "city", $city, $time );

		//echo $city;exit;

	}}

else{$city=ip_info("Visitor", "City");$_SESSION["city"]=$city;$smarty->assign("city",$city);$time = time() + (24*3600*365) ;	setcookie( "city", $city, $time );}

$smarty->assign("recordPerPage",12);

?>