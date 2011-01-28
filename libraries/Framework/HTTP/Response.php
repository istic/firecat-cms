<?PHP

class HTTP_Response {
	
	private $content = '<hr><i>No content provided to response</i>';
	private $content_type = 'text/xml';
	private $status_code  = '200';
	private $status_message = 'OK';
	
	private $cookies= array();
	
	private $etag = '';
	private $http_version = '1.1';
	
	public $location;
	
	
	public function respond(){
		header('HTTP '.$this->http_version.' '.$this->status_code.' '.$this->status_message);
		
		foreach($this->cookies as $cookie){
			list($name, $value, $expire, $path, $domain, $secure, $httponly) = $cookie;
			setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
		}
		
		echo $this->content;
	}
	
	public function setcontent($content){
		$this->content = $content;
		
	}
	
	
	public function seterror($content){
		$this->errortext = $content;
		
	}
	
	public function setcookie($name, $value, $expire = 0, $path = '/' , $domain = false , $secure=false, $httponly=false){
		$expire = strtotime($expire);
		$this->cookies[$name] = array($name, $value, $expire, $path, $domain, $secure, $httponly);
		Logger::log('Response', 'Setting cookie '.$name.' to '.$value, L_TRACE);
	}
	
	public function redirect($location){
		$this->location = $location;
		$this->setstatus(301);
		$this->setcontent("301 to ".$location);
	}
	
	public function setstatus($code){
		$this->status_code = $code;
		
		switch ($code){
			case '404':
				$this->status_message = 'Does not exist';
                                $this->content = '
                                <h1>File Not Found</h1>

                                <p>'.$this->errortext.' </p>
                                ';
                        	echo Logger_Display::display();
				break;
				
			case '503':
				$this->status_message = 'Something\'s screwed';
				try {
					$view = new View('Errors', 'Error503');
					$this->content = $view->render();
				} catch ( Exception  $e ) {
					$this->content = '
					<h1>System Error</h1>
					
					<p>Something\'s gone wrong. Try again later? Maybe</p>			
					';			
				}
				break;
				
			case '302':
			case '301':
				if (!$this->location){
					throw new Exception("Redirect requested, but no location specified");
				}
				header("Location: ".$this->location);
		}
		

		
	}
}
