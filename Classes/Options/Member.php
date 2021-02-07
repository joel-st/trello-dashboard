<?php

namespace TVP\TrelloDashboard\Options;

class Member
{
	/**
	 * Class Properties
	 */
	public $optionPages = [];
	public $slugMember = '';
	public $optionPrefix = '';

	public $options = [];

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->optionPages = TVP_TD()->Admin->OptionPages->optionPages;
		$this->slugMember = TVP_TD()->Admin->OptionPages->slugMember;
		$this->prefix = $this->slugMember;

		$this->options = [
			'key' => $this->prefix . '-text-fields',
			'title' => __('Text', 'sht'),
			'fields' => [
				[
					'key' => $this->prefix . '-text',
					'name' => $this->prefix . '-text',
					'label' => __('Text', 'sht'),
					'type' => 'text',
					'required' => 1,
				],
			],
			'location' => [
				[
					[
						'param' => 'options_page',
						'operator' => '==',
						'value' => $this->slugMember,
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
	}

	/**
	 * Add ACF Options to Sub Pages trough acf acf_add_local_field_group
	 */
	public function addOptions()
	{
		if (isset($this->optionPages[$this->slugMember]) && function_exists('acf_add_local_field_group')) {
			acf_add_local_field_group($this->options);
		}
	}
}
