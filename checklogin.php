<?php
$lastPage="http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
if(!isset($_SESSION["user"]["uid"]))redirect(href('login.php','redirect='.$lastPage));
?>