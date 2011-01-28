<?PHP

class Site_Initialise {
	
	Protected   $request;
	Protected   $response;
	
	function __construct(HTTP_Request $request,  HTTP_Response $response){
		//Logger::log('Initialise', 'Hi '.$request, L_DEBUG);
		
		$this->request  = $request;
		$this->response = $response;
	}
	
	
	function __call($method, $params){
		Logger::log('Initialise', 'Init Hook '.$method.' undefined', L_DEBUG);
	}
}