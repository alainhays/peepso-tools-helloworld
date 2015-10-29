<?php
/**
 * Plugin Name: PeepSoBOOTSTRAP
 * Plugin URI: https://jwr.sk
 * Description: BOOTSTRAP
 * Author: Matt Jaworski
 * Author URI: https://jwr.sk
 * Version: 1.0.0
 * Copyright: (c) 2015 Matt Jaworski All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: BOOTSTRAP
 * Domain Path: /language
 *
 * This software contains GPLv2 or later software courtesy of PeepSo.com, Inc
 *
 */

class PeepSoBOOTSTRAP
{
	private static $_instance = NULL;

	const PLUGIN_VERSION = '1.0.0';
	const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE

	public $widgets = array(
		'PeepSoBOOTSTRAPWidgetBOOTSTRAP',
	);

	private function __construct()
	{
		add_action('peepso_init', array(&$this, 'init'));

		if (is_admin()) {
			add_action('admin_init', array(&$this, 'check_peepso'));
		}

		register_activation_hook(__FILE__, array(&$this, 'activate'));
	}

	public static function get_instance()
	{
		if (NULL === self::$_instance) {
			self::$_instance = new self();
		}
		return (self::$_instance);
	}

	public function init()
	{
		if (is_admin()) {
			add_action('admin_init', array(&$this, 'check_peepso'));
		} else {
			add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
		}

		add_filter('peepso_widgets', array(&$this, 'register_widgets'));
	}


	public function check_peepso()
	{
		if (!class_exists('PeepSo'))
		{
			if (is_plugin_active(plugin_basename(__FILE__))) {
				// deactivate the plugin
				deactivate_plugins(plugin_basename(__FILE__));
				// display notice for admin
				add_action('admin_notices', array(&$this, 'disabled_notice'));
				if (isset($_GET['activate'])) {
					unset($_GET['activate']);
				}
			}
			return (FALSE);
		}

		return (TRUE);
	}

	public function activate()
	{
		if (!$this->check_peepso()) {
			return (FALSE);
		}

		require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'activate.php');
		$install = new PeepSoBOOTSTRAPInstall();
		$res = $install->plugin_activation();
		if (FALSE === $res) {
			// error during installation - disable
			deactivate_plugins(plugin_basename(__FILE__));
		}

		return (TRUE);
	}

	public function disabled_notice()
	{
		echo '<div class="error fade">';
		echo
		'<strong>' , self::PLUGIN_NAME , ' ' ,
		__('plugin requires the PeepSo plugin to be installed and activated.', 'peepso'),
		'</a>',
		'</strong>';
		echo '</div>';
	}

	public function enqueue_scripts()
	{
		wp_enqueue_style('BOOTSTRAP', plugin_dir_url(__FILE__) . 'assets/BOOTSTRAP.css', array('peepso'), self::PLUGIN_VERSION, 'all');
		wp_enqueue_script('BOOTSTRAP', plugin_dir_url(__FILE__) . 'assets/BOOTSTRAP.js', array('peepso'), self::PLUGIN_VERSION, TRUE);
	}

	public function register_widgets($widgets)
	{
		foreach (scandir($widget_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'widgets' . DIRECTORY_SEPARATOR) as $widget) {
			if (strlen($widget)>=5) require_once($widget_dir . $widget);
		}

		return array_merge($widgets, $this->widgets);
	}
}

PeepSoBOOTSTRAP::get_instance();

// EOF