<div style='width:100%;height:30%;margin-bottom:15px;'>
	<textarea id=queryeditor class='ui-widget-content ui-corner-all' style='height:80%;width:80%;resize:none;'></textarea>
	<select class='tablelist ui-widget-content ui-corner-all' style='width:18%;height:100%;float:right;font-weight:bold;' size=2 ondblclick="insertQuery(this.value,'select')">
	</select>
	<div style='width:80%' align=right>
		<button onclick="designQuery()">Design</button>
		<button onclick="$('#queryeditor').val('');">Clear</button>
		<button onclick="execute($('#queryeditor').val())">Run SQL</button>
	</div>
</div>
<div id=queryresults class='ui-widget-content ui-corner-all' style='width:100%;height:66%;overflow:auto;'>
	<table id=queryresultstable class="datatable" width=100% border=0 cellpadding=0 cellspacing=0>
		<tr><td colspan=10><h3>Invoke A SQL To See The Results Here.</h3></td></tr>
	</table>
</div>
<script>
$(function() {
	//updateTableView("#queryresultstable");
});
function insertQuery(tbl,type) {
	if(tbl.length>0) {
		if(type=="select") {
			$('#queryeditor').val('SELECT * FROM '+tbl);
		}
	}
}
function execute(sql) {
	if(sql.length<=0) {
		$('#queryresultstable').html("<tr><td>No SQL Query Found.</td></tr>");
		return;
	}
	$('#queryresultstable').html("<tr><td colspan=10><div class='ajaxloading'>Loading ... </div></td></tr>");
	src=getCMD();
	prms="&action=query&sql="+sql;
	processAJAXPostQuery(src,prms,function(txt) {
			if(txt.length>0) {
				$('#queryresultstable').html(txt);
				updateTableView("#queryresultstable");
			} else {
				$('#queryresultstable').html("<tr><td colspan=10>Query Didn't Return Any Result Set</td></tr>");
			}
		});
}
function designQuery() {
	<?php
		$a=checkModule("sqlgenerator");
		if($a) {
			echo "lgksOverlayURL('services/?scmd=sqlgenerator&callback=insertSQL');";
		} else {
			echo "lgksAlert('<h2 align=center>SQLGenerator Module Is Required But Not Found</h2>');";
		}
	?>
}
function insertSQL(sql) {
	$("#queryeditor").val(sql);
}
</script>
