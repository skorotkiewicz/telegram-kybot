<?php
class lastfm {

	public $name 	= 'lastfm';
	public $version = '0.01';
	public $creator = 'kenny';
	public $desc	= 'Tells you what a lastfm account is currently listening to.';
	
	public  function rss_to_array($tag, $array, $url) {
                $doc = new DOMdocument();
                $doc->load($url);
                $rss_array = array();
                $items = array();
                foreach($doc->getElementsByTagName($tag) AS $node) {    
                        foreach($array AS $key => $value) {
                                $items[$value] = $node->getElementsByTagName($value)->item(0)->nodeValue;
                        }
                        array_push($rss_array, $items);
                }
                return $rss_array;
    }
	
	public function handle ($recv, $replyobj) {
		        
        $rss_tags = array(
                'title',
                'link',
                'guid',
                'comments',
                'description',
                'pubDate',
                'category',
        );
        $rss_item_tag = 'item';
        $rss_url = 'http://ws.audioscrobbler.com/1.0/user/'.$recv[1].'/recenttracks.rss';
        
        $rssfeed = $this->rss_to_array($rss_item_tag,$rss_tags,$rss_url);
        
		//echo str_replace("?","-",utf8_decode($rssfeed[0][title]));

		$replyobj->reply('txt',str_replace("?","-",utf8_decode($rssfeed[0][title])));
	}
	
}

$this->registerModule('lastfm');
$this->registerCommand('lastfm','lastfm');
?>