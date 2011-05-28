<?PHP

class HTTP_Request {
	
	public $uri;
	public $get;
	public $post;
	public $cookies;
	public $client_ip;
	public $user_agent;
	public $keep_alive;
	public $connection;
	public $accept_content;
	public $accept_charset;
	public $accept_languages;

	
	function __construct(){
		
		
      Logger::log('Request', 'Init Request with '.count($_COOKIE).' cookies', L_TRACE);
		
		$this->post             = (object)$_POST;
		$this->get              = (object)$_GET;
		$this->cookies          = (object)$_COOKIE;
		$this->user_agent       = @$_SERVER['HTTP_USER_AGENT'];
		$this->accept_content   = explode(',', $_SERVER['HTTP_ACCEPT']);
		$this->accept_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		if(isset($_SERVER['HTTP_ACCEPT_CHARSET'])){
			$this->accept_charset   = explode(',', $_SERVER['HTTP_ACCEPT_CHARSET']);
		}
		if(isset($_SERVER['HTTP_KEEP_ALIVE'])){
			$this->keep_alive   = $_SERVER['HTTP_KEEP_ALIVE'];
		}
		$this->connection       = @$_SERVER['HTTP_CONNECTION'];
		
		$this->client_ip        = @$_SERVER['REMOTE_ADDR'];
		
		$this->uri              = @$_SERVER['REQUEST_URI'];
		
		foreach((array) $this->post as $index => $value){
			if(is_string($value)){
				$this->post->$index = stripslashes($value);
			}
		}
		foreach((array) $this->get as $index => $value){
			if(is_string($value)){
				$this->get->$index = stripslashes($value);
			}
		}


		$uri = trim($this->uri,'/');
		
		if(strpos($uri, '?') !== false){
			Logger::log('Routing', 'A Query String', L_INFO);
			list($uri, $query) = explode('?', $uri);
		}
		
		$path = explode('/',rtrim($uri));
				
		if (isset($path[0]) && empty($path[0])){
			array_pop($path);
		}

		$this->path = $path;
		
	}	
	
	
	
}


?>
