<div style='width:100%;height:7%;margin-bottom:15px;'>
	<select id=browsetableselector class='tablelist ui-widget-content ui-corner-all' style='width:400px;height:30px;margin-top:3px;margin-right:15px;float:left;font-weight:bold;font-size:14px;' >
	</select>
	<div style='' align=left>
		<button onclick="browseTbl($('#browsetableselector').val())">View Data</button>
	</div>
</div>
<div id=browserresults class='ui-widget-content ui-corner-all' style='width:100%;height:90%;overflow:auto;'>
	<table id=browserresultstable class="datatable" width=100% border=0 cellpadding=0 cellspacing=0>
		<tr><td colspan=10><h3>Select Table To Browse Its Data.</h3></td></tr>
	</table>
</div>
<script>
function browseTbl(tbl) {
	if(tbl.length<=0) {
		$('#browserresultstable').html("<tr><td>No SQL Query Found.</td></tr>");
		return;
	}
	$('#browserresultstable').html("<tr><td colspan=10><div class='ajaxloading'>Loading ... </div></td></tr>");
	src=getCMD();
	prms="&action=viewdata&tbl="+tbl+"&limit=0,100";
	processAJAXPostQuery(src,prms,function(txt) {
			$('#browserresultstable').html(txt);
			updateTableView("#browserresultstable");
		});
}
function deleteRecord(tr) {
	tbl=$("#browsetableselector").val();
	col=tr.find("td.serial_col").attr("key");
	fld=tr.attr("id");
	fld=fld.replace("ROW_","");
	src=getCMD();
	prms="&action=deleterow&tbl="+tbl+"&fld="+fld+"&col="+col;
	lgksConfirm("Are You Sure about deleting Field <b>"+fld+"</b> From Table <b>"+tbl+"</b> ?","Delete Field",function() {
				processAJAXPostQuery(src,prms,function(txt) {
						$('#browserresultstable').html(txt);
						updateTableView("#browserresultstable");
					});
			});
}
</script>
