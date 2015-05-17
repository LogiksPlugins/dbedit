<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
isAdminSite();

if(isset($_REQUEST["action"])) {
	$act=$_REQUEST["action"];
	$dbsessid=null;
	if(isset($_REQUEST["dbsessid"])) {
		$dbsessid=$_REQUEST["dbsessid"];
		$site=$_SESSION[$dbsessid."_SITE"];
	} elseif(isset($_REQUEST['forsite'])) {
		$dbsessid=session_id();
		$site=$_REQUEST['forsite'];
		checkUserSiteAccess($_REQUEST["forsite"]);
	} else {
		exit("DB Session Expired");
	}

	if(!isset($_REQUEST["limit"])) {
		$_REQUEST["limit"]="0,100";
		$_POST["limit"]="0,100";
	}

	checkUserSiteAccess($site,true);

	if($site=="admincp" && $_SESSION["SESS_PRIVILEGE_ID"]<=2) {
		$dbFile=ROOT."config/db.cfg";
	} else {
		$dbFile=ROOT.APPS_FOLDER.$site."/config/db.cfg";
	}
	if(file_exists($dbFile)) {
		LoadConfigFile($dbFile);
		$con=new Database($GLOBALS['DBCONFIG']["DB_DRIVER"]);
		$con->connect($GLOBALS['DBCONFIG']["DB_USER"],$GLOBALS['DBCONFIG']["DB_PASSWORD"],$GLOBALS['DBCONFIG']["DB_HOST"],$GLOBALS['DBCONFIG']["DB_DATABASE"]);
		$_SESSION[$dbsessid."_CONN"]=$con;
	} else {
		exit("DB Configuration Missing For Site");
	}

	if($act=="tablelist") {
		$frmt=$_REQUEST["format"];
		$sql="SHOW TABLES";
		$result=_dbQuery($sql);
		$s="";
		if($result) {
			while($row = $con->fetchData($result,"array")) {
				$t=$row[0];
				if($frmt=="select") {
					$s.="<option value='$t'>$t</option>";
				} elseif($frmt=="table") {
					$s.="<tr><td align=center style='padding-right:10px;' class='tblcheckbox'><input type=checkbox name='$t' />";
					$s.="</td><td height=22px><b class='tblname'>$t</b></td></tr>";
				} else {
					$s.="$t<br/>";
				}
			}
			_db()->freeResult($result);
		}
		exit($s);
	} else if($act=="tablelistdetails") {
		$frmt=$_REQUEST["format"];
		$sql = "SHOW TABLE STATUS";
		$result=_dbQuery($sql);
		$s="";
		if($result) {
			while($row = $con->fetchData($result,"array")) {
				$t=$row[0];
				if($frmt=="select") {
					$s.="<option value='$t'>$t</option>";
				} elseif($frmt=="table") {
					$s.="<tr class='hover'><td align=center style='padding-right:10px;' class='tblcheckbox'><input type=checkbox name='{$row[0]}' /></td>";
					$s.="<td><b class='tblname'>{$row[0]}</b></td>";
					$s.="<td><b class='tblname'>{$row[1]}</b></td>";
					$s.="<td align=right><b class='tblname'>{$row[4]}</b></td>";

					if($row[8]>0)
						$s.="<td title='Index Size : {$row[8]}' align=center><div class='status_green_icon'></div></td>";
					else
						$s.="<td title='No Index Found' align=center><div class='status_blue_icon'></div></td>";

					if($row[9]>0)
						$s.="<td title='Needs Optimization\n{$row[9]}' align=center><div class='status_red_icon'></div></td>";
					else
						$s.="<td title='Optimized' align=center><div class='status_green_icon'></div></td>";

					$s.="<td title='{$row[11]}'><b class='tblname'>"._pdate($row[11])."</b></td>";
					$s.="<td title='{$row[12]}'><b class='tblname'>"._pdate($row[12])."</b></td>";

					$s.="<td tbl='{$row[0]}'>";
					$s.="<div title='Export Table' class='tableexporticon' style='float:right;padding:0px;' onclick=\"exportTable($(this).parent().attr('tbl'))\"></div>";
					$s.="<div title='Import Table' class='tableimporticon' style='float:right;padding:0px;' onclick=\"importTable($(this).parent().attr('tbl'))\"></div>";
					//$s.="<div title='Edit Table' class='tableediticon' style='float:right;padding:0px;' onclick=\"editTable($(this).parent().attr('tbl'))\"></div>";
					$s.="<div title='Table Template' class='templateicon' style='float:right;padding:0px;' onclick=\"templateTable($(this).parent().attr('tbl'))\"></div>";
					$s.="<div title='Browse Table' class='browseicon' style='float:right;padding:0px;' onclick=\"browseTable($(this).parent().attr('tbl'))\"></div>";
					$s.="<div title='Table Structure' class='structureicon' style='float:right;padding:0px;' onclick=\"structureTable($(this).parent().attr('tbl'))\"></div>";
					$s.="</td>";

					$s.="</tr>";
				} else {
					$s.="$t<br/>";
				}
			}
			_db()->freeResult($result);
		}
		exit($s);
	} elseif($act=="columnlist" && isset($_REQUEST['tbl'])) {
		$frmt=$_REQUEST["format"];
		$tbls=explode(",",$_REQUEST['tbl']);
		$s="";
		if(strlen($_REQUEST['tbl'])<=0) {
			$o=array("No Tables Selected"=>"");
			printFormattedArray($o);
			exit();
		}
		if(count($tbls)>1) {
			$cols=array();
			foreach($tbls as $a) {
				if(strlen($a)>0) {
					$b=_db()->getColumnList($a);
					$c=array();
					foreach($b as $m=>$n) {
						$c["{$a}.{$m}"]="{$a}.{$m}";
					}
					$cols=array_merge($cols,$c);
				}
			}
			foreach($cols as $a) {
				$t=$a;
				if($frmt=="select") {
					$s.="<option value='$t'>$t</option>";
				} elseif($frmt=="table") {
					$s.="<tr><td align=center style='padding-right:10px;' class='tblcheckbox'><input type=checkbox name='$t' />";
					$s.="</td><td height=22px><b class='tblname'>$t</b></td></tr>";
				} else {
					$s.="$t<br/>";
				}
			}
		} else {
			$sql="SHOW COLUMNS FROM {$_REQUEST['tbl']}";
			$result=_dbQuery($sql);
			if($result) {
				while($row = mysql_fetch_array($result,MYSQL_NUM)) {
					$t=$row[0];
					$n=str_replace("_"," ",$t);
					$n=ucwords($n);
					if($frmt=="select") {
						$s.="<option value='$t'>$n</option>";
					} elseif($frmt=="table") {
						$s.="<tr><td align=center style='padding-right:10px;' class='tblcheckbox'><input type=checkbox name='$t' />";
						$s.="</td><td height=22px><b class='tblname'>$t</b></td></tr>";
					} else {
						$s.="$t<br/>";
					}
				}
				_db()->freeResult($result);
			}
		}
		exit($s);
	} elseif($act=="viewblob") {
		viewBlobData();
		exit();
	}
	if(isset($_REQUEST["page"]) && isset($_REQUEST["dbsessid"])) {
		$f=checkModule("dbedit");
		$f=dirname($f)."/cmds/{$_REQUEST["page"]}.php";
		if(file_exists($f)) {
			include $f;
			exit();
		} else {
			exit("Page Command Not Found");
		}
	}
}
printErr("WrongFormat");
exit();

function viewBlobData() {
	$dbtbl=$_REQUEST["dbtbl"];
	$col=$_REQUEST["col"];
	$idcol=$_REQUEST["idcol"];
	$id=$_REQUEST["id"];
	//$sql="SELECT ";
	if(strpos($col,"_data")>1) {
		$name=explode("_",$col);
		$name=$name[0];

		$sql="SHOW COLUMNS FROM $dbtbl";
		$r=_dbQuery($sql);
		if($r) {
			$a=_dbData($r);
			_db()->freeResult($r);
			$typeCol=false;
			foreach($a as $x) {
				if(isset($x['Field'])) {
					if($x['Field']=="{$name}_type") {
						$typeCol=true;
						break;
					}
				}
			}
			if($typeCol) {
				$tCol="{$name}_type";
				$sql="SELECT $col,$tCol FROM $dbtbl WHERE $idcol=$id";
				$r=_dbQuery($sql);
				if($r) {
					$a=_dbData($r);
					_db()->freeResult($r);

					header("Content-type: {$a[0][$tCol]}");
					header("Content-Transfer-Encoding: binary\n");
					header("Expires: 0");
					echo $a[0][$col];
					exit();
				} else {
					exit("Could Not Find The Data");
				}
			} else {
				$sql="SELECT $col FROM $dbtbl WHERE $idcol=$id";
				$r=_dbQuery($sql);
				if($r) {
					$a=_dbData($r);
					_db()->freeResult($r);
					echo "<textarea style='width:99%;height:98%;border:1px solid #eee;resize:none;' readonly>";
					echo $a[0][$col];
					echo "</textarea>";
				} else {
					exit("Could Not Find The Data");
				}
			}
		} else {
			exit("Could Not Find The DataSource");
		}
	} else {
		$sql="SELECT $col FROM $dbtbl WHERE $idcol=$id";
		$r=_dbQuery($sql);
		if($r) {
			$a=_dbData($r);
			_db()->freeResult($r);
			echo "<textarea style='width:99%;height:98%;border:1px solid #eee;resize:none;' readonly>";
			echo $a[0][$col];
			echo "</textarea>";
		} else {
			exit("Could Not Find The Data");
		}
	}
}

function printResultTable($result, $toolBtns=array(),$parseColType=true,$allowCaption=true) {
	if($result==null || is_bool($result)) return "";
	$header="";
	$body="";
	$cnt=mysql_num_rows($result);
	$cols=mysql_num_fields($result);
	if($cnt<=0) return "";
	$i=0;
	$header.="<tr>";
	if(sizeOf($toolBtns)>0) $header.="<td width=40px>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
	while ($i < mysql_num_fields($result)) {
		$meta = mysql_fetch_field($result, $i);
		if($meta) {
			$t=str_replace("_"," ",$meta->name);
			$t=ucwords($t);
			$header.="<td>$t</td>";
		} else {
			$header.="<td>&nbsp;</td>";
		}
		$i++;
	}
	$header.="</tr>";
	while($row = mysql_fetch_array($result,MYSQL_NUM)) {//MYSQL_ASSOC, MYSQL_NUM, and MYSQL_BOTH
		$body.="<tr id='ROW_".$row[0]."'>";
		if(sizeOf($toolBtns)>0) {
			$body.="<td align=center>";
			foreach($toolBtns as $a=>$b) {
				$body.="<div class='tbltoolbtn $a' onclick=\"$b\"></div>";
			}
			$body.="</td>";
		}
		$c=0;
		foreach($row as $a=>$b) {
			$rowData=$b;
			$meta=mysql_fetch_field($result, $c);
			$type=$meta->type;
			$name=$meta->name;
			$tbl=$meta->table;
			if($type=="date") {
				$df=str_replace("yy","Y",getConfig("DATE_FORMAT"));
				if($b=="0000-00-00" || $b==null) $b="NA";
				else $b=date($df,strtotime($b));
			}
			$clz=$meta->name;
			if($parseColType) {
				if($meta->primary_key) $body.="<td class='serial_col {$clz}' key='".$meta->name."' val='{$rowData}' >$b</td>";
				else {
					if($type=="date") {
						if($b=="NA") {
							$body.="<td class='calerroricon {$clz}' >&nbsp;</td>";
						} else {
							$body.="<td class='{$clz}' align=center>$b</td>";
						}
					} elseif($type=="int" || $type=="float" || $type=="double") {
						$body.="<td class='{$clz}' align=right>$b</td>";
					} elseif($type=="bool" || $type=="boolean") {
						$body.="<td class='{$clz}' align=center>$b</td>";
					} elseif($type=="blob") {
						$body.="<td title='Click To View' class='blobicon {$clz}' onclick=\"viewBlobData('$name','$tbl',$(this).parents('tr').find('td.serial_col').attr('key'),$(this).parents('tr').find('td.serial_col').attr('val'))\">&nbsp;</td>";
					} else {
						$body.="<td class='{$clz}'>$b</td>";
					}
				}
			} else $body.="<td class='{$clz}'>$b</td>";
			$c++;
		}
		$body.="</tr>";
	}
	if($allowCaption) return "<caption>Query Result Set [$cnt Records][$cols Columns]</caption><thead>$header</thead><tbody>$body</tbody>";
	else return "<thead class='ui-widget-header'>$header</thead><tbody>$body</tbody>";
}
?>
