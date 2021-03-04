<?php

namespace TVP\TrelloDashboard\Admin;

/**
 * Register and enqueue a css style sheet and javascript for the admin area
 */

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Assets
{
	/**
	 * Class Properties
	 */
	public $prefix = '';

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->prefix = TVP_TD()->prefix;
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		add_action('admin_enqueue_scripts', [$this, 'registerAssets']);
	}

	/**
	 * Register and enqueue a css style sheet and javascript for the admin area if on plugins option pages
	 */
	public function registerAssets()
	{
		// javascript only on tvp trello dashboard screen
		if (strpos(get_current_screen()->base, TVP_TD()->prefix) !== false) {
			$deps = ['jquery'];
			wp_enqueue_script($this->prefix . '-admin-js', plugin_dir_url(__FILE__) . '../../assets/scripts/admin.js', $deps, true, TVP_TD()->version);
			wp_localize_script($this->prefix . '-admin-js', 'tvp_td_vars', [
				'ajax_url' => admin_url('admin-ajax.php'),
			]);
		}

		// css
		wp_enqueue_style($this->prefix . '-admin', plugin_dir_url(__FILE__) . '../../assets/styles/admin.css', [], TVP_TD()->version);
	}
}
