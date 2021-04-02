<?php

namespace TVP\TrelloDashboard\View;

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
		// if ($background = get_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-signup-background', 'options')) {
		// 	$backgroundUrl = $background['url'];
		// 	$backgroundStyle = 'style="background-image: url('.$backgroundUrl.')"';
		// }
		$signup = '<div class="tvptd__dashboard tvptd__dashboard--signup">';

		$signup .= '<header class="tvptd__header">';
		$signup .= get_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-signup-pre-content', 'options');
		$signup .= '</header>'; // .tvptd__header

		$signup .= '<div class="tvptd__content">';
		$signup .= '<div class="tvptd__main">';
		$signup .= '<button class="button button--large" id="'.$this->prefix.'-with-trello">'._x('Login with Trello', 'Trello Dashboard login action', 'tvp-trello-dashboard').'</button>';
		$signup .= '</div>'; // .tvptd__main
		$signup .= '</div>'; // .tvptd__content

		$signup .= '</div>'; // .tvp-tvptd__dashboard

		if ($background = get_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-signup-background', 'options')) {
			$backgroundUrl = $background['url'];
			$backgroundStyle = 'style="background-image: url('.$backgroundUrl.')"';
			$signup .= '<div class="tvptd__background" ' . $backgroundStyle . '></div>';
		}

		return $signup;
	}
}
