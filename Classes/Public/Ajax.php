<?php

namespace TVP\TrelloDashboard\Public;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Ajax
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
		$this->prefix = TVP_TD()->prefix . '-public-ajax';
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		add_action('wp_ajax_' . $this->prefix . '-get-signup-content', [$this, 'getSignUpContent']);
		add_action('wp_ajax_nopriv_' . $this->prefix . '-get-signup-content', [$this, 'getSignUpContent']);
		add_action('wp_ajax_' . $this->prefix . '-get-dashboard-content', [$this, 'getDashboardContent']);
		add_action('wp_ajax_nopriv_' . $this->prefix . '-get-dashboard-content', [$this, 'getDashboardContent']);
		add_action('wp_ajax_' . $this->prefix . '-login', [$this, 'validateLogin']);
		add_action('wp_ajax_nopriv_' . $this->prefix . '-login', [$this, 'validateLogin']);
	}

	public function getSignUpContent()
	{
		if (!isset($_GET['nonce']) || ! wp_verify_nonce($_GET['nonce'], TVP_TD()->ajaxNonceKey)) {
			header('HTTP/1.1 401 Bad request');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(['message' => 'Ajax nonce error.', 'code' => 401]));
		}

		$response = TVP_TD()->Public->SignUp->getSignUpContent();

		header('HTTP/1.1 200 OK');
		header('Content-Type: text/html; charset=utf-8');
		die(json_encode(['html' => $response, 'code' => 200]));
		// Don't forget to always exit in the ajax function.
		wp_die();
	}

	public function getDashboardContent()
	{
		if (!isset($_GET['nonce']) || ! wp_verify_nonce($_GET['nonce'], TVP_TD()->ajaxNonceKey)) {
			header('HTTP/1.1 401 Bad request');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(['message' => 'Ajax nonce error.', 'code' => 401]));
		}

		$response = TVP_TD()->Public->Dashboard->getDashboardContent();

		header('HTTP/1.1 200 OK');
		header('Content-Type: text/html; charset=utf-8');
		die(json_encode(['html' => $response, 'code' => 200]));
		// Don't forget to always exit in the ajax function.
		wp_die();
	}

	public function validateLogin()
	{
		if (!isset($_GET['nonce']) || ! wp_verify_nonce($_GET['nonce'], TVP_TD()->ajaxNonceKey)) {
			header('HTTP/1.1 401 Bad request');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(['message' => 'Ajax nonce error.', 'code' => 401]));
		}

		if (!isset($_GET['member'])) {
			header('HTTP/1.1 401 Bad request');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(['message' => 'No member specified.', 'code' => 401]));
		}

		$member = $_GET['member'];

		if (!isset($member['idOrganizations']) || ! in_array(TVP_TD()->Options->TrelloIntegration->getOrganizationId(), $member['idOrganizations'])) {
			header('HTTP/1.1 401 Bad request');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(['message' => 'Member not in organisation.', 'code' => 401]));
		}

		$processed = TVP_TD()->Trello->DataProcessor->addUpdateMember($member);

		if (!empty($processed) && isset($processed['userId']) && isset($processed['userName']) && isset($processed['trelloId'])) {
			wp_set_current_user($processed['userId']);
			wp_set_auth_cookie($processed['userId']);
			do_action('wp_login', $processed['userName']);
			setcookie(TVP_TD()->authCookie, $processed['trelloId'], time()+604800, '/' . TVP_TD()->Public->Dashboard->slug . '/'); // 7 days
		}

		header('HTTP/1.1 200 OK');
		header('Content-Type: text/html; charset=utf-8');
		die(json_encode(['data' => true, 'code' => 200]));

		// Don't forget to always exit in the ajax function.
		wp_die();
	}
}
