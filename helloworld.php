<?php
/**
 * Plugin Name: PeepSoHelloWorld
 * Plugin URI: https://peepso.com
 * Description: Plugin template for development of PeepSo addons
 * Author: PeepSo
 * Author URI: https://peepso.com
 * Version: 1.5.1
 * Copyright: (c) 2015 PeepSo All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: peepsohelloworld
 * Domain Path: /language
 *
 * This software contains GPLv2 or later software courtesy of PeepSo.com, Inc
 *
 * PeepSoHelloWorld is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * PeepSoHelloWorld is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY. See the
 * GNU General Public License for more details.
 */


class PeepSoHelloworld
{
	private static $_instance = NULL;

	const PLUGIN_VERSION = '1.5.1';
	const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE

	public $widgets = array(
		'PeepSoHelloworldWidgetHelloworld',
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
		PeepSo::add_autoload_directory(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
		PeepSoTemplate::add_template_directory(plugin_dir_path(__FILE__));

		if (is_admin()) {
			add_action('admin_init', array(&$this, 'check_peepso'));
			add_filter('peepso_admin_dashboard_tabs', array(&$this,'admin_dashboard_tabs'));
			add_filter('peepso_admin_config_tabs', array(&$this, 'admin_config_tabs'));
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
		$install = new PeepSoHelloInstall();
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
		wp_enqueue_style('peepsohelloworld', plugin_dir_url(__FILE__) . 'assets/helloworld.css', array('peepso'), self::PLUGIN_VERSION, 'all');
		wp_enqueue_script('peepsohelloworld', plugin_dir_url(__FILE__) . 'assets/helloworld.js', array('peepso'), self::PLUGIN_VERSION, TRUE);
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
			'menu' => __('Hello World', 'peepsohelloworld'),
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
			'label' => __('Hello World Tab', 'peepsohelloworld'),
			'tab' => 'helloworld',
			'description' => __('Example Config Tab', 'peepsohelloworld'),
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