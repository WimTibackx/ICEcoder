<?php
include("lib/settings.php");
$allowedIP = false;
for($i=0;$i<count($_SESSION['allowedIPs']);$i++) {
	if ($_SESSION['allowedIPs'][$i]==$_SERVER["REMOTE_ADDR"]||$_SESSION['allowedIPs'][$i]=="*") {
		$allowedIP = true;
	}
}
if (!$allowedIP) {
	header('Location: /');
};

// Test for latest CodeMirror version
if ($testcMVersion) {
	$cMLatestVer = json_encode(file_get_contents("http://codemirror.net/latest-version.txt"));
	$cMLatestVer = rtrim(ltrim($cMLatestVer,"\""),"\"\\n");
	if ($cMThisVer<$cMLatestVer) {
		echo '<script>alert(\'Code Mirror '.$cMLatestVer.' now released\n\nPlease upgrade\');</script>';
	}
}
?>
<!DOCTYPE html>

<html>
<head>
<title>ICE Coder - <?php echo $versionNo;?></title>
<link rel="stylesheet" type="text/css" href="lib/coder.css">
<script>
shortURLStarts = "<?php echo $shortURLStarts;?>";
theme = "<?php if ($theme=="default") {echo 'icecoder';} else {echo $theme;};?>";
tabsIndent = <?php if ($tabsIndent) {echo 'true';} else {echo 'false';};?>;
<?
$docRoot = str_replace("\\","/",$_SERVER['DOCUMENT_ROOT']);
if (strrpos($docRoot,"/")==strlen($docRoot)-1) {$docRoot = substr($docRoot,0,strlen($docRoot)-1);};
echo 'fullPath = "'.$docRoot.'";'.PHP_EOL;
?>
window.onbeforeunload = function() {
	for (var i=0; i<=top.ICEcoder.changedContent.length; i++) {
		if (top.ICEcoder.changedContent[i]==1) {
			return "You have some unsaved changes.";
		}
	}
}

lastOpenFiles = [<?php
	if ($lastOpenedFiles!="" && $_SESSION['userLevel'] == 10) {
		$openFilesArray = explode(",",$lastOpenedFiles);
		for ($i=0;$i<count($openFilesArray);$i++) {
			echo "'".$openFilesArray[$i]."'";
			if ($i<count($openFilesArray)-1) {echo ",";};
		}
	}
?>];
</script>
<script language="JavaScript" src="lib/coder.js"></script>
</head>

<body onLoad="ICEcoder.init(<?php if ($_SESSION['userLevel'] == 10) {echo "'login'";} ?>)<?php echo $onLoadExtras;?>" onResize="ICEcoder.setLayout()" onMouseMove="top.ICEcoder.getMouseXY(event);top.ICEcoder.canResizeFilesW()" onMouseDown="top.ICEcoder.mouseDown=true" onMouseUp="top.ICEcoder.mouseDown=false" onKeyDown="return ICEcoder.interceptKeys('coder', event);" onKeyUp="parent.ICEcoder.resetKeys(event);">

<div id="blackMask" class="blackMask" onClick="ICEcoder.showHide('hide',this)">
	<div class="popupVCenter">
		<div class="popup" id="mediaContainer"></div>
	</div>
</div>


<div id="loadingMask" class="blackMask" style="visibility: visible">
	<span class="progressBar"></span>
	<div class="popupVCenter">
		<div class="popup">
			<div class="circleOutside"></div>
			<div class="circleInside"></div>
			&nbsp;&nbsp;&nbsp;working...
		</div>
	</div>
</div>

<div id="fileMenu" class="fileMenu" onMouseOver="ICEcoder.changeFilesW('expand')" onMouseOut="ICEcoder.changeFilesW('contract')">
	<span id="folderMenuItems">
		<a href="javascript:top.ICEcoder.newFile()" onMouseOver="document.getElementById('fileMenu').style.display='inline-block'">New File</a>
		<a href="javascript:top.ICEcoder.newFolder()" onMouseOver="document.getElementById('fileMenu').style.display='inline-block'">New Folder</a>
	</span>
	<a href="javascript:top.ICEcoder.deleteFile(top.ICEcoder.rightClickedFile)" onMouseOver="document.getElementById('fileMenu').style.display='inline-block'">Delete</a>
	<span id="singleFileMenuItems">
		<a href="javascript:top.ICEcoder.renameFile(top.ICEcoder.rightClickedFile)" onMouseOver="document.getElementById('fileMenu').style.display='inline-block'">Rename</a>
		<a href="javascript:window.open(top.ICEcoder.rightClickedFile.substr((top.ICEcoder.rightClickedFile.indexOf(shortURLStarts)+top.shortURLStarts.length),top.ICEcoder.rightClickedFile.length))" onMouseOver="document.getElementById('fileMenu').style.display='inline-block'">View Webpage</a>
	</span>
</div>

<div id="header" class="header" onContextMenu="return false">
	<div class="plugins" id="pluginsContainer">
	<?php echo $pluginsDisplay; ?>
	</div>
	<div class="version"><?php echo $versionNo;?></div>
	<img src="images/ice-coder.gif" class="logo" onContextMenu="ICEcoder.settingsScreen('show')">
</div>

<div id="files" class="files" onMouseOver="ICEcoder.changeFilesW('expand')" onMouseOut="ICEcoder.changeFilesW('contract'); top.document.getElementById('fileMenu').style.display='none';">
	<div class="account" id="account">
		<div class="accountLoginContainer" id="accountLoginContainer">
			<div class="accountLogin" id="accountLogin">
				<form name="login" action="lib/settings.php" method="POST" target="ff">
				<input type="password" name="loginPassword" class="accountPassword">
				<input type="submit" name="submit" value="Login" class="button">
				</form>
			</div>
		</div>
		<div class="accountOptions">
			<a nohref title="Save" onClick="ICEcoder.fMIcon('save')"><img src="images/save.png" alt="Save" id="fMSave" style="opacity: 0.3"></a>
			<a nohref title="Open" onClick="ICEcoder.fMIcon('open')"><img src="images/open.png" alt="Open" id="fMOpen" style="margin-left: 7px; opacity: 0.3"></a>
			<a nohref title="New File" onClick="ICEcoder.fMIcon('newFile')"><img src="images/new-file.png" alt="New File" id="fMNewFile" style="margin: 8px 0px 0px 10px; opacity: 0.3"></a>
			<a nohref title="New Folder" onClick="ICEcoder.fMIcon('newFolder')"><img src="images/new-folder.png" alt="New Folder" id="fMNewFolder" style="margin: 9px 0px 0px 5px; opacity: 0.3"></a>
			<a nohref title="Delete" onClick="ICEcoder.fMIcon('delete')"><img src="images/delete.png" alt="Delete" id="fMDelete" style="margin: 9px 0px 0px 5px; opacity: 0.3"></a>
			<a nohref title="Rename" onClick="ICEcoder.fMIcon('rename')"><img src="images/rename.png" alt="Rename" id="fMRename" style="margin: 9px 0px 0px 5px; opacity: 0.3"></a>
			<a nohref title="View" onClick="ICEcoder.fMIcon('view')"><img src="images/view.png" alt="View" id="fMView" style="margin: 9px 0px 0px 5px; opacity: 0.3"></a>
		</div>
		<a nohref style="cursor: pointer" onClick="ICEcoder.lockUnlockNav()"><img src="images/file-manager-icons/padlock.png" id="fmLock" class="lock"></a>
	</div>
	<iframe id="filesFrame" class="frame" name="ff" src="files.php" style="opacity: 0" onLoad="this.style.opacity='1'"></iframe>
	<div class="serverMessage" id="serverMessage"></div>
</div>

<div id="editor" class="editor">
	<div id="tabsBar" class="tabsBar" onContextMenu="return false">
		<?php
		for ($i=1;$i<=10;$i++) {
			echo '<div id="tab'.$i.'" class="tab" onClick="if(ICEcoder.canSwitchTabs) {ICEcoder.switchTab('.$i.')} else {ICEcoder.canSwitchTabs=true}"></div>';
		}
		?><div class="newTab" onClick="ICEcoder.newTab()"><img src="images/nav-new.png"></div>
	</div>
	<div id="findBar" class="findBar" onContextMenu="return false">
		<form name="findAndReplace">
			<div class="findReplace">
				<div class="findText">Find</div>
				<input type="text" name="find" value="" id="find" class="textbox find" onKeyUp="ICEcoder.findReplace('find',true,false)">
				<div class="findTextPlural">'s</div>
				<select name="connector" onChange="ICEcoder.findReplaceOptions()">
				<option>in</option>
				<option>and</option>
				</select>
				<div class="replaceText" id="rText" style="display: none">
					<select name="replaceAction" class="replaceAction">
					<option>replace</option>
					<option>replace all</option>
					</select>
					 with
				</div>
				<input type="text" name="replace" value="" id="replace" class="textbox replace" style="display: none">
				<div class="targetText" id="rTarget" style="display: none">in</div>
				<select name="target" onChange="ICEcoder.updateResultsDisplay(this.value=='this document' ? 'show' : 'hide')">
				<option>this document</option>
				<option>open documents</option>
				<option>all files</option>
				<option>all filenames</option>
				</select>
				<input type="button" name="submit" value="&gt;&gt;" class="submit" onClick="ICEcoder.findReplace('findReplace',false,true)">
				<div class="results" id="results"></div>
			</div>
		</form>
		<form onSubmit="return ICEcoder.goToLine()">
		<div class="codeAssist"><input type="checkbox" name="codeAssist" id="codeAssist" checked onClick="top.ICEcoder.codeAssistToggle()">Code Assist</div>
		<div class="goLine">Go to Line<input type="text" name="goToLine" value="" id="goToLineNo" class="textbox goToLine">
		</form>
	</div>
	<iframe name="contentFrame" id="content" src="editor.php" class="code">
	</iframe>
</div>

<div class="footer" id="footer" onContextMenu="return false">
	<div class="nesting" id="nestValid">Nesting OK</div>
	<div class="nestLoc">Cursor nest location</div>
	<div class="nestDisplay" id="nestDisplay"></div>
	<div class="charDisplay" id="charDisplay"><span id="char"></span></div>
</div>

<script>
ICEcoder.setLayout('dontSetEditor');
</script>

</body>

</html>