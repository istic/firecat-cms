<?PHP


class Controller {
	
	function __construct(HTTP_Request $request, HTTP_Response $response){
		$this->request = $request;
		$this->response = $response;
	}
	
	function __call($method, $params){
		
		Logger::log('Controller', get_class($this).' doesn\'t have an action called '.$method, L_WARN);
		$this->response->setError('Controller '.get_class($this).' doesn\'t have an action called '.$method);
		$this->response->setStatus(404);
	}
}
