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

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->optionPages = TVP_TD()->Admin->OptionPages->optionPages;
		$this->slugDashboardManager = TVP_TD()->Admin->OptionPages->slugDashboardManager;
		$this->optionPrefix = $this->slugDashboardManager;

		$this->options = [
			'key' => $this->optionPrefix . '-text-fields',
			'title' => __('Text', 'tvp-trello-dashboard'),
			'fields' => [
				[
					'key' => $this->optionPrefix . '-dashboard-page',
					'name' => $this->optionPrefix . '-dashboard-page',
					'label' => __('Dashboard Page', 'tvp-trello-dashboard'),
					'type' => 'post_object',
					'required' => 1,
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
		$dashboardPage = get_field($this->optionPrefix . '-dashboard-page', 'options');
		if (!empty($post) && !empty($dashboardPage) && $post->ID === $dashboardPage->ID) {
			var_dump('asdf');
		}
	}
}
