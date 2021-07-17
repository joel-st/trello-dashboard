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
	public $editorRole = '';

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->role = TVP_TD()->prefix . '-role';
		$this->editorRole = TVP_TD()->prefix . '-editor-role';
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		add_action('init', [$this, 'addRole']);
		add_action('admin_menu', [$this, 'adminAreaRestrictions']);
		add_action('user_profile_update_errors', [$this, 'suppressEmptyEmailError'], 10, 3);
		add_action('admin_bar_menu', [$this, 'addToolBarItem'], 100);
		add_action('admin_body_class', [$this, 'adminBodyClass']);
	}

	/**
	 * Add body class to modify admin area based on role
	 */
	public function adminBodyClass($classes)
	{
		$userObject = wp_get_current_user();

		if (in_array($this->role, $userObject->roles)) {
			// Add a leading space and a trailing space.
			$classes .= ' ' . $this->role . ' ';
		}

		if (in_array($this->editorRole, $userObject->roles)) {
			// Add a leading space and a trailing space.
			$classes .= ' ' . $this->editorRole . ' ';
		}

		if (in_array('administrator', $userObject->roles)) {
			// Add a leading space and a trailing space.
			$classes .= ' ' . 'administrator' . ' ';
		}

		return $classes;
	}

	/**
	 * Add the TVP Trello Member user role
	 */
	public function addRole()
	{
		// if role does not exist
		if (!$GLOBALS['wp_roles']->is_role($this->role)) {
			add_role($this->role, 'TVP Trello Member', [ 'read' => true ]);
		}

		if (!$GLOBALS['wp_roles']->is_role($this->editorRole)) {
			add_role($this->editorRole, 'TVP Trello Editor', [ 'read' => true ]);
		}

		// add custom role caps to admin
		// get the the role object
		$adminRole = get_role('administrator');
		// grant the unfiltered_html capability
		$adminRole->add_cap($this->role, true);
		$adminRole->add_cap($this->editorRole, true);
	}

	/**
	 * Restrictions for the TVP Trello Member user rolse
	 * Hides the Dashboard, so a logged in user with only the TVP Trello Member user role only has access to his edit profile page
	 */
	public function adminAreaRestrictions()
	{
		global $current_user, $menu, $submenu;
		get_currentuserinfo();

		if (! in_array('administrator', $current_user->roles)) {
			reset($menu);
			$page = key($menu);
			while (( __('Dashboard') != $menu[$page][0] ) && next($menu)) {
				$page = key($menu);
			}
			if (__('Dashboard') == $menu[$page][0]) {
				unset($menu[$page]);
			}
			reset($menu);
			$page = key($menu);
			while (! $current_user->has_cap($menu[$page][1]) && next($menu)) {
				$page = key($menu);
			}
			if (preg_match('#wp-admin/?(index.php)?$#', $_SERVER['REQUEST_URI']) &&
			( 'index.php' != $menu[$page][2] )) {
				wp_redirect(get_option('siteurl') . '/wp-admin/edit.php');
			}
		}
	}

	/**
	 * Since we have no access to the member email via the trello memberships when we create a new WordPress user,
	 * we have to suppress the empty email error on profile pages for the trello users.
	 */
	function suppressEmptyEmailError($errors, $update, $user)
	{
		if (isset($user->ID)) {
			$userObject = get_user_by('id', $user->ID);
			if (in_array($this->role, $userObject->roles)) {
				$errors->remove('empty_email');
			}
		}
	}

	/**
	 * Add toolbar item to go to the trello dashboard on trello user profiles and trello plugin optionpages
	 */
	public function addToolBarItem($adminBar)
	{
		if (is_admin()) {
			if (strpos(get_current_screen()->base, TVP_TD()->Admin->OptionPages->optionSlug) !== false || get_current_screen()->base === 'profile') {
				$userObject = wp_get_current_user();

				if (in_array($this->role, $userObject->roles)) {
					$adminBar->add_menu([
						'id'    => TVP_TD()->prefix . '-go-to-dashboard',
						'title' => _x('Trello Dashboard', 'Admin bar go to dashboard link', 'tvp-trello-dashboard'),
						'href'  => TVP_TD()->View->Dashboard->getPermalink(),
						'meta'  => [
							'title' => _x('Go to Dashboard', 'Admin bar go to dashboard link meta title', 'tvp-trello-dashboard'),
						],
					]);
				}
			}
		}
	}
}
