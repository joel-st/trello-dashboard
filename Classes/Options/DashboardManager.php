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

	public $dashboardOptions = [];
	public $signupOptions = [];
	public $notInOrganizationOptions = [];

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

		$this->dashboardOptions = [
			'key' => $this->optionPrefix . '-dashboard-options',
			'title' => __('Dashboard Options', 'tvp-trello-dashboard'),
			'fields' => [
				[
					'key' => $this->optionPrefix . '-dashboard-slug',
					'name' => $this->optionPrefix . '-dashboard-slug',
					'label' => __('Dashboard Page Slug', 'tvp-trello-dashboard'),
					'type' => 'text',
					'allow_null' => 1,
					'default_value' => 'trello-dashboard',
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

		$this->signupOptions = [
			'key' => $this->optionPrefix . '-signup-options',
			'title' => __('Signup Options', 'tvp-trello-dashboard'),
			'fields' => [
				[
					'key' => $this->optionPrefix . '-signup-pre-content',
					'name' => $this->optionPrefix . '-signup-pre-content',
					'label' => __('Signup Page', 'tvp-trello-dashboard'),
					'type' => 'wysiwyg',
				],
				[
					'key' => $this->optionPrefix . '-signup-background',
					'name' => $this->optionPrefix . '-signup-background',
					'label' => __('Signup Background', 'tvp-trello-dashboard'),
					'type' => 'image',
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
			'menu_order' => 5,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
		];

		$this->notInOrganizationOptions = [
			'key' => $this->optionPrefix . '-not-in-organization-options',
			'title' => __('Not In Organization Options', 'tvp-trello-dashboard'),
			'fields' => [
				[
					'key' => $this->optionPrefix . '-not-in-organization-pre-content',
					'name' => $this->optionPrefix . '-not-in-organization-pre-content',
					'label' => __('Not In Organization Page', 'tvp-trello-dashboard'),
					'type' => 'wysiwyg',
				],
				[
					'key' => $this->optionPrefix . '-not-in-organization-background',
					'name' => $this->optionPrefix . '-not-in-organization-background',
					'label' => __('Not In Organization Background', 'tvp-trello-dashboard'),
					'type' => 'image',
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
			'menu_order' => 10,
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

		// metabox
		add_action('acf/input/admin_head', [$this, 'registerDashboardMetabox'], 10);
	}

	/**
	 * Add ACF Options to Sub Pages trough acf acf_add_local_field_group
	 */
	public function addOptions()
	{
		if (isset($this->optionPages[$this->slugDashboardManager]) && function_exists('acf_add_local_field_group')) {
			acf_add_local_field_group($this->dashboardOptions);
			acf_add_local_field_group($this->signupOptions);
			acf_add_local_field_group($this->notInOrganizationOptions);
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
				$dashboardSlug = get_field($this->optionPrefix . '-dashboard-slug', 'options');

				echo '<div id="'.$this->optionPrefix . '-api-test'.'" class="missing">';
				echo '<h3 class="title">Dashboard Pages</h3>';

				if (empty($dashboardSlug)) {
					echo '<p class="page-not-set">';
					echo __('Please chose a page for the dashboard.', 'tvp-trello-dashboard');
					echo '</p>';
				} else {
					echo '<ul>';
					echo '<li>';
					echo '<a href="' . TVP_TD()->View->Dashboard->getPermalink() . '" target="_blank">';
					echo __('Dashbaord Page', 'tvp-trello-dashboard');
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
