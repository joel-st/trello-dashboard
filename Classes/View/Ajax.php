<?php

namespace TVP\TrelloDashboard\View;

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
		add_action('wp_ajax_' . $this->prefix . '-get-not-in-organization-content', [$this, 'getNotInOrganizationContent']);
		add_action('wp_ajax_nopriv_' . $this->prefix . '-get-not-in-organization-content', [$this, 'getNotInOrganizationContent']);
		add_action('wp_ajax_' . $this->prefix . '-get-dashboard-content', [$this, 'getDashboardContent']);
		add_action('wp_ajax_nopriv_' . $this->prefix . '-get-dashboard-content', [$this, 'getDashboardContent']);
		add_action('wp_ajax_' . $this->prefix . '-login', [$this, 'validateLogin']);
		add_action('wp_ajax_nopriv_' . $this->prefix . '-login', [$this, 'validateLogin']);
		add_action('wp_ajax_' . $this->prefix . '-logout', [$this, 'logout']);
		add_action('wp_ajax_nopriv_' . $this->prefix . '-logout', [$this, 'logout']);
		add_action('wp_ajax_' . $this->prefix . '-get-organization-overview', [$this, 'getOrganizationOverview']);
		add_action('wp_ajax_nopriv_' . $this->prefix . '-get-organization-overview', [$this, 'getOrganizationOverview']);
		add_action('wp_ajax_' . $this->prefix . '-get-organization-statistics', [$this, 'getOrganizationStatistics']);
		add_action('wp_ajax_nopriv_' . $this->prefix . '-get-organization-statistics', [$this, 'getOrganizationStatistics']);
	}

	public function getSignUpContent()
	{
		// if (!isset($_GET['nonce']) || ! wp_verify_nonce($_GET['nonce'], TVP_TD()->ajaxNonceKey . '-signup')) {
		// 	header('HTTP/1.1 401 Bad request');
		// 	header('Content-Type: application/json; charset=UTF-8');
		// 	die(json_encode(['message' => 'Ajax nonce error.', 'code' => 401]));
		// }

		$response = TVP_TD()->View->SignUp->getSignUpContent();

		header('HTTP/1.1 200 OK');
		header('Content-Type: text/html; charset=utf-8');
		die(json_encode(['html' => $response, 'code' => 200]));
		// Don't forget to always exit in the ajax function.
		wp_die();
	}

	public function getNotInOrganizationContent()
	{
		// if (!isset($_GET['nonce']) || ! wp_verify_nonce($_GET['nonce'], TVP_TD()->ajaxNonceKey . '-signup')) {
		// 	header('HTTP/1.1 401 Bad request');
		// 	header('Content-Type: application/json; charset=UTF-8');
		// 	die(json_encode(['message' => 'Ajax nonce error.', 'code' => 401]));
		// }

		$response = TVP_TD()->View->NotInOrganization->getNotInOrganizationContent();

		header('HTTP/1.1 200 OK');
		header('Content-Type: text/html; charset=utf-8');
		die(json_encode(['html' => $response, 'code' => 200]));
		// Don't forget to always exit in the ajax function.
		wp_die();
	}

	public function getDashboardContent()
	{
		// if (!isset($_GET['nonce']) || ! wp_verify_nonce($_GET['nonce'], TVP_TD()->ajaxNonceKey  . '-content')) {
		// 	header('HTTP/1.1 401 Bad request');
		// 	header('Content-Type: application/json; charset=UTF-8');
		// 	die(json_encode(['message' => 'Ajax nonce error.', 'code' => 401]));
		// }

		$response = TVP_TD()->View->Dashboard->getDashboardContent();

		header('HTTP/1.1 200 OK');
		header('Content-Type: text/html; charset=utf-8');
		die(json_encode(['html' => $response, 'code' => 200]));
		// Don't forget to always exit in the ajax function.
		wp_die();
	}

	public function validateLogin()
	{
		// if (!isset($_GET['nonce']) || ! wp_verify_nonce($_GET['nonce'], TVP_TD()->ajaxNonceKey  . '-login')) {
		// 	header('HTTP/1.1 401 Bad request');
		// 	header('Content-Type: application/json; charset=UTF-8');
		// 	die(json_encode(['message' => 'Ajax nonce error.', 'code' => 401]));
		// }

		if (!isset($_GET['member']) || empty($_GET['member'])) {
			header('HTTP/1.1 401 Bad request');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(['message' => 'No member specified.', 'code' => 401]));
		}

		if (!isset($_GET['membership']) || empty($_GET['membership'])) {
			header('HTTP/1.1 401 Bad request');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(['message' => 'No membership specified.', 'code' => 401]));
		}

		$member = $_GET['member'];
		$membership = $_GET['membership'];

		// check if user exists
		$response = [];
		$userExists = false;
		$args = [
			'meta_key'   => TVP_TD()->Member->UserMeta->optionsPrefix . '-id',
			'meta_value' => $member['id'],
		];
		$userExists = get_users($args);

		if ($userExists) {
			$userId = (int)$userExists[0]->data->ID;
		} else {
			$newUser = TVP_TD()->Trello->DataProcessor->addUpdateMember($membership);
			$userId = $newUser['userId'];
		}

		if ($userId && !empty($user = get_user_by('id', $userId))) {
			$processInfo['exists'] = true;
			$response['userId'] = $userId;
			$response['userName'] = $user->user_login;
			$response['trelloId'] = $member['id'];

			// update email since we only can get the e-mail from the trello authentication
			if (isset($member['email']) && empty(get_user_meta($userId, 'user_email', true))) {
				update_user_meta($userId, 'user_email', $member['email']);
			}
		} else {
			header('HTTP/1.1 401 Bad request');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(['message' => 'Error while getting user', 'code' => 401]));
		}

		if (!empty($response) && isset($response['userId']) && isset($response['userName']) && isset($response['trelloId'])) {
			wp_set_current_user($response['userId']);
			wp_set_auth_cookie($response['userId']);
			do_action('wp_login', $response['userName']);
			setcookie(TVP_TD()->authCookie, $response['trelloId'], time()+604800, '/' . TVP_TD()->View->Dashboard->slug . '/'); // 7 days
		}

		header('HTTP/1.1 200 OK');
		header('Content-Type: text/html; charset=utf-8');
		die(json_encode(['data' => $response, 'code' => 200]));

		// Don't forget to always exit in the ajax function.
		wp_die();
	}

	public function logout()
	{
		// if (!isset($_GET['nonce']) || ! wp_verify_nonce($_GET['nonce'], TVP_TD()->ajaxNonceKey  . '-logout')) {
		// 	header('HTTP/1.1 401 Bad request');
		// 	header('Content-Type: application/json; charset=UTF-8');
		// 	die(json_encode(['message' => 'Ajax nonce error.', 'code' => 401]));
		// }

		setcookie(TVP_TD()->authCookie, '', time() - 3600, '/' . TVP_TD()->View->Dashboard->slug . '/');
		wp_logout();

		header('HTTP/1.1 200 OK');
		header('Content-Type: text/html; charset=utf-8');
		die(json_encode(['data' => true, 'code' => 200]));

		// Don't forget to always exit in the ajax function.
		wp_die();
	}

	public function getOrganizationOverview()
	{
		$organizationOverview = '<section class="tvptd__widget-section">';
		$memberTotal = TVP_TD()->API->Member->getMemberTotal();
		$memberTotalAtLeastOneBoard = TVP_TD()->API->Action->getMemberTotalAtLeastOneBoard();
		$memberTotalAtLeastOneAction = TVP_TD()->API->Action->getMemberTotalAtLeastOneAction();
		$organizationOverview .= '<p>'.sprintf(__('Total boards: %s', 'tvp-trello-dashbaord'), '<b>'.TVP_TD()->API->Action->getBoardTotal().'</b>').'</p>';
		$organizationOverview .= '<p>'.sprintf(__('Total cards: %s', 'tvp-trello-dashbaord'), '<b>'.TVP_TD()->API->Action->getCardTotal().'</b>').'</p>';
		$organizationOverview .= '<p>'.sprintf(__('Total actions: %s', 'tvp-trello-dashbaord'), '<b>'.TVP_TD()->API->Action->getActionTotal().'</b>').'</p>';
		$organizationOverview .= '<p>'.sprintf(__('Total members: %s', 'tvp-trello-dashbaord'), '<b>'.$memberTotal.'</b>').'</p>';
		$organizationOverview .= '<p>'.sprintf(__('Total members joined at least 1 board: %s', 'tvp-trello-dashbaord'), '<b>' . $memberTotalAtLeastOneBoard . '</b> ('. round(100 * $memberTotalAtLeastOneBoard / $memberTotal) .'%)').'</p>';
		$organizationOverview .= '<p>'.sprintf(__('Total members performed at least 1 action: %s', 'tvp-trello-dashbaord'), '<b>'. $memberTotalAtLeastOneAction .'</b> ('. round(100 * $memberTotalAtLeastOneAction / $memberTotal) .'%)').'</p>';
		$organizationOverview .= '</section>'; // .tvptd__widget-section

		header('HTTP/1.1 200 OK');
		header('Content-Type: text/html; charset=utf-8');
		die(json_encode(['html' => $organizationOverview, 'code' => 200]));
	}

	public function getOrganizationStatistics()
	{
		$metaQueryDate = [
			[
				'value' => [date('Y-m-d', strtotime('-28 days')), date("Y-m-d", strtotime(date("d.m.Y")))],
				'compare' => 'BETWEEN',
			]
		];

		$organizationStatistics = '<section class="tvptd__widget-section">';
		$organizationStatistics .= '<p>'.sprintf(
			__('%1$s performed a total of %2$s in %3$s'),
			'<b>'.sprintf(__('%s people', 'tvp-trello-dashbaord'), TVP_TD()->API->Action->getMemberTotalAtLeastOneAction($metaQueryDate)).'</b>',
			'<b>'.sprintf(__('%s actions', 'tvp-trello-dashbaord'), TVP_TD()->API->Action->getActionTotal([
				[
					'key' => TVP_TD()->Trello->Action->optionPrefixAction . '-date',
					'value' => $metaQueryDate[0]['value'],
					'compare' => $metaQueryDate[0]['compare'],
				]
			])).'</b>',
			'<b>'.sprintf(__('%s teams', 'tvp-trello-dashbaord'), TVP_TD()->API->Action->getActionsOnBoardsTotal($metaQueryDate)).'</b>'
		).'</p>';
		$organizationStatistics .= '</section>'; // .tvptd__widget-section

		$organizationStatistics .= '<section class="tvptd__widget-section">';
		$organizationStatistics .= '<p><b>'.sprintf(__('Number of people added to the organization: %s', 'tvp-trello-dashbaord'), TVP_TD()->API->Member->getMemberAddedTotal($metaQueryDate)).'</b></p>';

		$members = TVP_TD()->API->Member->getMember($metaQueryDate);

		if (!empty($members)) {
			$organizationStatistics .= '<ul class="tvptd__widget-list tvptd__widget-list--members">';
			foreach ($members as $key => $member) {
				$organizationStatistics .= '<li class="tvptd__widget-list-item">';
				$organizationStatistics .= $member->user_login;
				$organizationStatistics .= '</li>';
			}
			$organizationStatistics .= '</ul>';
		}

		$organizationStatistics .= '</section>'; // .tvptd__widget-section

		$organizationStatistics .= '<section class="tvptd__widget-section">';
		$organizationStatistics .= '<p><b>'.sprintf(__('Out of the %s added, how many joined boards', 'tvp-trello-dashbaord'), sprintf(__('%s people', 'tvp-trello-dashbaord'), TVP_TD()->API->Member->getMemberAddedTotal($metaQueryDate))).'</b></p>';
		$memberAddedJoinedBoard = TVP_TD()->API->Action->getMemberAddedJoinedBoard($metaQueryDate);
		if (!empty($memberAddedJoinedBoard)) {
			$organizationStatistics .= '<ul class="tvptd__widget-list tvptd__widget-list--added-members-joined-board">';
			foreach ($memberAddedJoinedBoard as $boardName => $data) {
				$organizationStatistics .= '<li class="tvptd__widget-list-item">';
				$organizationStatistics .= $boardName . ': ' . $data['number'] . ' (' . $data['percentual'] . '%)';
				$organizationStatistics .= '</li>';
			}
			$organizationStatistics .= '</ul>';
		}
		// $organizationStatistics .= $memberAddedJoinedBoard['number'] . ' (' . $memberAddedJoinedBoard['percentual'] . '%)';
		$organizationStatistics .= '</section>'; // .tvptd__widget-section

		$organizationStatistics .= '<section class="tvptd__widget-section">';
		$organizationStatistics .= '<p><b>'.sprintf(__('Out of the %s added, how many performed actions', 'tvp-trello-dashbaord'), sprintf(__('%s people', 'tvp-trello-dashbaord'), TVP_TD()->API->Member->getMemberAddedTotal($metaQueryDate))).'</b></p>';
		$memberAddedPerformedActions = TVP_TD()->API->Action->getMemberAddedPerformedActions($metaQueryDate);
		if (!empty($memberAddedPerformedActions)) {
			$organizationStatistics .= '<ul class="tvptd__widget-list tvptd__widget-list--added-members-performed-actions">';
			foreach ($memberAddedPerformedActions as $key => $data) {
				$organizationStatistics .= '<li class="tvptd__widget-list-item">';
				if ($key == '0') {
					$organizationStatistics .= __('0 actions', 'tvp-trello-dashboard');
				} else {
					$organizationStatistics .= sprintf(_n('At least %s action', 'At least %s actions', (int)$key, 'tvp-trello-dashboard'), $key);
				}

				$organizationStatistics .= ': ' . $data['number'] . ' (' . $data['percentual'] . '%)';
				$organizationStatistics .= '</li>';
			}
			$organizationStatistics .= '</ul>';
		}
		$organizationStatistics .= '</section>'; // .tvptd__widget-section

		$organizationStatistics .= '<section class="tvptd__widget-section">';
		$organizationStatistics .= '<p><b>'.__('Number of actions within each board', 'tvp-trello-dashbaord').'</b></p>';
		$numberOfActionsOnBoards = TVP_TD()->API->Action->getNumberOfActionsOnBoards($metaQueryDate);
		if (!empty($numberOfActionsOnBoards)) {
			$organizationStatistics .= '<ul class="tvptd__widget-list tvptd__widget-list--number-of-actions-on-boards">';
			foreach ($numberOfActionsOnBoards as $name => $actions) {
				$organizationStatistics .= '<li class="tvptd__widget-list-item">';
				$organizationStatistics .= $name . ': ' . $actions;
				$organizationStatistics .= '</li>';
			}
			$organizationStatistics .= '</ul>';
		}
		$organizationStatistics .= '</section>'; // .tvptd__widget-section

		header('HTTP/1.1 200 OK');
		header('Content-Type: text/html; charset=utf-8');
		die(json_encode(['html' => $organizationStatistics, 'code' => 200]));
	}
}