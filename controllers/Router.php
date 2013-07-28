<?php

class Router {
	public $debug = false;
	private $options = array();

	public function getSections() {
		// return the url sections as an array
		$self = explode('/', $_SERVER['PHP_SELF']);
		$uri = explode('/', $_SERVER['REQUEST_URI']);
		return array_splice($uri, count($self)-1, -1);
	}
	
	public function getSectionsString() {
		// return the url sections as a string
		return implode('/', $this->getSections());
	}
	
	public function getRoot() {
		// split the current url by slashes and calculate the site root folder
		$self = explode('/', $_SERVER['PHP_SELF']);
		$array = array_splice($self, 0, -1);
		return implode('/', $array).'/';
	}
}
?>