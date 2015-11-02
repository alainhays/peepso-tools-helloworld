<?php
require_once(PeepSo::get_plugin_dir() . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'install.php');

class PeepSoHelloInstall extends PeepSoInstall
{

	// optional default settings
	protected $default_config = array(
		#'HELLO_WORLD' => '100',
	);

	public function plugin_activation()
	{
		// Set some settings
		$settings = PeepSoConfigSettings::get_instance();
		$settings->set_option('peepso_helloworld_use_custom_message', 0);
		$settings->set_option('peepso_helloworld_custom_message', 'Custom Hello World!');

		parent::plugin_activation();

		return (TRUE);
	}

	// optional DB table creation
	public static function get_table_data()
	{
		$aRet = array(
			'hello' => "
				CREATE TABLE `hello` (
					`hello_id`				BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,

					PRIMARY KEY (`hello_id`),
				) ENGINE=InnoDB",
		);

		 return $aRet;
	}

	// optional notification emails
	public function get_email_contents()
	{
		$emails = array(
			'email_hello' => "Hello World!",
		);

		 return $emails;
	}

	// optional page with shortcode
	protected function get_page_data()
	{
		// default page names/locations
		$aRet = array(
			'hello' => array(
				'title' => __('Hello', 'peepsohello'),
				'slug' => 'Hello World',
				'content' => '[peepso_hello]'
			),
		);

		return ($aRet);
	}
}