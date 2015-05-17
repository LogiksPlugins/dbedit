<?php
if (!defined('ROOT')) exit('No direct script access allowed');

if(isset($_REQUEST["action"]) && strlen($_REQUEST["action"])>0) $ops=$_REQUEST["action"];
else {
	printErr("WrongFormat","You Forgot The Operation Command.");
	exit();
}
if(!isset($_REQUEST["tbl"]) || strlen($_REQUEST["tbl"])<=0) {
	printErr("WrongFormat","Data Table For Operation Not Found.");
	exit();
}

$toolBtns=array(
		"deleteicon"=>"deleteRecord($(this).parents('tr'))",
		//"editicon"=>"editRecord($(this).parents('tr'))",
	);
if($_REQUEST["action"]=="viewdata") {
	$sql="SELECT * FROM {$_REQUEST['tbl']}";
	if(isset($_REQUEST["limit"])) $sql.=" LIMIT {$_REQUEST['limit']}";
	
	$result=_dbQuery($sql);
	if($result) {
		$s=printResultTable($result,$toolBtns);
		if(strlen($s)>0) {
			echo $s;
			exit();
		}
	}
	echo "<tr><td colspan=100><h3>No Records Found</h3></td></tr>";
} elseif($_REQUEST["action"]=="deleterow") {
	$sql="DELETE FROM {$_REQUEST['tbl']} WHERE {$_REQUEST['col']}={$_REQUEST['fld']}";
	_dbQuery($sql);
	
	$sql="SELECT * FROM {$_REQUEST['tbl']}";
	$result=_dbQuery($sql);
	if($result) {
		$s=printResultTable($result,$toolBtns);
		if(strlen($s)>0) {
			echo $s;
			exit();
		}
	}
	echo "<tr><td colspan=100><h3>No Records Found</h3></td></tr>";
}

exit();
?>
