<?php

namespace TVP\TrelloDashboard\Options;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class DashboardManager
{
	/**
	 * Class Properties
	 */
	public $optionPages = [];
	public $slugDashboardManager = '';
	public $optionPrefix = '';

	public $options = [];

	public $dashboardMetaboxId = '';

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->optionPages = TVP_TD()->Admin->OptionPages->optionPages;
		$this->slugDashboardManager = TVP_TD()->Admin->OptionPages->slugDashboardManager;
		$this->optionPrefix = $this->slugDashboardManager;

		$this->dashboardMetaboxId = $this->optionPrefix . '-dashboard-metabox';

		$this->options = [
			'key' => $this->optionPrefix . '-text-fields',
			'title' => __('Text', 'tvp-trello-dashboard'),
			'fields' => [
				[
					'key' => $this->optionPrefix . '-dashboard-page',
					'name' => $this->optionPrefix . '-dashboard-page',
					'label' => __('Dashboard Page', 'tvp-trello-dashboard'),
					'type' => 'post_object',
					'allow_null' => 1,
					'instructions' => 'Only logged in users with the TVP Trello Member user role can access this page.'
				],
				[
					'key' => $this->optionPrefix . '-dashboard-pre-content',
					'name' => $this->optionPrefix . '-dashboard-pre-content',
					'label' => __('Dashboard Page', 'tvp-trello-dashboard'),
					'type' => 'wysiwyg',
				],
				[
					'key' => $this->optionPrefix . '-latest-news',
					'name' => $this->optionPrefix . '-latest-news',
					'label' => __('Latest News', 'tvp-trello-dashboard'),
					'type' => 'repeater',
					'layout' => 'block',
					'collapsed' => $this->optionPrefix . '-latest-news-label',
					'sub_fields' => [
						[
							'key' => $this->optionPrefix . '-latest-news-label',
							'name' => $this->optionPrefix . '-latest-news-label',
							'label' => __('Label', 'tvp-trello-dashboard'),
							'type' => 'text',
							'required' => true,
							'wrapper' => [
								'width' => 50,
							]
						],
						[
							'key' => $this->optionPrefix . '-latest-news-link',
							'name' => $this->optionPrefix . '-latest-news-link',
							'label' => __('Link', 'tvp-trello-dashboard'),
							'type' => 'url',
							'required' => true,
							'wrapper' => [
								'width' => 50,
							]
						]
					]
				],
				[
					'key' => $this->optionPrefix . '-useful-information',
					'name' => $this->optionPrefix . '-useful-information',
					'label' => __('Useful Information', 'tvp-trello-dashboard'),
					'type' => 'repeater',
					'layout' => 'block',
					'collapsed' => $this->optionPrefix . '-useful-information-label',
					'sub_fields' => [
						[
							'key' => $this->optionPrefix . '-useful-information-label',
							'name' => $this->optionPrefix . '-useful-information-label',
							'label' => __('Label', 'tvp-trello-dashboard'),
							'type' => 'text',
							'required' => true,
							'wrapper' => [
								'width' => 50,
							]
						],
						[
							'key' => $this->optionPrefix . '-useful-information-link',
							'name' => $this->optionPrefix . '-useful-information-link',
							'label' => __('Link', 'tvp-trello-dashboard'),
							'type' => 'url',
							'required' => true,
							'wrapper' => [
								'width' => 50,
							]
						]
					]
				],
				[
					'key' => $this->optionPrefix . '-volunteer-resources',
					'name' => $this->optionPrefix . '-volunteer-resources',
					'label' => __('Volunteer Resources', 'tvp-trello-dashboard'),
					'type' => 'repeater',
					'layout' => 'block',
					'collapsed' => $this->optionPrefix . '-volunteer-resources-label',
					'sub_fields' => [
						[
							'key' => $this->optionPrefix . '-volunteer-resources-label',
							'name' => $this->optionPrefix . '-volunteer-resources-label',
							'label' => __('Label', 'tvp-trello-dashboard'),
							'type' => 'text',
							'required' => true,
							'wrapper' => [
								'width' => 50,
							]
						],
						[
							'key' => $this->optionPrefix . '-volunteer-resources-link',
							'name' => $this->optionPrefix . '-volunteer-resources-link',
							'label' => __('Link', 'tvp-trello-dashboard'),
							'type' => 'url',
							'required' => true,
							'wrapper' => [
								'width' => 50,
							]
						]
					]
				],
				[
					'key' => $this->optionPrefix . '-help-needed',
					'name' => $this->optionPrefix . '-help-needed',
					'label' => __('Help Needed', 'tvp-trello-dashboard'),
					'type' => 'repeater',
					'layout' => 'block',
					'collapsed' => $this->optionPrefix . '-help-needed-label',
					'sub_fields' => [
						[
							'key' => $this->optionPrefix . '-help-needed-label',
							'name' => $this->optionPrefix . '-help-needed-label',
							'label' => __('Label', 'tvp-trello-dashboard'),
							'type' => 'text',
							'required' => true,
							'wrapper' => [
								'width' => 50,
							]
						],
						[
							'key' => $this->optionPrefix . '-help-needed-link',
							'name' => $this->optionPrefix . '-help-needed-link',
							'label' => __('Link', 'tvp-trello-dashboard'),
							'type' => 'url',
							'required' => true,
							'wrapper' => [
								'width' => 50,
							]
						]
					]
				],
			],
			'location' => [
				[
					[
						'param' => 'options_page',
						'operator' => '==',
						'value' => $this->slugDashboardManager,
					],
				],
			],
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
		];
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		add_action('acf/init', [$this, 'addOptions']);
		add_action('get_header', [$this, 'restrictDashboardAccess']);

		// metabox
		add_action('acf/input/admin_head', [$this, 'registerDashboardMetabox'], 10);

		//Adds a page-state to the page-list
		add_action('display_post_states', [$this, 'dashboardPageState'], 10, 2);
	}

	/**
	 * Add ACF Options to Sub Pages trough acf acf_add_local_field_group
	 */
	public function addOptions()
	{
		if (isset($this->optionPages[$this->slugDashboardManager]) && function_exists('acf_add_local_field_group')) {
			acf_add_local_field_group($this->options);
		}
	}

	public function restrictDashboardAccess()
	{
		global $post;
		$currentUser = wp_get_current_user();
		$roles = [TVP_TD()->Member->Role->role, 'administrator'];
		$dashboardPage = get_field($this->optionPrefix . '-dashboard-page', 'options');

		if (!empty($post) && !empty($dashboardPage) && $post->ID === $dashboardPage->ID) {
			if (!is_user_logged_in() || empty(array_intersect($roles, $currentUser->roles))) {
				wp_redirect(esc_url(get_permalink($dashboardPage) . TVP_TD()->Public->SignUp->slugSignUp));
			}
		}
	}

	/**
	 * Register a metabox to show infos of the dashboard
	 */
	public function registerDashboardMetabox()
	{
		if (strpos(get_current_screen()->base, $this->slugDashboardManager) !== false) {
			// Add meta box
			add_meta_box($this->dashboardMetaboxId, __('Dashboard Information', 'tvp-trello-dashboard'), function () {
				$dashboardPage = get_field($this->optionPrefix . '-dashboard-page', 'options');

				echo '<div id="'.$this->optionPrefix . '-api-test'.'" class="missing">';
				echo '<h3 class="title">Dashboard Pages</h3>';

				if (empty($dashboardPage)) {
					echo '<p class="page-not-set">';
					echo __('Please chose a page for the dashboard.', 'tvp-trello-dashboard');
					echo '</p>';
				} else {
					echo '<ul>';
					echo '<li>';
					echo '<a href="' . get_permalink($dashboardPage) . '" target="_blank">';
					echo __('Dashbaord Page', 'tvp-trello-dashboard');
					echo '</a>';
					echo '</li>';
					echo '<li>';
					echo '<a href="' . get_permalink($dashboardPage) . TVP_TD()->Public->SignUp->slugSignUp . '" target="_blank">';
					echo __('Dashbaord Signup Page', 'tvp-trello-dashboard');
					echo '</a>';
					echo '</li>';
					echo '</ul>';
				}

				echo '<h3 class="title">Summary</h3>';
				echo '</div>';
			}, 'acf_options_page', 'side');
		}
	}

	public function dashboardPageState($post_states, $post)
	{
		$dashboardPage = get_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-dashboard-page', 'options');

		if (!$dashboardPage) {
			return $post_states;
		}

		if ($dashboardPage->ID === $post->ID) {
			$post_states[] = __('TVP Trello Dashboard', 'tvp-trello-dashboard');
		}

		return $post_states;
	}
}
