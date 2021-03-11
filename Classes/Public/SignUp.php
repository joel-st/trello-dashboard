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
		$signup = '<h1 style="text-align: center;">TVP Trello Dashbaord</h1>';
		$signup .= '<h3 style="text-align: center;">Login with Trello.</h3>';
		$signup .= '<button id="'.$this->prefix.'-with-trello">Login</button>';
		return $signup;
	}
}
