<div style='width:100%;height:7%;margin-bottom:15px;'>
	<select id=searchtableselector class='tablelist ui-widget-content ui-corner-all' 
		style='width:200px;height:30px;margin-top:3px;margin-right:15px;float:left;font-weight:bold;font-size:14px;' 
		onchange="updateColumns(this.value,'#searchcolumnelector');" >
	</select>
	<select id=searchcolumnelector class='ui-widget-content ui-corner-all' style='width:200px;height:30px;margin-top:3px;margin-right:15px;float:left;font-weight:bold;font-size:14px;' >
		<option value='*'>All Columns</option>
	</select>
	<input id=searchtxt type=text class='ui-widget-content ui-corner-all' style='width:200px;height:28px;margin-top:1px;margin-right:15px;float:left;font-weight:bold;font-size:16px;' />
	<div style='' align=left>
		<button onclick="searchTbl($('#searchtableselector').val(),$('#searchcolumnelector').val(),$('#searchtxt').val())">Search</button>
	</div>
</div>
<div id=searchrresults class='ui-widget-content ui-corner-all' style='width:100%;height:90%;overflow:auto;'>
	<table id=searchrresultstable class="datatable" width=100% border=0 cellpadding=0 cellspacing=0>
		<tr><td colspan=10><h3>Select Table And Column To Start Searching Data.</h3></td></tr>
	</table>
</div>
<script>
function searchTbl(tbl,col,val) {
	if(tbl.length<=0) {
		$('#searchrresultstable').html("<tr><td colspan=10><h3>Select A Table To Search In</h3></td></tr>");
		return;
	}
	if(val.length<=0) {
		$('#searchrresultstable').html("<tr><td colspan=10><h3>Please Give What To Search In Table</h3></td></tr>");
		return;
	}
	if(col.length<=0 || col=="#") {
		$('#searchrresultstable').html("<tr><td colspan=10><h3>Please Try Again. Loading Has Not Yet Finised.</h3></td></tr>");
		return;
	}
	$('#searchrresultstable').html("<tr><td colspan=10><div class='ajaxloading'>Loading ... </div></td></tr>");
	src=getCMD();
	prms="&action=search&tbl="+tbl+"&col="+col+"&val="+val;
	processAJAXPostQuery(src,prms,function(txt) {
			if(txt.length>0) {
				$('#searchrresultstable').html(txt);
				updateTableView("#searchrresultstable");
			} else {
				$('#searchrresultstable').html("<tr><td colspan=10 align=center><h3 align=center>No Results Found For Given Search String.</h3></td></tr>");
			}
		});
}
function updateColumns(tbl,selectorID) {
	if(tbl.length<=0) {
		$(selectorID).html("<option value='*'>All Columns</option>");
		return;
	}
	$(selectorID).html("<option value='#'><div class='ajaxloading4'>Loading ... </div></option>");
	src=getCMD();
	prms="&action=columnlist&tbl="+tbl+"&format=select";
	processAJAXPostQuery(src,prms,function(txt) {
			if(txt.length>0) {
				$(selectorID).html("<option value='*'>All Columns</option>"+txt);
			}
		});
}
</script>
