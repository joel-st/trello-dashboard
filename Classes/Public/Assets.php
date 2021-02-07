<?php

namespace TVP\TrelloDashboard\Public;

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
		add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
	}

	/**
	 * Register and enqueue a css style sheet and javascript for the admin area
	 */
	public function registerAssets()
	{
		wp_enqueue_style($this->prefix . '-public-css', plugin_dir_url(__FILE__) . '../../assets/styles/public.css', [], TVP_TD()->version);
		wp_enqueue_script($this->prefix . '-public-js', plugin_dir_url(__FILE__) . '../../assets/scripts/public.js', ['jquery'], true, TVP_TD()->version);
		wp_enqueue_script($this->prefix . '-public-css');
	}
}