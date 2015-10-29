<?php
require_once(PeepSo::get_plugin_dir() . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'install.php');

class PeepSoBOOTSTRAPInstall extends PeepSoInstall
{

	// optional default settings
	protected $default_config = array(
		#'BOOTSTRAP_BOOTSTRAP' => '100',
	);

	public function plugin_activation()
	{
		// Set some settings
		$settings = PeepSoConfigSettings::get_instance();
		#$settings->set_option('page_friends', 'friends');

		parent::plugin_activation();

		return (TRUE);
	}

	// optional DB table creation
	public static function get_table_data()
	{
		$aRet = array(
			'BOOTSTRAP' => "
				CREATE TABLE `BOOTSTRAP` (
					`BOOTSTRAP_id`				BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,

					PRIMARY KEY (`BOOTSTRAP_id`),
				) ENGINE=InnoDB",
		);

		# return $aRet;
	}

	// optional notification emails
	public function get_email_contents()
	{
		$emails = array(
			'email_BOOTSTRAP' => "BOOTSTRAP",
		);

		# return $emails;
	}

	// optional page with shortcode
	protected function get_page_data()
	{
		// default page names/locations
		$aRet = array(
			'BOOTSTRAP' => array(
				'title' => __('BOOTSTRAP', 'msgso'),
				'slug' => 'BOOTSTRAP',
				'content' => '[peepso_BOOTSTRAP]'
			),
		);

		#return ($aRet);
	}
}