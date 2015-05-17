<div style='width:100%;height:7%;margin-bottom:15px;'>
	<select id=structtableselector class='tablelist ui-widget-content ui-corner-all' style='width:400px;height:30px;margin-top:3px;margin-right:15px;float:left;font-weight:bold;font-size:14px;' >
	</select>
	<div style='' align=left>
		<button onclick="structTbl($('#structtableselector').val())">View Structure</button>
	</div>
</div>
<div id=structrresults class='ui-widget-content ui-corner-all' style='width:100%;height:90%;overflow:auto;'>
	<table id=structrresultstable class="datatable" width=100% border=0 cellpadding=0 cellspacing=0>
		<tr><td colspan=10><h3>Select A Table To View Its Structure.</h3></td></tr>
	</table>
</div>
<script>
function structTbl(tbl) {
	if(tbl.length<=0) {
		$('#structrresultstable').html("<tr><td>No Table Mentioned.</td></tr>");
		return;
	}
	$('#structrresultstable').html("<tr><td colspan=10><div class='ajaxloading'>Loading ... </div></td></tr>");
	src=getCMD();
	prms="&action=viewstruct&tbl="+tbl;
	processAJAXPostQuery(src,prms,function(txt) {
			$('#structrresultstable').html(txt);
			updateTableView("#structrresultstable");
		});
}
function deleteField(tr) {
	tbl=$("#structtableselector").val();
	fld=tr.attr("id");
	fld=fld.replace("ROW_","");
	src=getCMD();
	prms="&action=deletefield&tbl="+tbl+"&fld="+fld;
	lgksConfirm("Are You Sure about deleting Field <b>"+fld+"</b> From Table <b>"+tbl+"</b> ?","Delete Field",function() {
				processAJAXPostQuery(src,prms,function(txt) {
						$('#structrresultstable').html(txt);
						updateTableView("#structrresultstable");
					});
			});
}

</script>
