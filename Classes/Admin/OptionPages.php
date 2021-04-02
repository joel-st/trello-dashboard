<?php
namespace TVP\TrelloDashboard\Admin;

/**
 * Add menu item and subpages in the admin area to display option pages for the plugin configuration
 */

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class OptionPages
{
	/**
	 * Class Properties
	 */
	public $prefix = '';

	public $optionSlug = '';
	public $menuItem = [];

	public $slugTrelloIntegration = '';
	public $slugDashboardManager = '';
	public $slugMember = '';
	public $slugTrelloActions = '';
	public $slugTrelloLists = '';
	public $slugTrelloBoards = '';
	public $slugTrelloCards = '';
	public $optionPages = [];
	public $subPages = [];

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->prefix = TVP_TD()->prefix;

		$this->optionSlug = $this->prefix . '-options';
		$this->menuItem = [
			'menu_title' => __('Trello Dashboard', 'tvp-trello-dashboard'),
			'page_title' => __('Trello Dashboard', 'tvp-trello-dashboard'),
			'menu_slug' => $this->optionSlug,
			'capability' => 'edit_posts',
			'icon_url' => 'dashicons-admin-settings',
			'redirect' => true,
			'position' => TVP_TD()->menuPositionBase,
		];

		$this->slugTrelloIntegration = $this->optionSlug . '-trello-integration';
		$this->slugDashboardManager = $this->optionSlug . '-information-manager';
		$this->slugMember = $this->optionSlug . '-member';
		$this->slugTrelloActions = $this->optionSlug . '-trello-actions';
		$this->slugTrelloLists = $this->optionSlug . '-trello-lists';
		$this->slugTrelloBoards = $this->optionSlug . '-trello-boards';
		$this->slugTrelloCards = $this->optionSlug . '-trello-cards';
		$this->optionPages = [

		/**
		 * Trello Integration
		 */
			$this->slugTrelloIntegration => [
				'menu_title' => __('Trello Integration', 'tvp-trello-dashboard'),
				'page_title' => __('Trello Integration', 'tvp-trello-dashboard'),
				'parent_slug' => $this->optionSlug,
				'capability'  => 'edit_theme_options',
			],

			/**
			 * Dashboard Manager
			 */
			$this->slugDashboardManager => [
				'menu_title' => __('Dashboard Manager', 'tvp-trello-dashboard'),
				'page_title' => __('Dashboard Manager', 'tvp-trello-dashboard'),
				'parent_slug' => $this->optionSlug,
				'capability'  => 'edit_theme_options',
			],
		];

		$this->subPages = [
			/**
			 * Members
			 */
			$this->slugMember => [
				$this->slugTrelloIntegration,
				__('Members', 'tvp-trello-dashboard'),
				__('Members', 'tvp-trello-dashboard'),
				'edit_theme_options',
				$this->slugMember,
				[$this, 'memberRows']
			],
			$this->slugTrelloActions => [
				$this->slugTrelloIntegration,
				__('Trello Actions', 'tvp-trello-dashboard'),
				__('Trello Actions', 'tvp-trello-dashboard'),
				'edit_theme_options',
				'edit.php?post_type=tvptd-trello-action',
				null
			],
			$this->slugTrelloLists => [
				$this->slugTrelloIntegration,
				__('Trello Lists', 'tvp-trello-dashboard'),
				__('Trello Lists', 'tvp-trello-dashboard'),
				'edit_theme_options',
				'edit-tags.php?taxonomy=' . TVP_TD()->Trello->Action->listTaxonomy . '&post_type=' . TVP_TD()->Trello->Action->postType,
				null
			],
			$this->slugTrelloBoards => [
				$this->slugTrelloIntegration,
				__('Trello Boards', 'tvp-trello-dashboard'),
				__('Trello Boards', 'tvp-trello-dashboard'),
				'edit_theme_options',
				'edit-tags.php?taxonomy=' . TVP_TD()->Trello->Action->boardTaxonomy . '&post_type=' . TVP_TD()->Trello->Action->postType,
				null
			],
			$this->slugTrelloCards => [
				$this->slugTrelloIntegration,
				__('Trello Cards', 'tvp-trello-dashboard'),
				__('Trello Cards', 'tvp-trello-dashboard'),
				'edit_theme_options',
				'edit-tags.php?taxonomy=' . TVP_TD()->Trello->Action->cardTaxonomy . '&post_type=' . TVP_TD()->Trello->Action->postType,
				null
			],
		];
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		add_action('acf/init', [$this, 'addMenuItem']);
		add_action('acf/init', [$this, 'addOptionPages']);
		add_action('admin_menu', [$this, 'addMemberSubmenuPage'], 101); // prio 101 is higher than acf’s prio 99
	}

	/**
	 * Add ACF Options Page trough acf acf_add_options_page
	 * The options page appears like a regular WordPress admin menu item
	 */
	public function addMenuItem()
	{
		if (!empty($this->menuItem)&& function_exists('acf_add_options_page')) {
			acf_add_options_page($this->menuItem);
		}
	}

	/**
	 * Add ACF Options Sub Pages trough acf acf_add_options_sub_page
	 * The options sub pages appear like a regular WordPress admin sub menu items
	 */
	public function addOptionPages()
	{
		if (!empty($this->menuItem) && function_exists('acf_add_options_sub_page')) {
			foreach ($this->optionPages as $menuSlug => $page) {
				$page['menu_slug'] = $menuSlug;
				acf_add_options_sub_page($page);
			}
		}
	}

	/**
	 * Add WordPress add_submenu_page because this option page does not show acf fields
	 * instead it shows WP_List_Table markup
	 */
	public function addMemberSubmenuPage()
	{
		if (!empty($this->menuItem) && function_exists('add_submenu_page')) {
			foreach ($this->subPages as $menuSlug => $page) {
				add_submenu_page($page[0], $page[1], $page[2], $page[3], $page[4], $page[5]);
			}
		}
	}

	/**
	 * Output the WP_List_Table with user data for users with the user role TVP Trello Member
	 */
	public function memberRows()
	{
		echo '<div class="wrap">';
		echo '<h1 class="wp-heading-inline">'.__('TVP Trello Members', 'tvp-trello-dashboard').'</h1>';

		include plugin_dir_path(__DIR__) . '/Member/TVPUserList.php';
		$userList = new \TVPUserList();
		$userList->prepare_items();
		$userList->display();

		echo '</div>';
	}
}
