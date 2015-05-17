page="dbinfo";
$(function() {
	w=getWindowSize();
	$("#workspace").css("height",(w.h-$("#toolbar").height()-5)+"px");
	$("#workspace").css("width",(w.w-0)+"px");
	
	s="<div id=csvfileuploadform style='display:none;' title='Import CSV File' >Select A CSV File To Upload <br/><br/>";
	s+="<form onsubmit=\"return checkCSVFile('#csvfileuploadform')\" method=POST enctype='multipart/form-data' target='csv_upload_frame' ";
	s+="action='services/?scmd=dbedit&page=tables&dbsessid="+instanceID+"&action=importtable' >";
	s+="<table width=100% border=0 style='border:0px'>";
	s+="<tr><th align=left width=100px style='border:0px'>DataTable :: </th><td style='border:0px'><input name=dbtbl type=text readonly style='width:100%;height:23px;border:1px solid #eee;' /></td></tr>";
	s+="<tr><th align=left width=100px style='border:0px'>CSV File :: </th><td style='border:0px'><input name=csvfile type=file style='width:100%;height:23px;border:1px solid #eee;' /></td></tr>";
	s+="<tr><td colspan=10>&nbsp;</td></tr><tr><td colspan=10 align=center>";
	s+="<button type=button onclick=\"$('#csvfileuploadform').dialog('close');\">Close</button><button type=submit>Submit</button></td></tr>";
	s+="</table></form><br/>";
	s+="<iframe id=csv_upload_frame name=csv_upload_frame style='display:none'></iframe>";
	s+="<div>";
	$("#workspace").parent().append(s);
	
	$("button").button();
	$(".tabs").tabs();
	
	$("#toolbar button:first-child").addClass("ui-state-highlight");
});
function closeCSVDlg() {
	$('#csvfileuploadform').dialog('close');
}
function showPage(pageid, tbl) {
	if("#"+page==pageid) return;
	page=pageid.substr(1,pageid.length);
	anim="blind";
	opts={};
	$("#workspace .page.active").hide(anim,opts);
	$("#workspace .page.active").removeClass("active");
	
	$("#workspace " + pageid).show(anim,opts);
	$("#workspace " + pageid).addClass("active");
	
	if($(pageid).find("select.tablelist").length>0) {
		loadTableLists(pageid+" select.tablelist","select","tablelist",function() {
					if(tbl!=null) $(pageid+" select.tablelist").val(tbl);
			});
	}
	if($(pageid).find("table .tabelList").length>0) {
		loadTableLists(pageid+" table .tabelList","table","tablelist");
	}
	if($(pageid).find("table .tablelistdetails").length>0) {
		loadTableLists(pageid+" table .tablelistdetails","table","tablelistdetails");
	}
	
	$("#toolbar button.ui-state-highlight").removeClass("ui-state-highlight");
	$("#toolbar button[forpg="+pageid+"]").addClass("ui-state-highlight");
}
function updateTableView(id) {
	$(id + " thead").addClass("ui-state-active ui-corner-top");
	//$(id + " tbody").addClass("scrollContent");
	$(id + " caption").addClass("ui-state-default");
	
	$(id + " tbody tr").hover(function() {
			$(this).addClass("ui-state-highlight");
		},function() {
			$(this).removeClass("ui-state-highlight");
		});
}
function updateAutoComplete(id) {
	$(id).each(function() {
			var minL=1;
			if($(this).attr("minlength")!=null) minL=parseInt($(this).attr("minlength"));
			
			if($(this).attr("src")!=null) {
				var href=$(this).attr("src");				
				$(this).autocomplete({
						minLength: minL,
						source:href,						
					});
				}
		});
}
function loadTableLists(ele,format,cmd,callback) {
	if(cmd==null) cmd="tablelist";
	if(format==null) format="select";
	if(format=="select") $(ele).html("<option>Loading ...</option>");
	else $(ele).html("<tr><td colspan=10 class=ajaxloading6>Loading ...</td></tr>");
	src=getCMD();
	prms="&action="+cmd+"&format="+format;
	
	$(ele).load(src,prms,function(txt) {
			if(callback!=null && (typeof callback=="function")) callback(txt);
		});
}
function requestData() {
	//src=getCMD();
	//alert(src);
}
function viewBlobData(name,tbl,idcol,id) {
	l=getCMD();
	q="&action=viewblob&dbtbl="+tbl+"&col="+name+"&id="+id+"&idcol="+idcol;	
	openInNewPopupWindow(l+q,"DateViewer");
}
function checkCSVFile(frm) {
	f=$(frm+" input[type=file]").val();
	f1=f.toLowerCase();
	if(f1.lastIndexOf(".csv")==-1) {
		$(frm+" input[type=file]").val("");
	    lgksAlert("Please upload only .csv extention files");
	    return false;
	}	
	return true;
}
/*Table Commands*/
function exportTable(tbl) {
	src=getCMD("tables");
	prms="&action=exporttable&dbtbl="+tbl;
	window.open(src+prms);
}
function importTable(tbl) {
	$("#csvfileuploadform input[name=dbtbl]").val(tbl);
	$("#csvfileuploadform input[name=csvfile]").val("");
	
	osxPopupDiv("#csvfileuploadform",null,400);
}
function templateTable(tbl) {
	src=getCMD("tables");
	prms="&action=templatetable&dbtbl="+tbl;
	window.open(src+prms);
}
function browseTable(tbl) {
	showPage("#browse",tbl);
}
function structureTable(tbl) {
	showPage("#structure",tbl);
}
