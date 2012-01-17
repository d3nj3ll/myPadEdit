<?php 
error_reporting(0);
// %protect%
/**
 * PadEdit
 *
 * Copyright (c) 2010 Honest Code && (c) 2012 D3NJ3LL
 * Licensed under the GPL license.
 * http://www.gnu.org/licenses/gpl.txt
 *
 * Date: 2012-1-16
 * Version: 1.3-1.0 alpha
 *
 */
 
//define a version, we can also use this to prevent direct access to component scripts.
define('PADEDIT_VERSION', '1.3-1.0 alpha');
$version = PADEDIT_VERSION;

//set some values to avoid undefined notices
$editfile = null;
$safety = false;

//Start session -> do we need this anymore???
session_set_cookie_params('0','/', null , false, true); 
session_start(); 

// switch it up if user wants root-like access
if ($_GET['user'] == 'root'){
  // might be nice to force some kind of challenge like password etc.
  global $root;
  $root = '&user=root';
  //  require_once('system/root.php'); //extra features like BASH, moving up the ../../ ladder etc.
}


require_once("system/functions.php"); // core system object
$p = new padedit;

//controller section. works out what the user is trying to do and performs the necessary functions
// things we can only do if we are logged in
$loggedin=1;
// -> is carried over for the time being, will migrate to localhost.php and server.php, perhaps base.php
if ($loggedin){

	// save file
    if (isset($_GET['save'])) { 
    	$message = $p->saveFile();
    }
    
    // restore file from a backup
    if (isset($_GET['restore'])) { 
    	$message = $p->restoreBackup();
    } 
    
    // delete a file.
    if (isset($_GET['delete'])) { 
    	$message = $p->deleteFile();
    }
    
    // create a new blank file.
    if (isset($_GET['newfile'])) { 
    	$message = $p->createFile();
    }
    
    // create a new empty folder.
    if (isset($_GET['newfolder'])) { 
    	$message = $p->createFolder();
    }
    
    // rename a file.
    if (isset($_GET['rename'])) { 
    	$message = $p->renameFile();
    } 
    
    // upload a file.
    if (isset($_GET['upload'])) { 
    	$message = $p->uploadFile();
    }

	// logout    
    if (isset($_GET['logout'])) {
		$p->logout();
	}
    
    // confirmation messages
    if (isset($_GET['saved'])) {
    	$message = "File saved. <a href='".$_GET['path'].$_GET['file'].$root."' target='_blank'>View file.</a>";
    }
    
    if (isset($_GET['deleted'])) {
    	$message = "File deleted.";
    }
    
    if (isset($_GET['renamed'])) {
    	$message = "File renamed. <a href='".$_GET['path'].$_GET['file'].$root."' target='_blank'>View file.</a>";
    }
    
    if (isset($_GET['foldered'])) {
    	$message = "Folder created.";
    }
    
    if (isset($_GET['restored'])) {
    	$message = "Backup restored.";
    }
    
	// end confirmation messages
    
    //display editor
	$action = 'editor';
	
	//make sure we have a path
    if (isset($_GET['path'])) { 
    	$path = $_GET['path']; 
    } elseif (isset($root)) {
        $path = "/";
    } else {
    	$path = "/var/www";  
    }
    if (!$p->checkPath($path)) { 
    	header("Location: index.php?path=/var/www"); 
    	exit;
    } 
    
    //if the user wants to load a file
	if (isset($_GET['file']) and isset($_GET['path'])) {
		//get file details 
		$fileDetails = $p->getFileDetails($_GET['path'],$_GET['file']);
	}

	//check if they should be editing this file

/*
also an important feature, that I would sometimes like to override.
see notes above.
*/
  if (!isset($root)) {

	if ($fileDetails['protected'] == true ) {
		$safety = true;
		$fileLoaded = false;
	} else {
		$safety = false;
		$fileLoaded = true;
		$editfile =  $fileDetails['source'];
	}
	
	if ($fileDetails['is_file']  && $fileDetails['is_writable']){
		//Need to check the type of file and only allow files that can be edited to be loaded in editor.
                //Unless we are root, then fuck off.
		$fileEditable = $p->canEdit($fileDetails['extension']);

		if (!$fileEditable){
    		//$safety = true;
    		$fileLoaded = false;
    		$editfile =  '';
    		$message = "Sorry, You can't edit this type of file."; // 

		}
	}
   } else {
        $safety=false;
	$fileLoaded = true;
	$editfile =  $fileDetails['source'];
   }
}

// load the template
require_once("system/templates/padedit.tpl.php");
?>