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
		$this->organization = TVP_TD()->Options->TrelloIntegration->getOrganizationName();
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
	 * Function to curl url
	 * We use curl 'cause file_get_contents returned
	 * failed to open stream: HTTP request failed! HTTP/1.1 426 Upgrade Required
	 * in PHP < 8.0
	 */
	public function curl($url)
	{
		// create curl resource
		$ch = curl_init();

		// set url
		curl_setopt($ch, CURLOPT_URL, $url);

		// return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// $output contains the output string
		$response = curl_exec($ch);

		// close curl resource to free up system resources
		curl_close($ch);

		return $response;
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
			$url = 'https://api.trello.com/1/' . $request . '&key=' . $this->key . '&token=' . $this->token;
		} else {
			$url = 'https://api.trello.com/1/' . $request . '?key=' . $this->key . '&token=' . $this->token;
		}

		$data = $this->curl($url);

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

		$data = $this->curl($url);

		return json_decode($data);
	}

	/**
	 * The ajax integration test function called by integrationTest(); in javascript `assets/scripts/admin.js`
	 */
	public function integrationTest()
	{
		$connected = @fsockopen("www.google.com", 80);
		if ($connected) {
			fclose($connected);
		} else {
			if (!connection_status()) {
				header('HTTP/1.1 500 No Connection');
				header('Content-Type: application/json; charset=UTF-8');
				die(json_encode(['message' => 'No Internet connection.', 'code' => 401]));
			}
		}

		$memberUrl = 'https://api.trello.com/1/members/me?key=' . $this->key . '&token=' . $this->token;
		$memberData = $this->curl($memberUrl);

		if (empty($memberData) || !$parsedMemberData = json_decode($memberData, true)) {
			header('HTTP/1.1 500 No Content');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(['message' => 'Invalid authentication details.', 'code' => 401]));
		}

		$organizationUrl = 'https://api.trello.com/1/organization/' . $this->organization . '?key=' . $this->key . '&token=' . $this->token;
		$organizationData = file_get_contents($organizationUrl);

		if (empty($organizationData) || !$parsedOrganizationData = json_decode($organizationData, true)) {
			header('HTTP/1.1 500 No Content');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(['message' => 'Organization not found.', 'code' => 401]));
		}

		if (isset($parsedOrganizationData["id"])) {
			update_field(TVP_TD()->Options->TrelloIntegration->optionPrefix . '-organization-id', $parsedOrganizationData["id"], 'options');
		}

		header('HTTP/1.1 200 OK');
		header('Content-Type: application/json; charset=UTF-8');
		die(json_encode(['member' => $memberData, 'organization' => $organizationData, 'code' => 200]));

		// Don't forget to always exit in the ajax function.
		wp_die();
	}
}
