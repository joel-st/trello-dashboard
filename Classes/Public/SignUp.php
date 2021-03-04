<?php

namespace TVP\TrelloDashboard\Public;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class SignUp
{
	/**
	 * Class Properties
	 */
	public $prefix = '';
	public $slugSignup = '';

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->slugSignUp = 'signup';
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		add_action('wp_head', [$this, 'signUpRedirect']);
	}

	public function signUpRedirect()
	{
		global $wp_query;

		$dashboardPage = get_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-dashboard-page', 'options');
		$signUpUrl = esc_url(get_permalink($dashboardPage) . TVP_TD()->Public->SignUp->slugSignUp);

		$baseUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'];
		$currentUrl = $baseUrl . $_SERVER['REQUEST_URI'];

		if ($wp_query->is_404 && $currentUrl === $signUpUrl) {
			$wp_query->is_404 = true;
			$wp_query->is_page = true;
			header('HTTP/1.1 200 OK');
			$this->signUpTemplate();
			exit;
		}
	}

	public function signUpTemplate()
	{
		echo 'signup';
	}
}
