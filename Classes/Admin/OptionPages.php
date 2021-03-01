<?php
namespace TVP\TrelloDashboard\Admin;

/**
 * Add menu item and subpages in the admin area to display option pages for the plugin configuration
 */

class OptionPages
{
	/**
	 * Class Properties
	 */
	public $prefix = '';

	public $optionSlug = '';
	public $menuItem = [];

	public $slugTrelloIntegration = '';
	public $slugInformationManager = '';
	public $slugMember = '';
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
		];

		$this->slugTrelloIntegration = $this->optionSlug . '-trello-integration';
		$this->slugInformationManager = $this->optionSlug . '-information-manager';
		$this->slugMember = $this->optionSlug . '-member';
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
			 * Useful Information Manager
			 */
			$this->slugInformationManager => [
				'menu_title' => __('Information Manager', 'tvp-trello-dashboard'),
				'page_title' => __('Information Manager', 'tvp-trello-dashboard'),
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
		// add_action('acf/init', [$this, 'addTrelloOptions']);
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
