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

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->prefix = TVP_TD()->prefix . '-signup';
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
	}

	public function getSignUpContent()
	{
		$signup = '<div class="tvp-td__dashboard tvp-td__dashboard--signup">';

		$signup .= '<header class="tvp-td__header">';
		$signup .= get_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-signup-pre-content', 'options');
		$signup .= '</header>'; // .tvp-td__header

		$signup .= '<div class="tvp-td__content">';
		$signup .= '<div class="tvp-td__main">';
		$signup .= '<button id="'.$this->prefix.'-with-trello">'._x('Login with Trello', 'Trello Dashboard login action', 'tvp-trello-dashboard').'</button>';
		$signup .= '</div>'; // .tvp-td__main
		$signup .= '</div>'; // .tvp-td__content

		$signup .= '</div>'; // .tvp-tvp-td__dashboard

		return $signup;
	}
}
