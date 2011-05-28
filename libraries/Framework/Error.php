<?PHP

class Error {
	
	
	function __call($function, $message){
		
		Error::defaultError('503', $message, $response);
	}
	
	static function Error503($message, $response){
		Error::defaultError('503', $message, $response);
	}
	
	
	static function defaultError($status, $message, $response){
		$trace = debug_backtrace();
		
		if(is_object($message) && is_subclass_of($message, 'Exception')){
			$trace = $message->getTrace();
			$message = $message->getMessage();
		}
		
		if(defined('DESTRUCT')){
			die($message);
		}


                $view = new stdClass;
		
				
		$view->error = '	
				
		<h2>'.$message.'</h2>
		
		';
		
		$view->error .= Error::getBacktrace($trace);

		echo $view->error;
		$response->setContent($view->error);
		
		$response->respond();

		
		if(defined("TEXTMODE")){
			echo "Uh-Oh\n";
			echo striptags($view->error);
			die();
		} else {		
			echo Logger_Display::display();
		}
		
	}
	
	static function getBacktrace($trace = null){
		
		if (is_null($trace)){
			echo "<p>(Trace generated from inside Exception handler)</p>";
			$trace = debug_backtrace();
		}
		#$trace = array_slice($trace, 1);
		
		$output = '
		
		<p><span style="color: #CCC">ħ</span> stands for <q>'.realpath(CODE_PATH."../").'</q> in the below:</p>
		
		<table width="100%">
			<tr><th>Function/Method</th><th>File</th><th>Line</th><th>Args</th></tr>';
		
		$sprintf = '<tr><td>%s</td><td>%s</td><td>%s</td><td><a href="#" onClick="document.getElementById(\'%s\').style.display = \'block\'; this.style.display = \'none\'">Args</a><pre id="%s" style="display: none;">%s</pre></td></tr>';
		foreach($trace as $t){
			$function = isset($t['class']) ?  $t['class']. $t['type']. $t['function'] : $t['function'];
			
			$id = uniqid();
			
			if (!defined("CODE_PATH")){
				define("CODE_PATH", getcwd());
			}
			//$file = str_replace(getcwd(), 'CWD', $t['file']);
                        $file = "";
                        if(isset($t['file'])){
                            $file = $t['file'];
                        }
			$file = str_replace(realpath(CODE_PATH), '[<acronym title="'.realpath(CODE_PATH).'">APP</acronym>]', $file);
			$file = str_replace(realpath(PATH), '[<acronym title="'.realpath(PATH).'">PLK</acronym>]', $file);
			$file = str_replace(getcwd(), 'CWD', $file);
			
			if(isset($t['file'])){
				$file = str_replace(realpath(CODE_PATH."../"), '<span style="color: #CCC">ħ</span>', $t['file']);
			} else {
				$file = "-";
			}
			if(isset($t['line'])){
				$line = $t['line'];
			} else {
				$line = "-";
			}
			if(isset($t['args'])){
				$args = Error::var_dump_string($t['args'],1);
			} else {
				$args = "[no arguments]";
			}
			$output .= sprintf($sprintf, $function, $file, $line, $id, $id, $args);
			
		}
		
		$output .= '</table>';

		return $output;
		
	}
	
	static function var_dump_string($thing){
			ob_start();
			var_dump($thing);
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		
	}
}
