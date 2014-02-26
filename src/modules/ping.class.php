<?php
class ping {

	public $name 	= 'ping';
	public $version = '0.01';
	public $creator = 'kenny';
	public $desc	= 'Replies -pong- on call.';
	
	public function handle ($recv, $replyobj) {
		$replyobj->reply('txt','pong');
	}
	
}

$this->registerModule('ping');
$this->registerCommand('ping','ping');
?>