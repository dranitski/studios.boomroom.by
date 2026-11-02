<?php
	
	defined('access') or die ('no access');
	
	require_once('functions/inc/pclzip.lib.php');
	
	// $pathFrom	= $_SERVER['DOCUMENT_ROOT']."/tmpl/";
	// $pathTo	= $_SERVER['DOCUMENT_ROOT']."/backup/archive.zip";
	
	// Создание архива
	function createZip($from, $to, $exceptions = array()) {
		
		// Получаем путь к папке с архивом
		$lastSymb	= strripos($to, "/");
		$logPath	= substr($to, 0, $lastSymb);
		
		$logFile		= $logPath."/log.tmp";
		fopen($logFile, 'w');
		
		file_put_contents($logFile, "0");
		
		$files_to_zip = array();

		for($d = @opendir($from); $file = @readdir($d);) {      
		  
			if($file!='.' && $file!='..' && in_array($file, $exceptions) != true) {
				
				$files_to_zip[] = $file;
			}
		}

		chdir($from);

		$archive = new PclZip($to);

		$list = $archive->create(implode(',', $files_to_zip));
		
		if($list) {
		
			file_put_contents($logFile, "1");
			return true;
		}
		else {
			
			// Выводим ошибку
			// echo $archive->errorInfo(true);
			file_put_contents($logFile, "2");
			return false;
		}
		
	}
	
	// Распаковка архива
	function extractZip($from, $to) {
		
		$archive = new PclZip($from);
		if ($archive->extract(PCLZIP_OPT_PATH, $to)) {
			
			return true;
		}
		else {
			
			// Выводим ошибку
			// echo $archive->errorInfo(true);
			return false;
		}
	}
	
?>