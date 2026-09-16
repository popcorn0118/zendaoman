<?php

namespace WPDM\__;

abstract class Cron {

	public $payload;
	function __construct($payload) {
		$this->payload = $payload;
	}
	abstract static function getTitle();
	abstract function dispatch();

}
