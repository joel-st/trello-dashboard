<?php

namespace TVP\TrelloDashboard\Member;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class UserMeta
{
	/**
	 * Class Properties
	 */
	public $optionPrefix = '';
	public $options = [];

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->optionsPrefix = TVP_TD()->prefix . '-trello-user';
		$this->options = [
			'key' => $this->optionsPrefix,
			'title' => __('TVP Trello Member Metainformation', 'tvp-trello-dashboard'),
			'fields' => [
				[
					// TODO: add all necessary user meta fields to fill with information from trello
					'key' => $this->optionsPrefix . '-id',
					'name' => $this->optionsPrefix . '-id',
					'label' => __('Trello User ID', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1
				],
			],
			'location' => [
				[
					[
						'param' => 'user_role',
						'operator' => '==',
						'value' => TVP_TD()->Member->Role->role,
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
		add_action('acf/init', [$this, 'addMetaFields']);
	}

	/**
	 * Add user meta fields
	 */
	public function addMetaFields()
	{
		if (function_exists('acf_add_local_field_group')) {
			acf_add_local_field_group($this->options);
		}
	}
}
