<?php
/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2008 Frederico Caldeira Knabben
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * This is the File Manager Connector for PHP.
 */

function GetFolders( $resourceType, $currentFolder )
{
	// Map the virtual path to the local server path.
	$sServerDir = ServerMapFolder( $resourceType, $currentFolder, 'GetFolders' ) ;

	// Array that will hold the folders names.
	$aFolders    = array() ;
	if(defined('FTP_ON') && FTP_ON) {
		$list = ftpcmd('list', $sServerDir);
		foreach($list as $sFile) {
			$iFileSize = ftpcmd('filesize', $sFile);
			$sFile = basename($sFile);
			if ( $iFileSize < 0 )
			$aFolders[] = '<Folder name="' . ConvertToXmlAttribute( $sFile ) . '" />' ;
		}
	} else {

		$oCurrentFolder = opendir( $sServerDir ) ;

		while ( $sFile = readdir( $oCurrentFolder ) )
		{
			if ( $sFile != '.' && $sFile != '..' && is_dir( $sServerDir . $sFile ) )
			$aFolders[] = '<Folder name="' . ConvertToXmlAttribute( $sFile ) . '" />' ;
		}

		closedir( $oCurrentFolder ) ;
	}

	// Open the "Folders" node.
	echo "<Folders>" ;

	natcasesort( $aFolders ) ;
	foreach ( $aFolders as $sFolder )
	echo $sFolder ;

	// Close the "Folders" node.
	echo "</Folders>" ;
}

function GetFoldersAndFiles( $resourceType, $currentFolder )
{
	// Map the virtual path to the local server path.
	$sServerDir = ServerMapFolder( $resourceType, $currentFolder, 'GetFoldersAndFiles' ) ;

	// Arrays that will hold the folders and files names.
	$aFolders    = array() ;
	$aFiles        = array() ;
	if(defined('FTP_ON') && FTP_ON) {
		$list = ftpcmd('list', $sServerDir);
		foreach($list as $sFile) {
			$iFileSize = ftpcmd('filesize', $sFile);
			$sFile = basename($sFile);
			if ( $sFile != '.' && $sFile != '..' )
			{
				if ( $iFileSize < 0 )
				$aFolders[] = '<Folder name="' . ConvertToXmlAttribute( $sFile ) . '" />' ;
				else
				{
					if ( !$iFileSize ) {
						$iFileSize = 0 ;
					}
					if ( $iFileSize > 0 )
					{
						$iFileSize = round( $iFileSize / 1024 ) ;
						if ( $iFileSize < 1 ) $iFileSize = 1 ;
					}

					$aFiles[] = '<File name="' . ConvertToXmlAttribute( $sFile ) . '" size="' . $iFileSize . '" />' ;
				}
			}
		}
	} else {
		$oCurrentFolder = opendir( $sServerDir ) ;

		while ( $sFile = readdir( $oCurrentFolder ) )
		{
			if ( $sFile != '.' && $sFile != '..' )
			{
				if ( is_dir( $sServerDir . $sFile ) )
				$aFolders[] = '<Folder name="' . ConvertToXmlAttribute( $sFile ) . '" />' ;
				else
				{
					$iFileSize = @filesize( $sServerDir . $sFile ) ;
					if ( !$iFileSize ) {
						$iFileSize = 0 ;
					}
					if ( $iFileSize > 0 )
					{
						$iFileSize = round( $iFileSize / 1024 ) ;
						if ( $iFileSize < 1 ) $iFileSize = 1 ;
					}

					$aFiles[] = '<File name="' . ConvertToXmlAttribute( $sFile ) . '" size="' . $iFileSize . '" />' ;
				}
			}
		}
	}

	// Send the folders
	natcasesort( $aFolders ) ;
	echo '<Folders>' ;

	foreach ( $aFolders as $sFolder )
	echo $sFolder ;

	echo '</Folders>' ;

	// Send the files
	natcasesort( $aFiles ) ;
	echo '<Files>' ;

	foreach ( $aFiles as $sFiles )
	echo $sFiles ;

	echo '</Files>' ;
}

function CreateFolder( $resourceType, $currentFolder )
{
	if (!isset($_GET)) {
		global $_GET;
	}
	$sErrorNumber    = '0' ;
	$sErrorMsg        = '' ;

	if ( isset( $_GET['NewFolderName'] ) )
	{
		$sNewFolderName = $_GET['NewFolderName'] ;
		$sNewFolderName = iconv("utf-8","gbk",$sNewFolderName);
		$sNewFolderName = SanitizeFolderName( $sNewFolderName ) ;

		if ( strpos( $sNewFolderName, '..' ) !== FALSE )
		$sErrorNumber = '102' ;        // Invalid folder name.
		else
		{
			// Map the virtual path to the local server path of the current folder.
			$sServerDir = ServerMapFolder( $resourceType, $currentFolder, 'CreateFolder' ) ;

			if ( is_writable( $sServerDir ) )
			{
				$sServerDir .= $sNewFolderName ;

				$sErrorMsg = CreateServerFolder( $sServerDir ) ;

				switch ( $sErrorMsg )
				{
					case '' :
						$sErrorNumber = '0' ;
						break ;
					case 'Invalid argument' :
					case 'No such file or directory' :
						$sErrorNumber = '102' ;        // Path too long.
						break ;
					default :
						$sErrorNumber = '110' ;
						break ;
				}
			}
			else
			$sErrorNumber = '103' ;
		}
	}
	else
	$sErrorNumber = '102' ;

	// Create the "Error" node.
	echo '<Error number="' . $sErrorNumber . '" originalDescription="' . ConvertToXmlAttribute( $sErrorMsg ) . '" />' ;
}

function FileUpload( $resourceType, $currentFolder, $sCommand )
{
	if (!isset($_FILES)) {
		global $_FILES;
	}

	$sErrorNumber = '0' ;
	$sFileName = '' ;

	if ( isset( $_FILES['NewFile'] ) && !is_null( $_FILES['NewFile']['tmp_name'] ) )
	{
		global $Config,$_G ;

		$oFile = $_FILES['NewFile'] ;
		

		// Map the virtual path to the local server path.
		$sServerDir = ServerMapFolder( $resourceType, $currentFolder, $sCommand ) ;

		// Get the uploaded file name.
		$sFileName = $oFile['name'] ;
		$sFileName = iconv("utf-8","gbk",$sFileName);
		$sFileName = SanitizeFileName( $sFileName ) ;

		$sOriginalFileName = $sFileName ;

		// Get the extension.
		$sExtension = substr( $sFileName, ( strrpos($sFileName, '.') + 1 ) ) ;
		$sExtension = strtolower( $sExtension ) ;
		$sFileName=$_G['timestamp'].random(4).".".$sExtension;

		if ( isset( $Config['SecureImageUploads'] ) )
		{
			if ( ( $isImageValid = IsImageValid( $oFile['tmp_name'], $sExtension ) ) === false )
			{
				$sErrorNumber = '202' ;
			}
		}

		if ( isset( $Config['HtmlExtensions'] ) )
		{
			if ( !IsHtmlExtension( $sExtension, $Config['HtmlExtensions'] ) &&
			( $detectHtml = DetectHtml( $oFile['tmp_name'] ) ) === true )
			{
				$sErrorNumber = '202' ;
			}
		}

		// Check if it is an allowed extension.
		if ( !$sErrorNumber && IsAllowedExt( $sExtension, $resourceType ) )
		{
			$iCounter = 0 ;

			while ( true )
			{
				$sFilePath = $sServerDir . $sFileName ;

				if ( is_file( $sFilePath ) )
				{
					$iCounter++ ;
					$sFileName = RemoveExtension( $sOriginalFileName ) . '(' . $iCounter . ').' . $sExtension ;
					$sErrorNumber = '201' ;
				}
				else
				{
					include_once(ROOT_PATH . '/source/class/app/app_img.php');
					$image = new app_img();
					if(defined('FTP_ON') && FTP_ON) {
						$isftp = true;
					}else{
						$isftp = false;
					}
					$sFileUrl = $image->upload_image($oFile,$resourceType.$currentFolder,$sFileName,$isftp);

					if ( is_file( $sFilePath ) )
					{
						if ( isset( $Config['ChmodOnUpload'] ) && !$Config['ChmodOnUpload'] )
						{
							break ;
						}

						$permissions = 0777;

						if ( isset( $Config['ChmodOnUpload'] ) && $Config['ChmodOnUpload'] )
						{
							$permissions = $Config['ChmodOnUpload'] ;
						}

						$oldumask = umask(0) ;
						chmod( $sFilePath, $permissions ) ;
						umask( $oldumask ) ;
					}

					break ;
				}
			}

			if ( file_exists( $sFilePath ) )
			{
				//previous checks failed, try once again
				if ( isset( $isImageValid ) && $isImageValid === -1 && IsImageValid( $sFilePath, $sExtension ) === false )
				{
					@unlink( $sFilePath ) ;
					$sErrorNumber = '202' ;
				}
				else if ( isset( $detectHtml ) && $detectHtml === -1 && DetectHtml( $sFilePath ) === true )
				{
					@unlink( $sFilePath ) ;
					$sErrorNumber = '202' ;
				}
			}
		}
		else
		$sErrorNumber = '202' ;
	}
	else
	$sErrorNumber = '202' ;


	$sFileUrl = CombinePaths( GetResourceTypePath( $resourceType, $sCommand ) , $currentFolder ) ;
	$sFileUrl = CombinePaths( $sFileUrl.$resourceType, $sFileName ) ;

	SendUploadResults( $sErrorNumber,$sFileUrl, $sFileName ) ;

	exit ;
}
function MoreFileUpload( $resourceType, $currentFolder, $sCommand )
{
	if (!isset($_FILES)) {
		global $_FILES;
	}

	global $Config,$_G;
	$sErrorNumber = '0' ;
	$sFileName = '' ;

	if ( is_array($_FILES['NewFile']['name']) )
	{
		$fileinfo =array();
		foreach($_FILES['NewFile']['name'] as $k=>$v){
			$fileinfo[]=array('name'=>$v,'type'=>$_FILES['NewFile']['type'][$k],'tmp_name'=>$_FILES['NewFile']['tmp_name'][$k],'error'=>$_FILES['NewFile']['error'][$k],'size'=>$_FILES['NewFile']['size'][$k]);
		}
		include_once(ROOT_PATH . '/source/class/app/app_img.php');
		$image = new app_img();
		if(defined('FTP_ON') && FTP_ON) {
			$islocal = false;
		}else{
			$islocal = true;
		}
		foreach ( $fileinfo as $key => $value )
		{
			if ($value['size']>0)
			{
				// Map the virtual path to the local server path.
				$sServerDir = ServerMapFolder( $resourceType, $currentFolder, $sCommand ) ;

				// Get the uploaded file name.
				$sFileName = $value['name'] ;
				$sFileName = SanitizeFileName( $sFileName ) ;

				$sOriginalFileName = $sFileName ;

				// Get the extension.
				$sExtension = substr( $sFileName, ( strrpos($sFileName, '.') + 1 ) ) ;
				$sExtension = strtolower( $sExtension ) ;
				$sFileName=$_G['timestamp'].random(4).".".$sExtension;

				if ( isset( $Config['SecureImageUploads'] ) )
				{
					if ( ( $isImageValid = IsImageValid( $value['tmp_name'], $sExtension ) ) === false )
					{
						$sErrorNumber = '202' ;
					}
				}

				if ( isset( $Config['HtmlExtensions'] ) )
				{
					if ( !IsHtmlExtension( $sExtension, $Config['HtmlExtensions'] ) &&
					( $detectHtml = DetectHtml( $value['tmp_name'] ) ) === true )
					{
						$sErrorNumber = '202' ;
					}
				}

				// Check if it is an allowed extension.
				if ( !$sErrorNumber && IsAllowedExt( $sExtension, $resourceType ) )
				{
					$iCounter = 0 ;

					while ( true )
					{
						$sFilePath = $sServerDir . $sFileName ;

						if ( is_file( $sFilePath ) )
						{
							$iCounter++ ;
							$sFileName = RemoveExtension( $sOriginalFileName ) . '(' . $iCounter . ').' . $sExtension ;
							$sErrorNumber = '201' ;
						}
						else
						{
							if(defined('FTP_ON') && FTP_ON) {
								$isftp = true;
							}else{
								$isftp = false;
							}
							$image->upload_image($value,$resourceType.$currentFolder,$sFileName,$isftp);
							if ( is_file( $sFilePath ) )
							{
								if ( isset( $Config['ChmodOnUpload'] ) && !$Config['ChmodOnUpload'] )
								{
									break ;
								}

								$permissions = 0777;

								if ( isset( $Config['ChmodOnUpload'] ) && $Config['ChmodOnUpload'] )
								{
									$permissions = $Config['ChmodOnUpload'] ;
								}

								$oldumask = umask(0) ;
								chmod( $sFilePath, $permissions ) ;
								umask( $oldumask ) ;
							}

							break ;
						}
					}

					if ( file_exists( $sFilePath ) )
					{
						//previous checks failed, try once again
						if ( isset( $isImageValid ) && $isImageValid === -1 && IsImageValid( $sFilePath, $sExtension ) === false )
						{
							@unlink( $sFilePath ) ;
							$sErrorNumber = '202' ;
						}
						else if ( isset( $detectHtml ) && $detectHtml === -1 && DetectHtml( $sFilePath ) === true )
						{
							@unlink( $sFilePath ) ;
							$sErrorNumber = '202' ;
						}
					}
				}
				else
				$sErrorNumber = '202' ;

				if ( $sErrorNumber == '202' )
				{
					$sFileUrl = CombinePaths( GetResourceTypePath( $resourceType, $sCommand ) , $currentFolder ) ;
					$sFileUrl = CombinePaths( $sFileUrl.$resourceType, $sFileName ) ;

					SendUploadResults( $sErrorNumber, $sFileUrl, $sFileName) ;
				}
			}
			else
			{
				continue;
			}
		}

		$sFileUrl = CombinePaths( GetResourceTypePath( $resourceType, $sCommand ) , $currentFolder ) ;
		$sFileUrl = CombinePaths( $sFileUrl.$resourceType, $sFileName ) ;

		SendUploadResults( $sErrorNumber, $sFileUrl, $sFileName, $key) ;
	}
	else
	{
		$sErrorNumber = '202' ;
		$sFileUrl = CombinePaths( GetResourceTypePath( $resourceType, $sCommand ) , $currentFolder ) ;
		$sFileUrl = CombinePaths( $sFileUrl, $sFileName ) ;

		SendUploadResults( $sErrorNumber, $sFileUrl, $sFileName ) ;
	}

	exit ;
}
?>