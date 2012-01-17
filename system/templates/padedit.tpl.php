<?php //

/*
 * PadEdit
 *
 * Copyright (c) 2010 Honest Code.
 * Licensed under the GPL license.
 * http://www.gnu.org/licenses/gpl.txt
 *
 * Date: 2010-12-25
 * Version: 1.3-1.0 Alpha
 *
 */

//if this page is trying to be accessed directly, exit.
if (!defined('PADEDIT_VERSION')){
	exit;
}

//the following head is used regardless of the action in order to streamline this script.
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- The following META AND LINK tags are for turning this into a native-looking APP-->

                <link rel="apple-touch-icon" href="./apple-touch-icon.png" /> <!-- pick one or make one -->
                <link rel="apple-touch-startup-image" href="./startup.png" /> <!-- must be 1024x768 -->
                <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
                <meta name="apple-mobile-web-app-capable" content="yes" />
                <link href="system/styles.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>

	<title>myPadEdit <?php if (isset($_GET['file'])) echo " &middot; ". $_GET['file']; echo " &middot; " . $_SERVER['HTTP_HOST'];  ?></title>
	<script type="text/javascript" src="system/js/jquery-linedtextarea.js"></script>
	<script type="text/javascript" src="system/js/editor.js"></script>
	<script type="text/javascript">
		var ipad = <?php if (strpos($_SERVER['HTTP_USER_AGENT'], "iPad")) { echo "true"; $ipad = true; } else { echo "false"; $ipad = false; } ?>;
		<?php 	if (isset($_GET['file'])) { 
				echo "var thisfile = '". substr($_GET['file'],0,strpos($_GET['file'], ".")) . "';"; 
			} else {
				echo "var thisfile = 'filelist';"; 
			}?> 
	</script>
	<style type="text/css">
		<?php 	if (isset($_COOKIE["pnl"])) {
				$panelwd = explode("^", $_COOKIE["pnl"]);
				echo ('#sidebar { width: '.$panelwd[0].'%; } ');
				echo ('#editor  { width: '.$panelwd[1].'%; } ');
			}
		?>
	</style>
	
	</head>
	<body>
	
	<div id="container">
		<div id="sidebar">
			<h1><?php if ($path != "/var/www/") { $pos = strrpos(substr($path, 0, -1), "/"); 
					?><a href="index.php?path=<?php echo substr($path, 0, $pos);?>/" class="btn"><img src="system/images/arrow.svg" alt="Parent Folder" title="Parent Folder" width="14" height="14" /></a><?php
				 } 

$displaypath = $path;
//					$displaypath = substr($path, strpos($path, "../")+3); 
					if ($displaypath) { echo $displaypath; } else { echo "&nbsp;"; } ?></h1>
			<div class="toolbar">
				<div id="resize" unselectable="on" onclick="void(0)">&nbsp;</div>
				<a href="#" id="newfolder"><img src="system/images/newfolder.svg" alt="new folder" title="New Folder" width="41" height="41" /></a>
				<a href="#" id="newfile"><img src="system/images/newfile.svg" alt="new file" title="New File" width="41" height="41" /></a>
				<?php if (!$ipad) { ?><a href="#" id="upload"><img src="system/images/upload.svg" alt="upload" title="Upload File" width="41" height="41" /></a> <?php } ?>
			</div>
			<?php include("system/filelist.php"); ?>
			<?php 
				$thisb = array();
				foreach ($backups as $b) {
					$info = explode("_", $b['name']); 
					if ($info[3] == $_GET['file']) {
						array_push($thisb, $b);
					}
				}
			 ?>
		</div> <!-- sidebar -->
		<div id="editor">
			<h1 class="inactive"><?php if (isset($_GET['file'])) { echo $_GET['file']; } else { echo "myPadEdit " . $version; } ?></h1>
			
			<div class="toolbar" style="text-align:center">
				<span style="float: left; line-height: 41px;">
					<?php if (!$safety && $fileEditable ) { ?>
					<a href="#" id="save"><img src="system/images/save.svg" alt="Save" title="Save" /></a> 
					<?php } ?>
					<?php if (!$safety && $fileLoaded && $fileEditable) { ?>
					<a href="<?php echo $_GET['path'].$_GET['file'];?>" target="_blank"><img src="system/images/open.svg" alt="View" title="View" /></a>
					<?php } ?>
				</span>
				<span style="margin-left: -41px;">
					<?php if (!$safety && $fileLoaded && $fileEditable) { ?>
					<a href="#" id="delete"><img src="system/images/delete.svg" alt="Delete" title="Delete" /></a>
					<?php } ?>
					<?php if (!$safety && $fileLoaded && $fileEditable) { ?>
					<a href="#" id="rename"><img src="system/images/rename.svg" alt="Rename" title="Rename" /></a>
					<?php } ?>
					<?php if (!$safety && $fileEditable ) { ?>
					<a href="#" id="snip"><img src="system/images/snippets.svg" alt="Snippets" title="Snippets" /></a>
					<?php } ?>
					<?php if (count($thisb)) { ?>
					<a href="#" id="restore"><img src="system/images/restore.svg" alt="Restore an old version" title="Restore an old version" /></a>
					<?php } ?>
				</span>
				<span style="float: right; line-height: 41px;">
<span style="padding:5px;border:solid 3px #444;background:#aaa;color:#444">
<?php if (!isset($root)) { 
print'<a href="'.$_SERVER["PHP_SELF"].$root'" class="button">WEBUSER</a>';
WEBUSER';}else{print'ROOT';} ?>
</span>

<!--					<a href="index.php?logout=true"><img src="system/images/logout.svg" alt="logout" title="Log out"/></a>-->
				</span>
			</div>
			
			<?php if (isset($message) or $safety) { ?>
				<div id="message">
					<?php if (isset($message)) { echo $message; } ?>
					<?php if ($safety) { echo "Sorry, but that file is protected. <a href='".$_SERVER['PHP_SELF']."&user=root'>Become Root?</a>"; } ?>
				</div>
			<?php } ?>
			<?php if (isset($_GET['image'])) { ?>			
				<div align="center" style="margin: 20px auto;"><img src="<?php echo $_GET['path'].$_GET['file'];?>" alt="<?php echo $_GET['file'] ?>"></div>
			<?php } else if (!$safety) { ?>
				<form name="editor" id="editform" action="index.php?path=<?php echo $_GET['path'] ?>&amp;file=<?php echo $_GET['file'] ?>&amp;save=true"<?php print $root ?> method="post">
					<textarea name="filetxt" id="filetxt" cols="25" rows="200"><?php if ($editfile) { echo $editfile; } ?></textarea>
				</form>
				<br style="clear:both;" />
			<?php } ?>
		</div> <!-- editor -->
	</div> <!-- container -->
	
	<!-- popups -->
	
	<div id="newfolderinfo" class="popup" style="display:none;">
		<form action="index.php?path=<?php echo $_GET['path'];?>&amp;newfolder=true<?php print $root ?>" method="post">
			<input id="newfoldername" name="newfoldername" type="text"/>
			<input name="submit" type="submit" value="Create New Folder"/> &nbsp; <a href="#" id="newfoldercancel" style="float:right; margin-top: 3px;">Cancel</a>
		</form>
	</div>
	<div id="newfileinfo" class="popup" style="display:none;">
		<form action="index.php?path=<?php echo $_GET['path'];?>&amp;newfile=true<?php print $root ?>" method="post">
			<input id="newfilename" name="newfilename" type="text"/>
			<input name="submit" type="submit" value="Create New File"/> &nbsp; <a href="#" id="newfilecancel" style="float:right; margin-top: 3px;">Cancel</a>
		</form>
	</div>
	<div id="uploadfileinfo" class="popup" style="display:none;">
		<form action="index.php?path=<?php echo $_GET['path'];?>&amp;upload=true<?php print $root ?>" enctype="multipart/form-data" method="post">
			<input name="uploadfile" type="file"/>
			<input name="submit" type="submit" value="Upload"/> &nbsp; <a href="#" id="uploadcancel" style="float:right; margin-top: 3px;">Cancel</a>
		</form>
	</div>
	
	<div id="areyousure" class="popup" style="display: none;">
		<strong style="color: #ffe856;">This file will be deleted permanently. </strong>
		<a href="#" id="deleteCancel" style="float:right;">Cancel</a> 
		<a href="index.php?path=<?php echo $_GET['path'] ?>&amp;file=<?php echo $_GET['file'] ?>&amp;delete=true<?php print $root ?>" class="doDelete">Delete</a>
	</div>
	
	<div id="renamefile" class="popup" style="display: none;">
		<form action="index.php?path=<?php echo $_GET['path'] ?>&amp;file=<?php echo $_GET['file'] ?>&amp;rename=true<?php print $root ?>" method="post">
			<input id="filename" name="filename" type="text" value="<?php echo $_GET['file'] ?>"/>
			<input name="submit" type="submit" value="Rename File"/> &nbsp; <a href="#" id="renameCancel" style="float:right; margin-top: 3px;">Cancel</a>
		</form>
	</div>
	
	<div id="snippets" class="popup" style="display: none;">
		<p style="margin-bottom: 20px; float: right;"><a href="#" id="snippetsCancel" style="margin-left:0; margin-top: 3px;">Cancel</a></p>
		<form action="" method="post">
			Clips XML File URL: <input id="snipfile" name="filename" type="text" value="<?php echo $_COOKIE["snip"];?>"/> <a href="http://honestcode.com/padedit/#howdoiimportmycodaclips" target="_blank"><strong>?</strong></a>
			<input id="snipimport" name="submit" type="button" value="Import Snippets"/>
		</form>
	</div>
	
	<div id="restorefile" class="popup" style="display: none;">
		<?php	if (count($thisb)) {
				echo '<p style="margin-bottom: 20px; float: right;"><a href="#" id="restoreCancel" style="margin-left:0; margin-bottom: 20px;">Cancel</a></p>'; 
				echo '<br style="clear:both;" />';
				echo "<table>";  
				foreach ($thisb as $b) {
					$info = explode("_", $b['name']); 
					?>
					<tr id="v<?php echo $info[2];?>">				
					<td>Version saved <?php echo date("Y F j, g:i A", $info[2]);?></td>
					<td align="right"><a class="viewbackup" rel="v<?php echo $info[2];?>" href="<?php echo $_GET['path'].$b['name'];?>">View</a></td>
					<td align="right"><a href="index.php?path=<?php echo $_GET['path'] ?>&amp;file=<?php echo $_GET['file'] ?>&amp;restore=<?php echo $info[2];?><?php print $root ?>">Restore</a></td>
					</tr>
			<?php	}
			echo "</table>"; 
			} ?>
	</div>
	
	<!-- end popups -->

	</body>
	</html>