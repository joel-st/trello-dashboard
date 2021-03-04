<?php

namespace TVP\TrelloDashboard\Trello;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class API
{
	/**
	 * Class Properties
	 */
	public $prefix = '';
	public $key;
	public $token;

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->key = TVP_TD()->Options->TrelloIntegration->getApiKey();
		$this->token = TVP_TD()->Options->TrelloIntegration->getApiToken();
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		// silence is golden
	}

	/**
	 * Getter function to run trello api requests
	 */
	public function get($request, $args = false)
	{
		if (!$args) {
			$args = [];
		} elseif (!is_array($args)) {
			$args = [$args];
		}

		if (strstr($request, '?')) {
			$url = 'https://api.trello.com' . $request . '&key=' . $this->key . '&token=' . $this->token;
		} else {
			$url = 'https://api.trello.com' . $request . '?key=' . $this->key . '&token=' . $this->token;
		}

		$data = file_get_contents($url);

		return json_decode($data);
	}
}
