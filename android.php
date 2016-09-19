<form method="post" action="ajax.php">
<?php 
foreach($_GET as $key=>$value){
	?><input name="<?php echo $key ?>" value="<?php echo $value ?>" type="text"><br><?php
}
?>
<input type="submit">
</form>