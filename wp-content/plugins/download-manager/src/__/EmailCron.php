<?php

namespace WPDM\__;

class EmailCron extends Cron {

	function __construct($payload) {
		parent::__construct($payload);
	}

	static function getTitle() {
		return 'Email Cron';
	}
	function dispatch() {
		$template = __::valueof($this->payload, 'template');
		$params = __::valueof($this->payload, 'params');
		Email::send($template, $params);
	}
}
