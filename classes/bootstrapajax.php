<?php

class PeepSoBOOTSTRAPAjax implements PeepSoAjaxCallback
{
	private static $_instance = NULL;

	private function __construct() {}

	public static function get_instance()
	{
		if (NULL === self::$_instance) {
			self::$_instance = new self();
		}
		return (self::$_instance);
	}

	public function BOOTSTRAP(PeepSoAjaxResponse $resp)
	{
		$peepsoInput = new PeepSoInput();
		#$peepsoActivity = PeepSoActivity::get_instance();

		#$user_id = PeepSo::get_user_id();
		#$peepsoActivity->set_user_id($user_id);

		$BOOTSTRAP = $peepsoInput->get_int('BOOTSTRAP', 'BOOTSTRAP');


		$resp->success(TRUE);
		$resp->set('BOOTSTRAP', $BOOTSTRAP);
	}
}