<?PHP
/***

	Plank Site
	
	Site is the main execution model of the system for a website.
	
	In to it comes a request & response object, and we modify the response
	object until we get to the end and throw it back up for display.
	
	Notable are:
	
		Site_Initialise - a class for seting up an environment and doing
				 what needs to be done afterwards. Sessions is a good example.
		
		Routing         - work out what controller to execute.

*/


class Site {
	

	function __construct($request, $response){
		
		$config = Config::getInstance();
        	$init = new Site_Initialise($request, $response);
		
		if ($init){
			$init->preroute();
		}
		
		$routing = new Routing($request);
	
		if (!$routing->foundRoute){
			Logger::log('Router', 'Routing failed', L_FATAL);
			$response->setError('I couldn\'t find a route for that URL');
			$response->setStatus(404);
			return;
		}
		
		$init->postroute($routing->controller, $routing->action);
		
		$controllername = 'Controller_'.$routing->controller;
		
		if (Autoload::findClass($controllername)){
			# Yay
		} elseif (Autoload::findClass('Controller_'.$routing->controller)) {
			$controllername = 'Controller_'.$routing->controller;
		} else {
			Logger::log('Router', 'There is no such thing as '.$controllername, L_WARN);
			$response->setError('I couldn\'t load the controller \''.$routing->controller.'\'');
			$response->setStatus(404);
			return;
		}
		
		$controller = new $controllername($request, $response);
		
		
		if (! is_a($controller, "Controller")){
			throw new Exception("$controllername must be a subclass of Controller");
		}
		
		$init->gotcontroller($controller, $routing->action);
		
		$method = $routing->action.'Action';
		
		$controller->$method();
		
		
		$init->shutdown($routing->controller, $routing->action);
		
	}
	
}