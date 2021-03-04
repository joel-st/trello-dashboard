<?php

namespace TVP\TrelloDashboard\Member;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Role
{
	/**
	 * Class Properties
	 */
	public $role = '';

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->role = TVP_TD()->prefix . '-role';
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		add_action('init', [$this, 'addRole']);
		add_action('admin_menu', [$this, 'adminAreaRestrictions']);
	}

	/**
	 * Add the TVP Trello Member user role
	 */
	public function addRole()
	{
		// remove_role($this->role);
		add_role($this->role, 'TVP Trello Member', [ 'read' => true ]);
	}

	/**
	 * Restrictions for the TVP Trello Member user rolse
	 * Hides the Dashboard, so a logged in user with the TVP Trello Member user role only has access to his edit profile page
	 */
	public function adminAreaRestrictions()
	{
		global $current_user, $menu, $submenu;
		get_currentuserinfo();
		if (sizeof($current_user->caps) === 1 && isset($current_user->caps[$this->role]) && $current_user->caps[$this->role]) {
			reset($menu);
			$page = key($menu);
			while ((__('Dashboard') != $menu[$page][0]) && next($menu)) {
				$page = key($menu);
			}
			if (__('Dashboard') == $menu[$page][0]) {
				unset($menu[$page]);
			}
			reset($menu);
			$page = key($menu);
			while (!$current_user->has_cap($menu[$page][1]) && next($menu)) {
				$page = key($menu);
			}
			if (preg_match('#wp-admin/?(index.php)?$#', $_SERVER['REQUEST_URI']) && ('index.php' != $menu[$page][2])) {
				wp_redirect(get_option('siteurl'));
			}
		}
	}
}
