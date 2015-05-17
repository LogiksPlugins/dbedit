<?php
$tables=array();
$result = $_SESSION[$InstanceID."_CONN"]->executeQuery('SHOW TABLES');
if($result) {
	while ($row = $_SESSION[$InstanceID."_CONN"]->fetchData($result,"array")) {
		array_push($tables,$row[0]);
	}
}
$_SESSION[$InstanceID."_CONN"]->freeResult($result);
?>
<div style='width:100%;height:7%;margin-bottom:15px;'>
	<select id=inserttableselector class='ui-widget-content ui-corner-all' style='width:400px;height:30px;margin-top:3px;margin-right:15px;float:left;font-weight:bold;font-size:14px;' >
		<?php
			foreach($tables as $a=>$b) {
				echo "<option>$b</option>";
			}
		?>
	</select>
	<div style='' align=left>
		<button onclick="showForm($('#inserttableselector').val())">Insert Data</button>
	</div>
</div>
<div id=insertform class='ui-widget-content ui-corner-all' style='width:100%;height:90%;overflow:auto;'>

</div>
<script>
function showForm(tbl) {
	if(tbl.length<=0) {
		$('#insertform').html("<tr><td>No Table Mentioned.</td></tr>");
		return;
	}
	$('#insertform').html("<div class='ajaxloading6'>Loading ... </div>");
	src=getCMD();
	prms="&action=autoform&dbtbl="+tbl;
	processAJAXPostQuery(src,prms,function(txt) {
			if(txt.length>0) {
				$('#insertform').html(txt);
			}
		});
}
</script>
