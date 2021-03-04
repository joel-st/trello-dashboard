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
}
