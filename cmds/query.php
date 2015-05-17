<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$sql=stripslashes($_POST["sql"]);
$result=$con->executeQuery($sql);
if($con->getErrorNo()>0) {
	$msg="<span style='color:red;font-size:16px;'>";
	$msg.="[Error Code: ".$con->getErrorNo().", SQL State: 42S02]";
	$msg.=" <b style='color:maroon;'>".$con->getError()."</b>";
	$msg.="</span>";
	echo $msg;
} else {
	if($result) {
		echo printResultTable($result);
	}
}
?>
