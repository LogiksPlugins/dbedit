<?php
if (!defined('ROOT')) exit('No direct script access allowed');

$a=checkModule("autoforms");
if($a) {
	loadModule("autoforms");
	loadFormFromRequest("POST");
} else {
	echo "<h2 align=center style='color:maroon;'>AutoForms Module Is Required But Not Found</h2>";
}
?>
