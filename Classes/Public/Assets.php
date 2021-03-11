<?php

namespace TVP\TrelloDashboard\Public;

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
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		// add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
	}

	/**
	 * Register and enqueue a css style sheet and javascript for the admin area
	 */
	public function registerAssets()
	{
		if (TVP_TD()->Public->Dashboard->isDashboard()) {
			// css
			wp_enqueue_style($this->prefix . '-public-css', plugin_dir_url(__FILE__) . '../../assets/styles/public.css', [], TVP_TD()->version);

			// javascript
			wp_enqueue_script($this->prefix . '-public-js', plugin_dir_url(__FILE__) . '../../assets/scripts/public.js', ['jquery'], true, TVP_TD()->version);
			wp_enqueue_script($this->prefix . '-trello-client-js', plugin_dir_url(__FILE__) . '../../assets/scripts/trello-client.js', ['jquery'], true, TVP_TD()->version);
		}
	}
}
