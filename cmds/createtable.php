<?php
if (!defined('ROOT')) exit('No direct script access allowed');

$sql=$_POST["sql"];
$result=$con->executeQuery($sql);
if($con->getErrorNo()>0) {
	$msg="<span style='color:red;'>";
	$msg.="[Error Code: ".$con->getErrorNo().", SQL State: 42S02]";
	$msg.=" <b style='color:maroon;'>".$con->getError()."</b>";
	$msg.="</span><hr/><b>SQL CODE ::</b> <br/>";
	$msg.="<span style='color:#2B7A4B;'>";
	$msg.=$sql;
	$msg.="</span>";
	echo $msg;
} else {
	echo "<span style='color:#2B7A4B;font-size:1.5em;'>Successfully Created New Table.</span>";
}
?>
