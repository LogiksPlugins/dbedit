<?php
if (!defined('ROOT')) exit('No direct script access allowed');

$dbeditplugins=array();

$dbeditplugins[sizeOf($dbeditplugins)]=array(
	"title"=>"DBInfo","page"=>"dbinfo","icon"=>"dbinfoicon","tips"=>"View Table Structures.");
$dbeditplugins[sizeOf($dbeditplugins)]=array(
	"title"=>"Tables","page"=>"tables","icon"=>"tableicon","tips"=>"View/Edit/Manage Existing Tables");
$dbeditplugins[sizeOf($dbeditplugins)]=array(
	"title"=>"Structure","page"=>"structure","icon"=>"structureicon","tips"=>"View Table Structures.");
$dbeditplugins[sizeOf($dbeditplugins)]=array(
	"title"=>"DataView","page"=>"browse","icon"=>"browseicon","tips"=>"View Table Data.");
$dbeditplugins[sizeOf($dbeditplugins)]=array(
	"title"=>"Query","page"=>"query","icon"=>"sqlicon","tips"=>"Execute SQL Queries.");
$dbeditplugins[sizeOf($dbeditplugins)]=array(
	"title"=>"Search","page"=>"search","icon"=>"searchicon","tips"=>"Search Through Database.");
$dbeditplugins[sizeOf($dbeditplugins)]=array(
	"title"=>"Create","page"=>"createtable","icon"=>"createtableicon","tips"=>"Create New Table.");
$dbeditplugins[sizeOf($dbeditplugins)]=array(
	"title"=>"Insert","page"=>"insert","icon"=>"inserticon","tips"=>"Insert Data Into Table Using AutoForms");
?>
