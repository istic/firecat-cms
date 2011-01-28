<?PHP
/* This is the firecat-cms project doorway. */

// Step one, set up some basic stuff.

$path = ini_get("include_path");
$path .= ":../libraries:../libraries/Framework";
ini_set("include_path", $path);

define("PATH", getcwd());

// Two, setup autoload. This means that when you do:
// $foo = new Firecat_WitW_Character
// it'll automatically look for a file called Libaries/Firecat/WitW/Character.php to include
// if it has no idea what class you mean.

require("Autoload.php");
require("Logger.php");
function __autoload($class_name){
	Autoload::loadClass($class_name);
}
function handle_exceptions($e){
	global $response;
	Error::Error503($e, $response);
}
set_exception_handler("handle_exceptions");



header('Content-type: text/html; charset=UTF-8') ;
ob_start();
define('SHOWDEBUG', true);

define('T', microtime(true));

define('CODE_PATH', '../libraries/');

define('L_TRACE', 32);
define('L_DEBUG', 16);
define('L_INFO', 8);
define('L_WARN', 4);
define('L_ERROR', 2);
define('L_FATAL', 1);

// Three, set up the basic objects

$request  = new HTTP_Request();
$response = new HTTP_Response();

// Four, run the site.
new Site($request, &$response);

$response->respond();

// Five, blow everything up.

define('DESTRUCT', true);