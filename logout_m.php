<?php
include_once("config.php");
$sql = "UPDATE " . SITE_USER . " SET regId='', is_online=0, userLastLogin='".date("Y-m-d H:m:s")."' where id='" . $_SESSION["user"]["uid"] . "'  ";
        $result = $db->query($sql);
session_destroy() ;
$fblogoutUrl;
unset($_SESSION["user"]);
unset($_SESSION['token']);
//$client->revokeToken();
redirect(URL_ROOT);
ob_flush();
?>