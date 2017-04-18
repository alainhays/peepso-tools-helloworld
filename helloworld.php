<?php
/**
 * Plugin Name: PeepSo Tools: Hello World
 * Plugin URI: https://peepso.com
 * Description: Plugin template for development of PeepSo addons
 * Author: PeepSo
 * Author URI: https://peepso.com
 * Version: 2.0.9
 * Copyright: (c) 2015 PeepSo LLP. All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: peepso-hello-world
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */

class PeepSoHelloworld
{
	private static $_instance = NULL;

	const PLUGIN_NAME	 = 'PeepSo Hello World';
	const PLUGIN_VERSION = '2.0.9';
	const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE

	/*
	 * peepso_all_plugins filter integration
	 * PEEPSO_VER_MIN is the minimum REQUIRED version of PeepSo
	 * if PeepSo is BELOW this number, this plugin will be disabled
	 *
	 * PEEPSO_VER_MAX is the maximum TESTED version of PeepSo
	 * if PeepSo is ABOVE this number, there will be a warning rendered in the wp-admin
	 *
	 * If you do not define these two constants and hook into peepso_all_plugins anyway
	 * your plugin will be treated as in strict version lock, similar to all the Core plugins
	 */
	const PEEPSO_VER_MIN = '1.7.0';
	const PEEPSO_VER_MAX = '1.7.7';

	public $widgets = array(
		'PeepSoHelloworldWidgetHelloworld',
	);

	private function __construct()
	{
		add_action('peepso_init', array(&$this, 'init'));

		if (is_admin()) {
			add_action('admin_init', array(&$this, 'peepso_check'));
		}

		add_filter('peepso_all_plugins', array($this, 'filter_all_plugins'));

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
		PeepSo::add_autoload_directory(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
		PeepSoTemplate::add_template_directory(plugin_dir_path(__FILE__));

		if (is_admin()) {
			add_action('admin_init', array(&$this, 'peepso_check'));
			add_filter('peepso_admin_dashboard_tabs', array(&$this,'admin_dashboard_tabs'));
			add_filter('peepso_admin_config_tabs', array(&$this, 'admin_config_tabs'));
		} else {
			add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
		}



		add_filter('peepso_widgets', array(&$this, 'register_widgets'));
	}

	/**
	 * Plugin activation
	 * Check PeepSo
	 * @return bool
	 */
	public function activate()
	{
		if (!$this->peepso_check()) {
			return (FALSE);
		}

		require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'activate.php');
		$install = new PeepSoHelloInstall();
		$res = $install->plugin_activation();
		if (FALSE === $res) {
			// error during installation - disable
			deactivate_plugins(plugin_basename(__FILE__));
		}
		return (TRUE);

		return (TRUE);
	}

	/**
	 * Check if PeepSo class is present (ie the PeepSo plugin is installed and activated)
	 * If there is no PeepSo, immediately disable the plugin and display a warning
	 * Run license and new version checks against PeepSo.com
	 * @return bool
	 */
	public function peepso_check()
	{
		if (!class_exists('PeepSo')) {
			add_action('admin_notices', array(&$this, 'peepso_disabled_notice'));
			unset($_GET['activate']);
			deactivate_plugins(plugin_basename(__FILE__));
			return (FALSE);
		}

		return (TRUE);
	}

	/**
	 * Display a message about PeepSo not present
	 */
	public function peepso_disabled_notice()
	{
		?>
		<div class="error fade">
			<strong>
				<?php echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'peepso-hello-world'), self::PLUGIN_NAME);?>
			</strong>
		</div>
		<?php
	}

	/**
	 * Hooks into PeepSo Core for compatibility checks
	 * @param $plugins
	 * @return mixed
	 */
	public function filter_all_plugins($plugins)
	{
		$plugins[plugin_basename(__FILE__)] = get_class($this);
		return $plugins;
	}

	public function enqueue_scripts()
	{
		wp_enqueue_style('peepso-hello-world', plugin_dir_url(__FILE__) . 'assets/css/helloworld.css', array('peepso'), self::PLUGIN_VERSION, 'all');
		wp_enqueue_script('peepso-hello-world', plugin_dir_url(__FILE__) . 'assets/js/helloworld.js', array('peepso'), self::PLUGIN_VERSION, TRUE);
	}

	public function register_widgets($widgets)
	{
		foreach (scandir($widget_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'widgets' . DIRECTORY_SEPARATOR) as $widget) {
			if (strlen($widget)>=5) require_once($widget_dir . $widget);
		}

		return array_merge($widgets, $this->widgets);
	}


	/*
	 * Methods below are used solely as an integration with the PeepSo admin section
	 */
	/**
	 * Register an option to the PeepSo admin sidebar menu
	 * PS_FILTER
	 *
	 * @param $tabs array
	 * @return array
	 */
	public function admin_dashboard_tabs( $tabs )
	{
		$tabs['red']['helloworld'] = array(
			'slug' => 'peepso-hello-world',
			'menu' => __('Hello World', 'peepso-hello-world'),
			'icon' => 'info',
			'function' => array(&$this, 'admin_page'),
		);

		return $tabs;
	}

	/**
	 * Registers a tab in the PeepSo Config Toolbar
	 * PS_FILTER
	 *
	 * @param $tabs array
	 * @return array
	 */
	public function admin_config_tabs( $tabs )
	{
		$tabs['helloworld'] = array(
			'label' => __('Hello World Tab', 'peepso-hello-world'),
			'tab' => 'helloworld',
			'description' => __('Example Config Tab', 'peepso-hello-world'),
			'function' => 'PeepSoConfigSectionHelloworld',
		);

		return $tabs;
	}

	// @todo move to a separate class
	public function admin_page()
	{
		echo "Hello World Page!";
	}
}

PeepSoHelloworld::get_instance();

// EOF