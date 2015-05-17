<div id=alltables class='ui-widget-content ui-corner-all' style='width:100%;height:100%;overflow:auto;'>
	<br/>
	<div class='ui-widget-content ui-corner-all' style='width:75%;height:95%;margin-left:10px;overflow:auto;float:left;'>
		<table id=alltables class='' width=100% border=0 cellspacing=0 cellpadding=0 style=''>
			<thead class="ui-widget-header" align=center>
				<tr height=25px>
					<td width=30px align=center style='padding-right:10px;'><input title='Select/Unselect All Tables' type=checkbox onchange="checkALL($(this).is(':checked'))" /></td>
					<td width=300px style='border-right:1px solid #aaa;'>Table Name</td>
					<td width=180px style='border-right:1px solid #aaa;'>Engine</td>
					<td width=80px style='border-right:1px solid #aaa;'>Rows</td>
					<td width=80px style='border-right:1px solid #aaa;'>Index</td>
					<td width=80px style='border-right:1px solid #aaa;'>Overhead</td>
					<td width=180px style='border-right:1px solid #aaa;'>Created</td>
					<td width=180px style='border-right:1px solid #aaa;'>Updated</td>
					<td width=260px style='border-right:1px solid #aaa;'>--</td>
				</tr>
			</thead>
			<tbody id=tableList class="tablelistdetails"></tbody>
		</table>
	</div>
	<div id=tblButtonGrps style='float:right;margin-right:10px;width:180px;' align=right>
		<button name='reload' onclick="btnAct(this)" class='' style='width:170px;'><div class='tableediticon' style="width:100%;padding:0px;">Reload</div></button>
		<br/><br/><hr/>
		<button name='checktable' onclick="btnAct(this)" class='' style='width:170px;'><div class='tablecheckicon' style="width:100%;padding:0px;">Check-Table</div></button>
		<button name='analyzetable' onclick="btnAct(this)" class='' style='width:170px;'><div class='tableanalyzeicon' style="width:100%;padding:0px;">Analyze-Table</div></button>
		<button name='repairtable' onclick="btnAct(this)" class='' style='width:170px;'><div class='tablerepairicon' style="width:100%;padding:0px;">Repair-Table</div></button>
		<button name='optimizetable' onclick="btnAct(this)" class='' style='width:170px;'><div class='tableoptimizeicon' style="width:100%;padding:0px;">Optimize-Table</div></button>
		<hr/>
		<!--<button name='flushtable' onclick="btnAct(this)" class='confirm' style='width:170px;'><div class='tableflushicon' style="width:100%;padding:0px;">Flush-Table</div></button>-->
		<button name='emptytable' onclick="btnAct(this)" class='confirm' style='width:170px;'><div class='tableemptyicon' style="width:100%;padding:0px;">Empty-Table</div></button>
		<button name='droptable' onclick="btnAct(this)" class='confirm' style='width:170px;'><div class='tabledeleteicon' style="width:100%;padding:0px;">Drop-Table</div></button>
		<hr/>
	</div>
</div>
<script language='javascript'>
function btnAct(btn) {
	act=$(btn).attr("name");
	clz="";	
	msg=$(btn).text();
	msg="Do you really want to "+msg+" ?<br/>";
	
	if($(btn).hasClass("confirm")) clz="confirm";
	else if($(btn).hasClass("popup")) clz="popup";
	else if($(btn).hasClass("window")) clz="window";
	
	doAction(act,clz,msg,$(btn).text());
}
function doAction(act,clz,msg,title) {
	if(act=="reload") {
		loadTableLists("#alltables table .tablelistdetails","table","tablelistdetails");
		return;
	}
	
	tbls=$("#tableList .tblcheckbox input[type=checkbox]:checked");
	if(tbls.length) {
		tbllst="";
		tbls.each(function() {
				b=$(this).attr("name");
				if(b.length>0) {
					tbllst+=b+",";
				}
			});
		msg+="<br/><b>"+tbllst+"</b><br/>";
		msg+="<br/>All Actions are final and  cannot be reverted back or restored?";
		
		if(clz=="confirm") {			
			lgksConfirm(msg,title,function() {
					runCmd(act,tbllst);
				});
		} else if(clz=="popup") {
			src=getCMD();
			prms="&action="+act+"&dbtbl="+tbllst;
			if(typeof openInNewTab=="function") openInNewTab(title,src+prms);
			else if(typeof parent.openInNewTab=="function") parent.openInNewTab(title,src+prms);
			else lgksOverlayURL(src+prms);
		} else if(clz=="window") {
			tbls=$("#tableList .tblcheckbox input[type=checkbox]:checked").attr("name");
			lgksConfirm("Only One Table Is Supported At A Time. <br/>Do you want to " + title+ " <br/><br/><div align=center><h3>"+tbls+"</h3></div>",title,function() {
					src=getCMD();
					prms="&action="+act+"&dbtbl="+tbls;
					window.open(src+prms);
				});
		} else {
			runCmd(act,tbllst);
		}
	}	
}
function runCmd(cmd,tbllst) {
	src=getCMD();
	prms="&action="+cmd+"&dbtbl="+tbllst;
	processAJAXPostQuery(src,prms,function(txt) {
				if(txt.trim().length>0) {
					lgksAlert(txt);
				}
				loadTableLists("#alltables table .tablelistdetails","table","tablelistdetails",function() {
								tbllst=tbllst.split(",");
								r=[];
								$(tbllst).each(function(a,b) {
										r.push("#tableList .tblcheckbox input[type=checkbox][name="+b+"]");
									});
								
								$(r.join(", ")).attr("checked","true");
						});
			});
}
function checkALL(b) {
	if(b) {
		$("#tableList .tblcheckbox input[type=checkbox]").attr("checked","true");
	} else {
		$("#tableList .tblcheckbox input[type=checkbox]").removeAttr("checked");
	}
}
</script>
