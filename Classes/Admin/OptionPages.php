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

			/**
			 * Members
			 */
			$this->slugMember => [
				'menu_title' => __('Members', 'tvp-trello-dashboard'),
				'page_title' => __('Members', 'tvp-trello-dashboard'),
				'parent_slug' => $this->optionSlug,
				'capability'  => 'edit_theme_options',
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
		if (!empty($this->menuItem)&& function_exists('acf_add_options_sub_page')) {
			foreach ($this->optionPages as $menuSlug => $page) {
				$page['menu_slug'] = $menuSlug;
				acf_add_options_sub_page($page);
			}
		}
	}
}
