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
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		add_action('admin_head', [$this, 'addUpadteMembers']);
	}

	public function addUpadteMembers()
	{
		if (strpos(get_current_screen()->base, TVP_TD()->Options->TrelloIntegration->slugTrelloIntegration) !== false) {
			$members = TVP_TD()->Trello->API->getFromOrganization('members');
			if ($members) {
				foreach ($members as $key => $member) {
					// check if user exists
					$userExists = false;

					$args = [
						'meta_key'   => TVP_TD()->Member->UserMeta->optionsPrefix . '-id',
						'meta_value' => $member->id,
					];

					$userExists = get_users($args);
					$userId = false;
					$user = false;

					if (empty($userExists)) {
						// if user does not exist, check if username already taken
						// if already taken, append number
						$username = $member->username;
						$originalName = $username;
						$suffix = 1;

						while (username_exists($username)) {
							$username = (string)$originalName . '_' . $suffix;
						}

						$userId = wp_create_user($username, bin2hex(random_bytes(16)), '');
						$user = get_user_by('id', $userId);
						$user->remove_role('subscriber');
					} else {
						$userId = (int)$userExists[0]->data->ID;
						$user = get_user_by('id', $userId);
					}

					if ($userId && $user) {
						$nameArray = explode(' ', $member->fullName);

						// update first name
						if (isset($nameArray[0]) && empty(get_user_meta($userId, 'first_name', true))) {
							update_user_meta($userId, 'first_name', $nameArray[0]);
						}

						// update last name
						if (isset($nameArray[1]) && empty(get_user_meta($userId, 'last_name', true))) {
							update_user_meta($userId, 'last_name', $nameArray[1]);
						}

						// add the role
						$user->add_role(TVP_TD()->Member->Role->role);

						// add meta fields
						update_field(TVP_TD()->Member->UserMeta->optionsPrefix . '-id', $member->id, 'user_' . $userId);
					}
				}
			}
		}
	}
}
