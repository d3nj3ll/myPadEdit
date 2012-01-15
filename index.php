<?php // %protect%
/**
 * PadEdit
 *
 * Copyright (c) 2010 Honest Code.
 * Licensed under the GPL license.
 * http://www.gnu.org/licenses/gpl.txt
 *
 * Date: 2012-1-15
 * Version: 1.3-1
 *
 */
 

/*
/ Do we need this kind of pseudo security on our own device?
/ It is unnecesary codebulk that inludes extra files. Weg damit.

/ LIGHTTD CONFIG ISSUES
 PHP must work.
 To be secure, it is better to write the server configs securely.
 This may mean using a certain port for root and otherwise doing it with just webuser...
 use service patching in the lighttd config file:
 http://localhost:8120/Applications/81FB3553-07D0-4611-9A6A-CF49AF43E96B/?newfolder=assets&chmod=755
 can use the device id as a cross-check to find out if browser on localhost isIpad()? 

PERFORMANCE ISSUES
/ Write some pragmacache headers to prevent reloading the page EVER
/ Put useless code in other include files so the tree can remain stable .
/ Remember what denjell says: "A cycle saved is cache earned."
/ NOTIFY WINDOW OF ITS REALM AND USER

/ add a GET_['realm'] key for something like: 
 http://localhost:8120/?realm=/Applications/81FB3553-07D0-4611-9A6A-CF49AF43E96B/

/ TOOLBASE EXPANSION -> using bash server
 git -clone a repository 
 chmod 755 $file 
 spin up another lighttd server for root / other user / other realm
 curl website, script etc.
*/ 


/*
//check for PHP 5 --> is no longer needed as because it is done in the make process.
$phpversion = floatval(phpversion());
if ($phpversion < 5) {
	die("Sorry, but PHP version 5 or above is required to use PadEdit.");
}
*/

//define a version, we can also use this to prevent direct access to component scripts.
define('PADEDIT_VERSION', '1.3');
$version = PADEDIT_VERSION;

//set some values to avoid undefined notices
$editfile = null;
$safety = false;

//Start session
session_set_cookie_params('0','/', null , false, true); 
session_start(); 

require_once("system/functions.php"); // core system object
$p = new padedit;

/*
//are we logged in? has been removed because we are running on a local system
// and a different type of identification has been chosen.


if (@$_SESSION["authorised"] === true ) { 
	//yes we are, but....
	//have they timed out?
	$lastActive = $_SESSION['lastActive'];
	// time limit in which they must log in again.
	$timeLimit = 1800; // 30 minutes. 
	
	//if they havent used the app in 30 minutes, make them log in again
	if (time() > ($lastActive + $timeLimit)   ){
		$loggedin = false;
	}else {
    	$loggedin = true; 
    	//update the last active time
    	$_SESSION['lastActive'] = time();
	}
}else {
	$loggedin = false;
}


if (!$loggedin) { 
	//checks to see if we need to run the setup, and runs it if we do.
	$setup = $p->checkSetup();

	if ($setup == true){
		//true, we need to run the setup
		$action = 'setup';
	} else {
		//otherwise make the user login.
		$p->login();
		$action = 'login';
	}
}


//controller section. works out what the user is trying to do and performs the necessary functions


// things we can only do if we are logged in
if ($loggedin){
*/
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
    	$message = "File saved. <a href='".$_GET['path'].$_GET['file']."' target='_blank'>View file.</a>";
    }
    
    if (isset($_GET['deleted'])) {
    	$message = "File deleted.";
    }
    
    if (isset($_GET['renamed'])) {
    	$message = "File renamed. <a href='".$_GET['path'].$_GET['file']."' target='_blank'>View file.</a>";
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
    } else { 
    	$path = "../"; 
    }
    if (!$p->checkPath($path)) { 
    	header("Location: index.php?path=../"); 
    	exit;
    } 
    
    //if the user wants to load a file
	if (isset($_GET['file']) and isset($_GET['path'])) {
		//get file details 
		$fileDetails = $p->getFileDetails($_GET['path'],$_GET['file']);
	}

	//check if they should be editing this file
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
		$fileEditable = $p->canEdit($fileDetails['extension']);

		if (!$fileEditable){
    		//$safety = true;
    		$fileLoaded = false;
    		$editfile =  '';
    		$message = "Sorry, You can't edit this type of file";
		}
		
	}
	
}

// load the template
require_once("system/templates/padedit.tpl.php");
?>