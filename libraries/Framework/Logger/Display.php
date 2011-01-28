<?PHP

class Logger_Display {
	
	// Display Plank logging info
	
	function display(){
		if(!defined('SHOWDEBUG')|| !SHOWDEBUG){return;}
		
		$out = <<<EOW
		
	<!-- begin Plank Inline Debuggery -->
	<script type="text/javascript">
	
		function plankShowDebug(what){
			document.getElementById('plankDebugLogs').style.display = 'none';
			document.getElementById('plankDebugStats').style.display = 'none';

			document.getElementById(what).style.display = 'block';
			
		}
		
		function plankHideDebug(){
			document.getElementById('plankDebugPane').style.display = 'none';
		}
	
	</script>
	<style type="text/css">
		#plankDebugPane {
			opacity: .25;
			filter: alpha(opacity=25);
			-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=25)";


			border: 1px solid red;	
			position: absolute;
			top: 0;
			right: 0;
			z-index: 10000;
			background: #FFF;
			font-family: "Trebuchet MS" sans-serif;
			border: 1px solid #840000;	
			-moz-border-radius: 15px 0 15px 30px;
			-webkit-border-radius: 30px 60px 30px 60px;
			

			
			padding-left: .5em;
		}
		
		#plankDebugPane:hover {

			opacity: .85;
			filter: alpha(opacity=85);
			-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=85)";
		}
	
		#plankDebugPane div.logView {
			padding-bottom: 1em;
			overflow: auto;
			max-width: 100%;
			width: 1024px;
		}
		
		.plankLogo {
			color: #840000;
			font-size: larger;	
		}
	</style>
	
EOW;
	$logOutput = Logger_Output::getInstance();
	list($log, $queries, $stats) = $logOutput->giveMeLogs();


echo '
	<div id="plankDebugPane">
		<div class="plankDebugNav">Show [
		<a href="javascript:plankShowDebug(\'plankDebugLogs\')">'.count($log).' Logs</a> | 
		<a href="javascript:plankShowDebug(\'plankDebugStats\')">'.count($stats).' Timings</a> | 
		<a href="javascript:plankShowDebug(\'plankDebugPane\')">Nothing</a> |
		<a href="javascript:plankHideDebug()">X</a>]</div> 
	';
	
	$strf = "<tt>[%2.5f][%d][%s]</tt> %s<br />";

	$out .= '<div id="plankDebugLogs" class="logView" style="display: none"><h2>Logs</h2>';
	foreach($log as $logline){
		if (is_array($logline[2])){
			foreach($logline[2] as $index => $item){
				$out .= sprintf($strf, $logline[0]-T, $logline[3], $logline[1], "<tt>[$index]</tt>".$item);
			}
			
		} else {
			$out .= sprintf($strf, $logline[0]-T, $logline[3], $logline[1], htmlentities($logline[2]));
		}
	}
	$out .= "\n</div>";
	
	
	$out .= '<div id="plankDebugStats" class="logView" style="display: none"><h2>Stats</h2>';
	
	foreach($stats as $logline){
    	$message =  $logline[2];
		if (is_array($message)){
			foreach($message as $index => $item){
				$out .= sprintf('[%2.5f][%s] %s<br/>', $logline[0]-T, $logline[1], $index." - ".$item);
			}
		} else {    		
			$out .= sprintf('[%2.5f][%s] %s<br/>', $logline[0]-T, $logline[1], $logline[2]);
		}
		
	}
	$out .= sprintf('( %2.5f total)', microtime(true)-T ) ;
	$out .= "\n</div>";

	$out .='	</div>';


	return $out;
		
	}
	
}

?>
