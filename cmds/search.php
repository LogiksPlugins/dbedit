<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$tbl=$_REQUEST["tbl"];
$col=$_REQUEST["col"];
$val=$_REQUEST["val"];

$sql="SELECT * FROM $tbl";
$whr="";
if($col=="*") {
	$where="";
	$q="SELECT * FROM $tbl limit 0,1";
	$temp = $con->executeQuery($q);
	$tempColCnt=$con->columnCount($temp);
	for($i=0;$i<$tempColCnt;$i++) {
		$field=$con->fetchField($temp,$i);		
		$s=$field->name;
		$where.="$s LIKE '%$val%'";
		if($i<$tempColCnt-1) $where.=" OR ";
	}	
	$con->freeResult($temp);
	$whr=$where;
} else {
	$col=explode(",",$col);
	foreach($col as $q=>$w) {
		$whr="$w LIKE '%$val%' OR ";
	}
	$whr=trim($whr);
	if(strlen($whr)>4) {
		$whr=substr($whr,0,strlen($whr)-2);
	}
}
if(strlen($whr)>0) $sql="$sql WHERE $whr";
//echo "$sql";
$result=$con->executeQuery($sql);
echo printResultTable($result);
?>
