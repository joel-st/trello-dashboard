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
		$memberUrl = 'https://api.trello.com/1/members/me?key=' . $this->key . '&token=' . $this->token;
		$memberData = file_get_contents($memberUrl);

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
