<?PHP


class Config {
    
	# Begin Singleton Zen
    static private $_instance;
        
    static function getInstance()
    {
        if(empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
 
    private function __construct ()
    {
        Logger::log('Singleton', get_class($this).' singleton created.', L_TRACE);
        $this->init();
    }
	# End Singleton Zen
    
	
	private $data = array();
	
	function init(){
		$this->loadFile('../configGlobal.ini');
		$this->loadFile('../configLocal.ini');
		
	}

        function loadFile($file){
                if (file_exists($file)){
                        if (is_readable($file)){
                                $data = parse_ini_file($file, true);
                                $this->data = array_merge($this->data, $data);
                        } else {
                                throw new Exception_ConfigError('Config File isn\'t readable');
                        }
                }
        }
	
	function get($area, $value){
		if (isset($this->data[$area]) && isset($this->data[$area][$value])){
                        Logger::log('Config', "Got $area/$value as ".$this->data[$area][$value], L_INFO);
			return $this->data[$area][$value];
		}		
		Logger::log('Config', "Couldn't get $area/$value", L_DEBUG);
		return false;
	}
	
	function getArea($area){
		if (isset($this->data[$area])){
			return $this->data[$area];
		}		
		Logger::log('Config', "Couldn't get $area", L_TRACE);
		return false;
	}
	
	function getAll(){
		return $this->data;
	}
}
