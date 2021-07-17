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
					'key' => $this->optionsPrefix . '-id',
					'name' => $this->optionsPrefix . '-id',
					'label' => __('Trello User ID', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1
				],
				[
					'key' => $this->optionsPrefix . '-type',
					'name' => $this->optionsPrefix . '-type',
					'label' => __('Trello Member Type', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1
				],
				[
					'key' => $this->optionsPrefix . '-unconfirmed',
					'name' => $this->optionsPrefix . '-unconfirmed',
					'label' => __('Trello Member Unconfirmed', 'tvp-trello-dashboard'),
					'type' => 'true_false',
					'required' => 0,
					'readonly' => 1
				],
				[
					'key' => $this->optionsPrefix . '-deactivated',
					'name' => $this->optionsPrefix . '-deactivated',
					'label' => __('Trello Member Deactivated', 'tvp-trello-dashboard'),
					'type' => 'true_false',
					'required' => 0,
					'readonly' => 1
				],
				[
					'key' => $this->optionsPrefix . '-avatar-url',
					'name' => $this->optionsPrefix . '-avatar-url',
					'label' => __('Trello Member Avatar', 'tvp-trello-dashboard'),
					'type' => 'url',
					'required' => 0,
					'readonly' => 1
				],
				[
					'key' => $this->optionsPrefix . '-date',
					'name' => $this->optionsPrefix . '-date',
					'label' => __('Trello Member Date', 'tvp-trello-dashboard'),
					'type' => 'date_picker',
					'required' => 0,
					'readonly' => 1,
					'display_format' => 'Y-m-d',
					'return_format' => 'Y-m-d',
				],
			],
			'location' => [
				[
					[
						'param' => 'user_role',
						'operator' => '==',
						'value' => TVP_TD()->Member->Role->role,
					],
					[
						'param' => 'current_user_role',
						'operator' => '==',
						'value' => 'administrator',
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
