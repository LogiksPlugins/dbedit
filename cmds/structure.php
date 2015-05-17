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
			"deleteicon"=>"deleteField($(this).parents('tr'))",
			//"editicon"=>"editField($(this).parents('tr'))",
		);
		
if($_REQUEST["action"]=="viewstruct") {
	$sql="DESCRIBE {$_REQUEST['tbl']}";
	$result=_dbQuery($sql);
	echo printResultTable($result,$toolBtns,false);
} elseif($_REQUEST["action"]=="deletefield") {
	$sql="ALTER TABLE {$_REQUEST['tbl']} DROP {$_REQUEST['fld']}";
	_dbQuery($sql);
	
	$sql="DESCRIBE {$_REQUEST['tbl']}";
	$result=_dbQuery($sql);
	echo printResultTable($result,$toolBtns,false);
}
//add,edit
exit();
?>
