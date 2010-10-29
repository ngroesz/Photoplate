<?php

class BuilderException extends Exception {
	public $error;
	function __construct($error) {
		$this->error = $error;
	}
}
