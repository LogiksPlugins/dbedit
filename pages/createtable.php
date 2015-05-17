<?php
$engines=array();

$sql="SHOW ENGINES";
$result = $_SESSION[$InstanceID."_CONN"]->executeQuery($sql);
if($result) {
	while ($row = $_SESSION[$InstanceID."_CONN"]->fetchData($result,"array")) {
		$engines[sizeOf($engines)]=array($row[0],$row[2]);
	}
}
$_SESSION[$InstanceID."_CONN"]->freeResult($result);
?>
<div style='width:100%;height:7%;margin-bottom:0px;'>
	<div style='float:left;font-size:14px;font-weight:bold;padding:5px;' align=left>
		Table Name 
		<input id=tblname type=text class='ui-widget-content ui-corner-all' style='width:200px;height:20px;font-weight:bold;font-size:16px;margin-left:10px;' />
		Table Type 
		<select id=tbltype class='ui-widget-content ui-corner-all' style='width:400px;height:23px;font-weight:bold;font-size:16px;margin-left:10px;' >
			<?php
				foreach($engines as $a=>$b) {
					$t1=$b[0];
					$t2=$b[1];
					echo "<option value='$t1'>$t1 [$t2]</option>";
				}
			?>
		</select>
		  
	</div>
	<div style='float:right' align=left>
		<button onclick="showSQL()" style='width:100px;'><div class='sqlicon'>SQL</div></button>
		<button onclick="resetDesigner()" style='width:100px;'><div class='reseticon'>Reset</div></button>
	</div>
</div>
<div id=designer class='ui-widget-content ui-corner-all' style='width:100%;height:90%;overflow:auto;'>
	<br/>
	<table id=designertable class='ui-widget-content ui-corner-all' border=0 cellspacing=0 cellpadding=0>
		<thead class="ui-widget-header" align=center>
			<tr height=25px>
				<td>Column Name</td>
				<td>Data Type</td>
				<td>Primary</td>
				<td>Not Null</td>
				<td>Auto Increament</td>
				<td>Unique</td>
				<td>Flags</td>
				<td>Default Value</td>
				<td>Comments</td>
				<td width=60px>--</td>
			</tr>
		</thead>
		<tbody>			
		</tbody>
	</table>
	<br/><br/>
	<table class='ui-widget-content ui-corner-all' border=0 cellspacing=0 cellpadding=0 style='margin-left:5%;width:50%;font-weight:bold;font-size:13px;'>
		<tr class="ui-state-active">
			<td colspan=10 height=20px>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Advanced Configurations</td>
		</tr>
		<tr>
			<td width=200px>TEMPORARY Table</td><td width=300px><input id=TEMPTBL class='TEMP' type='checkbox' /></td><td>&nbsp;</td>
		</tr>
		<tr>
			<td width=200px>IF NOT EXISTS</td><td width=300px><input id=IFNOTEXISTS class='IFNOTEXISTS' type='checkbox' /></td><td>&nbsp;</td>
		</tr>		
	</table>
	<br/><br/>
	<div style='width:95%;' align=right>
		<button onclick="runSQL()" style='width:200px;'><div class='runicon' style="width:100%;">Create-Table</div></button>
	</div>
</div>
<div id=sqlRunPad title="Create DataTable" style='display:none;overflow:hidden;'>	
</div>
<script>
var sqlRunning=false;
$(function() {
	$('#designertable tbody').html(generateBlankRecord("ID"));
	updateDTable();
});
function resetDesigner() {
	lgksConfirm("Are You Sure About Reseting Designer ?","Reset Designer",function() {
			$('#designertable tbody').html(generateBlankRecord("ID"));
			updateDTable();
			$("#tblname").val("");			
		});
}
function showSQL() {
	sql=generateSQL(false).trim();
	if(sql.length<=0) {
		return;
	}
	msg="<div class='ui-widget-content ui-border-all' style='width:550px;height:250px;overflow:auto;'><i style='color:maroon'><b>'"+sql+"'</b></i></div>";
	lgksAlert(msg,"Generated SQL");
}
function runSQL() {
	s=generateSQL(false);
	if(s.length<=0) return;
	$("#sqlRunPad").html("<textarea style='width:99%;height:99%;border-color:#aaa;resize:none;color:#1C3C7A;'>"+s+"</textarea>");
	lgksPopup("#sqlRunPad",
			{
				"Run":function() {
					if(sqlRunning) return false;
					if($("#sqlRunPad textarea").length<=0) {
						$(this).dialog( "close" );
						return;
					}
					s=$("#sqlRunPad textarea").val();
					
					r=$(this);
					src=getCMD();
					p="&action=createtbl&sql="+s;
					$("#sqlRunPad").html("<div class='ajaxloading'>Please Wait [Do Not Close This Window] ...</div>");
					
					sqlRunning=true;
					processAJAXPostQuery(src,p,function(txt) {
						$("#sqlRunPad").html("<span style='color:#2B7A4B;font-size:1.5em;'>"+txt+"</span>");						
						sqlRunning=false;
					});
				},
				"Reset":function() {
					if(sqlRunning) return false;
					$(this).dialog("close");
					resetDesigner();					
				},
				"Close":function() {
					if(sqlRunning) return false;
					$(this).dialog( "close" );
				},				
			},
			{width:600,height:400,show:"blind",hide:"blind",closeOnEscape:true,resizable:"none",
				beforeClose:function() {
						if(sqlRunning) return false;
						else return true;
					}}
		);
}
function generateBlankRecord(name) {
	if(name==null) name="";
	s="";
	s+="<tr class='field_row'>";
	s+="<td><input class='name' type=text value='"+name+"' /></td>";
	s+="<td><input class='type autocomplete' type=text src='services/?scmd=lookups&src=sqldatatypes' style='text-transform:uppercase;'/></td>";
	s+="<td><input class='PK' type='checkbox' /></td>";
	s+="<td><input class='NN' type='checkbox' /></td>";
	s+="<td><input class='AI' type='checkbox' /></td>";	
	s+="<td><input class='U' type='checkbox' /></td>";	
	s+="<td><input class='flags' type=text style='text-transform:uppercase;' /></td>";
	s+="<td><input class='defaults' type=text /></td>";
	s+="<td><input class='comments' type=text /></td>";
	s+="<td align=right>";
		s+="<div class='tbltoolbtn deleteicon' style='float:right;' onclick='removeRow(this)'></div>";
		s+="<div class='tbltoolbtn addicon' style='float:right;' onclick='addRow(this)'></div>";
	s+="</td>";
	s+="</tr>";
	
	return s;
}

//Table Functions
function removeRow(src) {
	if($('#designertable tbody').children().length==1) {
		addRow(src);
	}
	$(src).parent().parent("tr").detach();
}
function addRow(src) {
	if(checkBlankRow()) return;
	
	$('#designertable tbody').append(generateBlankRecord());
	updateDTable();
	child=$(src).parent().parent("tr").parent("tbody").find("tr:last-child td:first-child input");
	child.focus();
}
function updateDTable() {
	updateAutoComplete("#designertable tbody .autocomplete");
	$("#tblname").blur(function() {
			$("#tblname").val($("#tblname").val().replace(" ","_"));
		});
	$('#designertable tbody input.comments').keydown(function(event,ui) {
			if(event.keyCode==9 && !event.shiftKey) {
				event.preventDefault();
				addRow(this);
			}			
		});
	$('#designertable tbody input.type').focus(function(event,ui) {
			if($(this).val().length<=0) {
				$(this).val("INTEGER");
			}
		});
	$('#designertable tbody input.flags').focus(function(event,ui) {
			s=$(this).parent().parent().find("input.type").val();			
			if(s=="TINYINT" || s=="SMALLINT" || s=="MEDIUMINT" || s=="INT" || s=="INTEGER" || 
					s=="BIGINT" || s=="REAL" || s=="DOUBLE" || s=="FLOAT" || s=="DECIMAL" || s=="NUMERIC") {
				$(this).val("UNSIGNED ZEROFILL");
			} else if(s=="TINYTEXT" || s=="TEXT" || s=="MEDIUMTEXT" || s=="LONGTEXT") {
				$(this).val("BINARY");
			}
		});
}
function checkBlankRow() {
	child=$('#designertable tbody').find("tr:last-child td:first-child input");
	if(child.val().length>0) return false;
	else  return true;
}
function generateSQL(silent) {
	if(silent==null) silent=false;
	if($("#tblname").val().length<=0) {
		if(!silent) lgksAlert("Table Name Is Missing.");
		return "";
	}
	pkey=[];
	fields=[];
	sql="CREATE";
	if($("#TEMPTBL").is(':checked')) {
		sql+=" TEMPORARY";
	}
	sql+=" TABLE ";
	if($("#IFNOTEXISTS").is(':checked')) {
		sql+="IF NOT EXISTS ";
	}
	sql+=$("#tblname").val().replace(" ","_")+" (";
	$('#designertable tbody tr').each(function() {
			if($(this).find(".name").val().length<=0) return;
			if($(this).find(".type").val().length<=0) return;
			
			s="";
			s+=$(this).find(".name").val();
			s+=" "+$(this).find(".type").val();
			
			s+=" "+$(this).find(".flags").val();
			
			if($(this).find(".U").is(':checked'))
				s+=" UNIQUE";
						
			if($(this).find(".NN").is(':checked'))
				s+=" NOT NULL";
			else
				s+=" NULL";
			
			if($(this).find(".defaults").val().length>0) {
				s+=" DEFAULT " + getTxt($(this).find(".defaults").val(), $(this).find(".type").val());				
			} else {
				if($(this).find(".AI").is(':checked')) {
					s+=" AUTO_INCREMENT";
				}
			}
			if($(this).find(".comments").val().length>0) {
				s+=" COMMENT '"+$(this).find(".comments").val()+"'";
			}
			
			
			if($(this).find(".PK").is(':checked')) {
				pkey.push($(this).find(".name").val());
			}
			
			if(s.length>0) {
				fields.push(s);
			}			
		});
	if(fields.length==0) {
		if(!silent) lgksAlert("Please Give Some Columns For The Table.");
		return "";
	}
	sql+=fields.join(",");
	if(pkey.length>0) {				
		sql+=",PRIMARY KEY("+pkey.join(",")+")";
	}
	sql+=")";
	sql+=" TYPE="+$("#tbltype").val();
	return sql;
}
function getTxt(v,s) {
	if(s=="BIT" || s=="BOOL" || s=="TINYINT" || s=="SMALLINT" || s=="MEDIUMINT" || s=="INT" || s=="INTEGER" || 
			s=="BIGINT" || s=="REAL" || s=="DOUBLE" || s=="FLOAT" || s=="DECIMAL" || s=="NUMERIC") {
		return v;
	} else {
		return "'"+v+"'";
	}
}
</script>
