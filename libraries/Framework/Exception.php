<?PHP

class Plank_Exception extends Exception {
	public function __construct($message, $code = false, $previous = false){
		Plank_Logger::log('Exception', get_class($this).' '.$message.' '.$code, L_FATAL);
		
		parent::__construct($message, $code);
		
	}
}
