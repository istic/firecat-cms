<?PHP

class Logger_Output {

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
        $this->logLine('Singleton', get_class($this).' singleton created.', L_TRACE);
        
    }
	# End Singleton Zen
    
  
    private $log = array();
    private $querylog = array();
    private $stats = array();
    
    public $areamax = 0;
    private $errorlevel = 15;
    
    
    public function strf(){
    	return "[%2.5f][%-3d][%-".$this->areamax."s] %s";
    }
    
    public function logLine($area, $message, $level){    
    	
    	error_log(sprintf($this->strf(), microtime(true)-T, $level, $area, $message));
    		
    	if ($level < $this->errorlevel){
    		if (strlen($area) > $this->areamax){
    			$this->areamax = strlen($area);
    		}
    		$this->log[] = array(microtime(true), $area, $message, $level);
    		
    		//error_log(sprintf($this->strf(), microtime(true)-T, $level, $area, $message));
    		
		}
    }
    
    public function logQuery($area, $message, $query){
    	$this->querylog[] = array(microtime(true), $area, $message, $query);
    }
    
    public function logStat($area, $thing) {
    	$this->stats[] = array(microtime(true), $area, $thing);
    }
    
    public function dumpLog(){
    	foreach($this->log as $logline){
    		//microtime, level, area, message
    		
    		$message =  $logline[2];
    		if (is_array($message)){
    			foreach($message as $index => $item){
    				printf($this->strf(), $logline[0]-T, $logline[3], $logline[1], $index." - ".$item);
    				echo "\n";
    			}
    		} else {    		
    			printf($this->strf(), $logline[0]-T, $logline[3], $logline[1], $message);
    			echo "\n";
			}
    	}    	
    }
    
    public function giveMeLogs(){
    	return array($this->log, $this->querylog, $this->stats);
    }
    
    public function logPHP($errno, $errstr, $errfile = false, $errline = false, array $errcontext =null){

		$area = 'PHP';

    	if ($errno  < $this->errorlevel){
    		
    		if ($errfile && $errline){
    			$message = sprintf('%s in %s:%s', $errstr, $errfile, $errline);
			} else {
				$message = $errstr;
			}
    		if (strlen($area) > $this->areamax){
    			$this->areamax = strlen($area);
    		}
    		$this->log[] = array(microtime(true), $area, $message, $errno);
    		
    		error_log(sprintf($this->strf(), microtime(true)-T, $errno, $area, $message));
    		
		}
    }
    
    
}
