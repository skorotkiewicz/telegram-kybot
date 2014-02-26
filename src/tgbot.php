<?php
class main {
	public $commands = array();
	public $modules = array();
	
	private $numModules = NULL;
	private $numCommands = NULL;
	
	private $lastSender = NULL;
	
	public function __construct() {
		//
	}
	
	// allows modules to register themselves
	public function registerModule ($module) {
		$this->numModules++;
		$this->modules[$this->numModules] = $module;
	}
	
	// allows modules to register new commands
	public function registerCommand ($cmd, $handler) {
		$this->commands[$cmd] = $handler;		
	}
	
	// handle received commands
	public function handle ($recv) {
		$handler = @$this->commands[$recv[0]]; // find out handler for command
		if(!empty($handler)) {
			$hdl = new $handler;
			$hdl->handle($recv,$this); // delegate handling to command
		} else {
			$this->reply('txt', "Sorry, I don't know this command.");
		}
		unset($hdl);
	}
	
	public function loadModules () {
		foreach (glob("modules/*.php") as $filename) {
			include $filename;
		}
	}
	
	public function getCmd () {
		redo:
		$request = shell_exec('expects/tgrecvmsg.xp | grep ">>>"');
		if(empty($request)) goto redo;
		$reqArray = explode(" ",$request);
	
	    array_shift($reqArray);
	    array_shift($reqArray);
		array_shift($reqArray);
		array_shift($reqArray);
		
		$sender = NULL;
		
		$delimiter = array_search(">>>",$reqArray);
		
		while ($reqArray[0] != ">>>" OR empty($reqArray)) {
			if(!empty($sender)) $sender .= "_";
			$sender .= $reqArray[0];
			array_shift($reqArray);
		}
		
		array_shift($reqArray);
		
		$this->lastSender = $sender;
		$reqArray[0] = preg_replace( "/\r|\n/", "", $reqArray[0]);
				
		return $reqArray;
	}
	
	// send replies to sender
	public function reply ($type, $mesg) {
		switch ($type) {
			case 'imgstatic':  // Static image
				shell_exec('expects/tgsendimg.xp '.$this->lastSender.' '.$image);
				break;
				
			case 'imgget': // Image from URL
				shell_exec('wget --quiet -O /tmp/img.jpg '.$image);
				shell_exec('expects/tgsendimg.xp '.$this->lastSender.' /tmp/img.jpg');
				shell_exec('rm /tmp/img.jpg');
				break;
				
			case 'txt': // Standard message
				$mesg = preg_replace( "/\r|\n/", " ", $mesg);
				shell_exec('expects/tgsendmsg.xp '.$this->lastSender.' "'.$mesg.'"');
				break;
				
			default:
				echo "Illegal.";
				break;
		}
	}
	
}

$bot = new main();
$bot->loadModules();

//print_r($bot->commands);
//print_r($bot->modules);

// Main loop
while (1) {
	$recv = $bot->getCmd();
	$bot->handle($recv);
}

?>
