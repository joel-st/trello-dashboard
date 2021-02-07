<?php

namespace TVP\TrelloDashboard\Admin;

/**
 * Register and enqueue a css style sheet and javascript for the admin area
 */

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
	 * Register and enqueue a css style sheet and javascript for the admin area
	 */
	public function registerAssets()
	{
		wp_enqueue_style($this->prefix . '-admin-css', plugin_dir_url(__FILE__) . '../../assets/styles/admin.css', [], TVP_TD()->version);
		wp_enqueue_script($this->prefix . '-admin-js', plugin_dir_url(__FILE__) . '../../assets/scripts/admin.js', ['jquery'], true, TVP_TD()->version);
		wp_enqueue_script($this->prefix . '-admin-css');
	}
}
