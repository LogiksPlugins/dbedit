<?php
$tblCnt=0;
$result = $_SESSION[$InstanceID."_CONN"]->executeQuery('SHOW TABLES');
$tblCnt=$_SESSION[$InstanceID."_CONN"]->recordCount($result);
$_SESSION[$InstanceID."_CONN"]->freeResult($result);


$process="";
$result = mysql_list_processes($_SESSION[$InstanceID."_CONN"]->getLink());
while ($row = $_SESSION[$InstanceID."_CONN"]->fetchData($result)) {    
    $process.=sprintf("<div class='hover'>%s %s %s %s %s</div>", $row["Id"], $row["Host"], $row["db"],$row["Command"], $row["Time"]);
}
$_SESSION[$InstanceID."_CONN"]->freeResult($result);

$datas=array();
$result = $_SESSION[$InstanceID."_CONN"]->executeQuery('SHOW STATUS');
while ($row = $_SESSION[$InstanceID."_CONN"]->fetchData($result)) {
    $datas[$row['Variable_name']]=$row['Value'];
}
$_SESSION[$InstanceID."_CONN"]->freeResult($result);

?>
<div class='ui-widget-content ui-corner-top' style='width:40%;float:left;'>
	<div class='ui-state-active ui-corner-top' style='height:18px;padding:3px;'>Database Information</div>
	<table id=dbinfo width=100% border=0>
		<tr class="hover">
			<td class='title'>Db Host</td><td class='value'><?=$GLOBALS['DBCONFIG']["DB_HOST"]?></td>
		</tr>
		<tr class="hover">
			<td class='title'>Db Name</td><td class='value'><?=$GLOBALS['DBCONFIG']["DB_DATABASE"]?></td>
		</tr>
		<tr class="hover">
			<td class='title'>Db User</td><td class='value'><?=$GLOBALS['DBCONFIG']["DB_USER"]?></td>
		</tr>
		<tr class="hover">
			<td class='title'>Table Count</td><td class='value'><?=$tblCnt?></td>
		</tr>
		<tr class="hover">
			<td class='title'>Collation</td><td class='value'><?=mysql_client_encoding($_SESSION[$InstanceID."_CONN"]->getLink())?></td>
		</tr>
		<tr class="hover">
			<td class='title'>MySQL Client info</td><td class='value'><?=mysql_get_client_info()?></td>
		</tr>
		<tr class="hover">
			<td class='title'>MySQL Host info</td><td class='value'><?=mysql_get_host_info()?></td>
		</tr>
		<tr class="hover">
			<td class='title'>MySQL Server info</td><td class='value'><?=mysql_get_server_info()?></td>
		</tr>
		<tr class="hover">
			<td class='title'>MySQL Proto info</td><td class='value'><?=mysql_get_proto_info()?></td>
		</tr>	
	</table>
</div>
<div class="ui-corner-top" style='width:50%;height:90%;float:right;'>
	<div class='ui-state-active ui-corner-top' style='height:18px;padding:3px;'>DB Status</div>
	<div class="ui-widget-content" style='width:98%;height:93%;overflow:auto;padding:3px;'>
	<table id=dbinfo width=100% border=0>
		<?php
			foreach($datas as $a=>$b) {
				$t=str_replace("_"," ",$a);
				$t=ucwords($t);
				echo "<tr class='hover'><td class='title'>$t</td><td class='value'>$b</td></tr>";
			}
		?>
	</table>
	</div>
</div>
<div class='ui-widget-content ui-corner-top' style='width:40%;height:150px;float:left;margin-top:30px;'>
	<div class='ui-state-active ui-corner-top' style='height:18px;padding:3px;'>Active Process</div>
	<div style='width:99%;height:170px;overflow:auto;padding:3px;cursor:pointer;'>
		<?=$process?>
	</div>
</div>
