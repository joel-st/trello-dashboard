<?php

namespace TVP\TrelloDashboard\Trello;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class DataProcessor
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
		$this->prefix = TVP_TD()->prefix . '-data-processor';
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		add_action('wp_ajax_' . $this->prefix, [$this, 'ajaxDataProcessor']);
		add_action('wp_ajax_nopriv_' . $this->prefix, [$this, 'ajaxDataProcessor']);
	}

	public function addUpdateMembers()
	{
		$members = TVP_TD()->Trello->API->getFromOrganization('members');
		$processed = [];

		if ($members) {
			foreach ($members as $key => $member) {
				$member = (array)$member;
				$processed[] = $this->addUpdateMember($member);
			}
		} else {
			return false;
		}

		return $processed;
	}

	public function addUpdateMember($member)
	{
		if (!empty($member)) {
			$processInfo = [
				'trelloId' => $member['id'],
				'added' => false,
				'updated' => false,
				'exist' => false,
				'errors' => [],
				'deleted' => false,
			];
			// check if user exists
			$userExists = false;

			$args = [
				'meta_key'   => TVP_TD()->Member->UserMeta->optionsPrefix . '-id',
				'meta_value' => $member['id'],
			];

			$userExists = get_users($args);
			$userId = false;
			$user = false;

			if (empty($userExists)) {
				// if user does not exist, check if username already taken
				// if already taken, append number
				$username = $member['username'];
				$originalName = $username;
				$suffix = 1;

				while (username_exists($username)) {
					$username = (string)$originalName . '_' . $suffix;
				}

				$userId = wp_create_user($username, bin2hex(random_bytes(16)), '');

				if ($userId) {
					$processInfo['added'] = true;
					$user = get_user_by('id', $userId);
					$user->remove_role('subscriber');
				}
			} else {
				$userId = (int)$userExists[0]->data->ID;

				if ($userId) {
					$processInfo['exist'] = true;
					$user = get_user_by('id', $userId);
				}
			}

			$processInfo['userId'] = $userId;
			$processInfo['userName'] = $user->user_login;
			$processInfo['editLink'] = get_edit_user_link($userId);

			if ($userId && $user) {
				$processInfo['userObject'] = $user;
				$updatedFields = [];
				$nameArray = explode(' ', $member['fullName']);

				// update first name
				if (isset($nameArray[0]) && empty(get_user_meta($userId, 'first_name', true))) {
					update_user_meta($userId, 'first_name', $nameArray[0]);
					$updatedFields['first_name'] = $nameArray[0];
					$processInfo['updated'] = true;
				}

				// update last name
				if (isset($nameArray[1]) && empty(get_user_meta($userId, 'last_name', true))) {
					update_user_meta($userId, 'last_name', $nameArray[1]);
					$updatedFields['last_name'] = $nameArray[0];
					$processInfo['updated'] = true;
				}

				// update email
				if (isset($member['email']) && empty(get_user_meta($userId, 'user_email', true))) {
					update_user_meta($userId, 'user_email', $member['email']);
					$updatedFields['user_email'] = $nameArray[0];
					$processInfo['updated'] = true;
				}

				// add the role
				$user->add_role(TVP_TD()->Member->Role->role);

				// add meta fields
				update_field(TVP_TD()->Member->UserMeta->optionsPrefix . '-id', $member['id'], 'user_' . $userId);

				$processInfo['updatedFields'] = $updatedFields;
			}

			return $processInfo;
		}
	}

	public function ajaxDataProcessor()
	{
		if (isset($_GET['request'])) {
			$response = false;

			switch ($_GET['request']) {
				case 'addUpdateMembers':
					$response = $this->addUpdateMembers();
					break;
				default:
					header('HTTP/1.1 500 Request not specified');
					header('Content-Type: application/json; charset=UTF-8');
					die(json_encode(['message' => 'Request not specified.', 'code' => 401]));
					break;
			}

			header('HTTP/1.1 200 OK');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(['data' => json_encode($response), 'code' => 200]));
		} else {
			header('HTTP/1.1 500 Request not specified');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(['message' => 'Request not specified.', 'code' => 401]));
		}

		// Don't forget to always exit in the ajax function.
		wp_die();
	}
}
