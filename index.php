<?php
/*
 * This Module helps in Database Administration For MySQL Currently
 *
 * Author: Bismay Kumar Mohapatra bismay4u@gmail.com on 24/02/2012
 * Author: Kshyana Prava kshyana23@gmail.com on 24/02/2012
 * Version: 1.0
 */
if(!defined('ROOT')) exit('No direct script access allowed');
session_check(true);
user_admin_check(true);

_js(array("dialogs"));

function loadDbConsole($site=null) {
	if($site==null) {
		$site=SITENAME;
	}
	checkUserSiteAccess($site);
	$webPath=getWebPath(__FILE__);
	$rootPath=getRootPath(__FILE__);
	$rand=rand(1000,9999)*10000;
	$InstanceID="DBCONSOLE_$rand";
	$_SESSION[$InstanceID."_SITE"]=$site;

	if($site=="admincp" && $_SESSION["SESS_PRIVILEGE_ID"]<=2) {
		$dbFile=ROOT."config/db.cfg";
	} else {
		$dbFile=ROOT.APPS_FOLDER.$site."/config/db.cfg";
	}
	if(file_exists($dbFile)) {
		LoadConfigFile($dbFile);

		if(!(strlen($GLOBALS['DBCONFIG']["DB_DRIVER"])>0 &&
			strlen($GLOBALS['DBCONFIG']["DB_USER"])>0 &&
			strlen($GLOBALS['DBCONFIG']["DB_PASSWORD"])>0 &&
			strlen($GLOBALS['DBCONFIG']["DB_HOST"])>0 &&
			strlen($GLOBALS['DBCONFIG']["DB_DATABASE"])>0)) {

			dispErrMessage("DB Configuration Is Wrong For Site <i style='color:orange;'>$site</i>. <br/>Please correct under <b>Configurations>Database</b>","Wrong DB Configuration",203);
			return;
		}

		$con=new Database($GLOBALS['DBCONFIG']["DB_DRIVER"]);
		$a=$con->connect($GLOBALS['DBCONFIG']["DB_USER"],$GLOBALS['DBCONFIG']["DB_PASSWORD"],$GLOBALS['DBCONFIG']["DB_HOST"],$GLOBALS['DBCONFIG']["DB_DATABASE"]);
		if(!$a) {
			dispErrMessage("DB Configuration Is Not Correct For Site <i style='color:orange;'>$site</i>. <br/>Please correct under <b>Configurations>Database</b>","Wrong DB Configuration",401);
			return;
		}
		$_SESSION[$InstanceID."_CONN"]=$con;
	} else {
		dispErrMessage("DB Configuration Missing For Site <i style='color:orange;'>$site</i>","Missing DB Configuration",204);
		return;
	}
include "plugins.php";

?>
<link href='<?=$webPath?>style.css' rel='stylesheet' type='text/css' media='all' />
<script src='<?=$webPath?>script.js' type='text/javascript' language='javascript'></script>

<div style='width:100%;height:100%;overflow:hidden;'>
<div id=toolbar class="toolbar ui-widget-header">
	<div class='left' style='margin-left:5px;'>
		<?php
			foreach($dbeditplugins as $a=>$b) {
				$pg=$b["page"];
				$title=$b["title"];
				$tips=$b["tips"];
				$icon=$b["icon"];
				echo "<button title='$tips' forpg='#$pg' onclick='showPage(\"#$pg\");'><div class='$icon'>$title</div></button>";
			}
		?>
	</div>
	<div class='right' style='margin-right:5px;'>
		<div id=loadingmsg class='ajaxloading4'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
	</div>
</div>
<div id=workspace class="ui-widget-content" style='width:100%;overflow:auto;'>
	<?php
		foreach($dbeditplugins as $a=>$b) {
			$pg=$b["page"];
			$title=$b["title"];
			$tips=$b["tips"];
			$icon=$b["icon"];
			if($a==0) echo "<div id=$pg class='page active'>";
			else echo "<div id=$pg class='page'>";
			include "pages/$pg.php";
			echo "</div>";
		}
	?>
</div>
</div>
<script>
instanceID="<?=$InstanceID?>";
function getCMD(pg) {
	if(pg==null) pg=page;
	return getServiceCMD("dbedit")+"&page="+pg+"&dbsessid="+instanceID;
}
</script>
<?php } ?>
