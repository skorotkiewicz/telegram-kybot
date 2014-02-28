<?php
class help {

	public $name 	= 'help';
	public $version = '0.01';
	public $creator = 'kenny';
	public $desc	= 'Tells you your';
	
	public function handle ($recv, $replyobj) {
		$helptxt = implode(", ", $replyobj->commands);
		$replyobj->reply('txt','Available commands: '.$helptxt);
	}
	
}

$this->registerModule('help');
$this->registerCommand('help','help');
?>
