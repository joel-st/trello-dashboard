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
	public $organization;

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->key = TVP_TD()->Options->TrelloIntegration->getApiKey();
		$this->token = TVP_TD()->Options->TrelloIntegration->getApiToken();
		$this->organization = TVP_TD()->Options->TrelloIntegration->getOrganization();
		$this->prefix = TVP_TD()->prefix;
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		add_action('wp_ajax_' . $this->prefix . '-trello-integration-test', [$this, 'integrationTest']);
		add_action('wp_ajax_nopriv_' . $this->prefix . '-trello-integration-test', [$this, 'integrationTest']);
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

	public function getFromOrganization($request = false, $args = false)
	{
		if (!$args) {
			$args = [];
		} elseif (!is_array($args)) {
			$args = [$args];
		}

		if (strstr($request, '?')) {
			$url = 'https://api.trello.com/1/organizations/' . $this->organization . '/' . $request . '&key=' . $this->key . '&token=' . $this->token;
		} else {
			$url = 'https://api.trello.com/1/organizations/' . $this->organization . '/' . $request . '?key=' . $this->key . '&token=' . $this->token;
		}

		$data = file_get_contents($url);

		return json_decode($data);
	}

	/**
	 * The ajax integration test function called by integrationTest(); in javascript `assets/scripts/admin.js`
	 */
	public function integrationTest()
	{
		// check integration
		$url = 'https://api.trello.com/1/members/me?key=' . $this->key . '&token=' . $this->token;

		$data = file_get_contents($url);

		if (empty($data) || !$parsedData = json_decode($data, true)) {
			header('HTTP/1.1 500 No Content');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(['message' => 'Connection failed.', 'code' => 401]));
		}

		header('HTTP/1.1 200 OK');
		header('Content-Type: application/json; charset=UTF-8');
		die(json_encode(['data' => json_encode($parsedData), 'code' => 200]));

		// Don't forget to always exit in the ajax function.
		wp_die();
	}
}
