<?php 
include("config.php");
$data=$db->getRow("call getCMS(99)");
echo "<pre>";print_r($data);
?>