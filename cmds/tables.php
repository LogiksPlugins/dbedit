<?php
if (!defined('ROOT')) exit('No direct script access allowed');

if(isset($_REQUEST["action"]) && strlen($_REQUEST["action"])>0) $ops=$_REQUEST["action"];
else {
	printErr("WrongFormat","You Forgot The Operation Command.");
	exit();
}
if(isset($_POST["dbtbl"]) && strlen($_POST["dbtbl"])>0) {
	$dbtbl=$_POST["dbtbl"];
	$dbtbl=explode(",",$dbtbl);
	if(strlen($dbtbl[sizeOf($dbtbl)-1])==0) unset($dbtbl[sizeOf($dbtbl)-1]);
} elseif(isset($_REQUEST["dbtbl"]) && strlen($_REQUEST["dbtbl"])>0) {
	$dbtblX=$_REQUEST["dbtbl"];
	$dbtblX=explode(",",$dbtblX);
	if(sizeof($dbtblX)>1) {
		printErr("WrongFormat","Multiple Tables Are Not Supported In This Operation.");
		exit();
	}
	$dbtblX=$dbtblX[0];
} else {
	printErr("WrongFormat","Data Table For Operation Not Found.");
	exit();
}

$arrCmds=array(
				"checktable"=>array("CHECK TABLE %s","single_run"),
				"analyzetable"=>array("ANALYZE TABLE %s","single_run"),
				"repairtable"=>array("REPAIR TABLE %s","single_run"),
				"optimizetable"=>array("OPTIMIZE TABLE %s","single_run"),
				//"flushtable"=>array("",""),
				"emptytable"=>array("TRUNCATE TABLE %s","multi_run"),
				"droptable"=>array("DROP TABLE %s","multi_run"),
				
				"exporttable"=>array("exportTable","func"),
				"importtable"=>array("importTable","func"),
				"templatetable"=>array("templatizeTable","func"),
			);

if(array_key_exists($ops,$arrCmds)) {
	if($arrCmds[$ops][1]=="single_run") {
		$sql=sprintf($arrCmds[$ops][0],implode(",",$dbtbl));
		$r=_dbQuery($sql);
		if($r) {
			echo "<div style='width:700px;height:350px;overflow:auto;' ><table width=100% cellspacing=0>";
			echo printResultTable($r,null,false,false);
			echo "</table></div>";
		}
	} elseif($arrCmds[$ops][1]=="multi_run") {
		foreach($dbtbl as $tbl) {
			if(strlen($tbl)>0) {
				$sql=sprintf($arrCmds[$ops][0],$tbl);
				$r=_dbQuery($sql);
				if($r) {
					$s=printResultTable($r,null,false,false);					
					if(strlen($s)>0) echo "<table width=100%>$s</table>";
				}
			}
		}
	} elseif($arrCmds[$ops][1]=="func") {
		call_user_func($arrCmds[$ops][0],$dbtblX);
	}
	exit();
} else {
	printErr("TypeNotFound","Recquired Command $ops Not Found.");
	exit();
}

function exportTable($tbl) {
	ob_start("ob_gzhandler");
	header('Content-type: text/comma-separated-values');
	header("Content-Disposition: attachment; filename=db_{$tbl}_".date('YmdHis').'.csv');
	CSVToDBExport::export($tbl);
	ob_end_flush();
}
function importTable($tbl) {
	$file=$_FILES["csvfile"]['tmp_name'];
	$tbl=$_POST["dbtbl"];
	if(file_exists($file)) {
		$a=CSVToDBImport::importCSV($tbl,$file);
		if(is_array($a) && isset($a["error"])) {
			echo $a["error"];
		} else {
			echo "<script>parent.lgksAlert('Data Imported Into $table Successfully');parent.closeCSVDlg();</script>";
		}
	}
}
function templatizeTable($tbl) {
	ob_start("ob_gzhandler");
	header('Content-type: text/comma-separated-values');
	header("Content-Disposition: attachment; filename=db_{$tbl}_".date('YmdHis').'.csv');
	CSVToDBExport::exportHeader($tbl);
	ob_end_flush();
}
class CSVToDBExport {
	private static $SPECIAL_COLS=array("userid","privilegeid","scanBy","submittedby","createdBy","site","doc","doe","toc","toe","dtoc","dtoe","last_modified");
	/* This function is used to dump mysql table data in csv format
	 * @table the table whose data is to be dumped in a csv file
	 * */
	public static function export($table,$delimiter=",") {
		$sql="select * from $table";
		$maxReset=100;
		$result=_dbQuery($sql);
		if($result){
			$num_rows=_db()->recordCount($result);
			$num_fields=_db()->columnCount($result);
			
			$i=0;
			while($i<$num_fields){
				$meta=_db()->fetchField($result,$i);
				echo $meta->name;
				if($i<$num_fields-1){
					echo $delimiter;
				}
				$i++;
			}
			echo "\n\n";
			$cnt=0;
			if($num_rows>0){
				while($row=mysql_fetch_array($result)){
					for($i=0;$i<$num_fields;$i++){
						echo mysql_real_escape_string($row[$i]);
						if($i<$num_fields-1){
							echo $delimiter;
						}
					}
					echo "\n";
				}
				$cnt++;
				if($cnt==$maxReset) {
					$cnt=0;
					ob_flush();
				}
			}
			_dbFree($result);	
		}	
	}
	public static function exportHeader($table,$delimiter=",") {
		$sql="select * from $table limit 1";
		$result=_dbQuery($sql);
		if($result) {
			$num_fields=_db()->columnCount($result);
			
			$i=0;
			$s="";
			while($i<$num_fields) {
				$meta=_db()->fetchField($result,$i);
				if(!in_array($meta->name,CSVToDBExport::$SPECIAL_COLS)) {
					$s.=$meta->name;
					if($i<$num_fields-1){
						$s.=$delimiter;
					}
				}
				$i++;
			}
			_dbFree($result);
			$s=trim($s);
			if(substr($s,strlen($s)-1)==",") $s=substr($s,0,strlen($s)-1);
			echo "{$s}\n\n";
		}
	}
}

class CSVToDBImport {
	/*
	 * @file csv file to be imported
	 * @unique_col primary key of the table
	 * @headers Array that contains the header of csv file /column names
	 * 
	 **/
	
	public static function importCSV($table,$file,$unique_col='id') {
		if($file==null || strlen($file)==0 || !file_exists($file)) return array("error"=>"File Does Not Exist");
		if(!is_readable($file)) return array("error"=>"File Is Not Readable");
		
		$csvImport=new CSVToDBImport();
		$fp=fopen($file,'r');
		
		$headers = fgetcsv($fp, 2048, ',');
		$no_headers=sizeof($headers);
		if($unique_col!=null && strlen($unique_col)>0 && in_array($unique_col,$headers)){
			$index=array_search($unique_col,$headers);
			$sql="SELECT $unique_col from $table";	
			$res=_dbQuery($sql);
			if($res){
				$unique_data=array();
				while($rec=_db()->fetchData($res)){
					$unique_data[sizeof($unique_data)]=$rec[$unique_col];
				}
			}
			while($data = fgetcsv($fp, 2048, ',')) {
				if(sizeOf($data)<=0) continue;
				elseif(sizeOf($data)==1 && strlen($data[0])==0) continue;
				if(sizeof($data) != sizeof($headers)) {//if the no of columns are not equal to data count than this is not a proper csv data
					return array("error"=>"No of columns are not equal to data count than this is not a proper csv data");
				} else{
					if(!in_array($data[$index],$unique_data)) {
						$csvImport->insertCSVData($data,$headers,$table);
					} else{
						$csvImport->updateCSVData($data,$headers,$table,$index,$unique_col);				
					}
				}
			}
		} else {
			while($data = fgetcsv($fp, 2048, ',')){
				if(sizeof($data) != sizeof($headers)) {
					return array("error"=>"No of columns are not equal to data count than this is not a proper csv data");
				} else {
					$csvImport->insertCSVData($data,$headers,$table);
				}
			}
		}
		fclose($fp);
		return true;
	}
	private function insertCSVData($data,$headers,$table) {
		$str=implode(",",$headers);
		$sql="INSERT INTO $table ($str) VALUES(";
		foreach($data as $k=>$v){
			$sql .="'".mysql_real_escape_string($v)."',";					
		}
		$sql=substr($sql,0,strlen($sql)-1);
		$sql .=");";
		//echo $sql."<br>";
		_dbQuery($sql);
	}
	private function updateCSVData($data,$headers,$table,$index,$idCol){
		$sql="UPDATE $table SET ";
		for($j=0;$j<sizeof($data);$j++){
			if($headers[$j] !='id'){
				$sql .=$headers[$j] ."= '".mysql_real_escape_string($data[$j])."',";						
			}															
		}
		$sql=substr($sql,0,strlen($sql)-1);
		$sql .=" WHERE $idCol ='".$data[$index]."';";
		//echo $sql."<br>";
		_dbQuery($sql);
	}
}


?>









