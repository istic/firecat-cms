<?php

class Autoload {

	static function loadClass($class_name){
		if (defined('INIT')){
			Logger::log('Autoloader', 'Loading class '.$class_name, L_TRACE);
		}

		error_log('Loading '.$class_name);

		$found = Autoload::findClass($class_name);

		if ($found){
			//defined('INIT') ? Logger::log('Autoloader', 'Found '.$found, L_TRACE) : false;
			require($found);
			return true;
		} else {
			error_log('Couldn\'t Load Class "'.$class_name.'"');
			global $response;

			if (defined('DESTRUCT')){
				return;
			}


			Error::Error503('Code Error: Fatal Error Loading '.$class_name, $response);
			$response->respond();
		}
	}

	static function findClass($class_name){

                $searchpath = explode(':',ini_get('include_path'));

		$found = 0;

		$filename = implode('/',explode('_',$class_name)).'.php';
		foreach($searchpath as $path){
			$path .= '/';
			defined('INIT') ? Logger::log('Autoloader', 'Looking for '.$path.$filename, L_TRACE) : false;
			if (file_exists($path.$filename)){
				return $path.$filename;
			}
		}

	}
}