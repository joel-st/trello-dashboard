<?php

namespace TVP\TrelloDashboard\Options;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class TrelloIntegration
{
	/**
	 * Class Properties
	 */
	public $optionPages = [];
	public $slugTrelloIntegration = '';
	public $optionPrefix = '';

	public $connectionMetaboxId = '';

	public $options = [];

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->optionPages = TVP_TD()->Admin->OptionPages->optionPages;
		$this->slugTrelloIntegration = TVP_TD()->Admin->OptionPages->slugTrelloIntegration;
		$this->optionPrefix = $this->slugTrelloIntegration;

		$this->connectionMetaboxId = $this->optionPrefix . '-connection-metabox';

		$this->options = [
			'key' => $this->optionPrefix . '-text-fields',
			'title' => __('Connect to your Trello Organization', 'tvp-trello-dashboard'),
			'fields' => [
				[
					'key' => $this->optionPrefix . '-api-key',
					'name' => $this->optionPrefix . '-api-key',
					'label' => __('Trello API Key', 'tvp-trello-dashboard'),
					'type' => 'password',
					'instructions' => sprintf(__('Visit %1$s to get your key.', 'tvp-trello-dashboard'), '<a href="https://trello.com/app-key" target="_blank">https://trello.com/app-key</a>'),
				],
				[
					'key' => $this->optionPrefix . '-api-token',
					'name' => $this->optionPrefix . '-api-token',
					'label' => __('Trello API Server Token', 'tvp-trello-dashboard'),
					'type' => 'password',
					'instructions' => sprintf(__('Visit %1$s to create a Server Token. Click the <strong>Token</strong> hyperlink not the authorize one.', 'tvp-trello-dashboard'), '<a href="https://trello.com/app-key" target="_blank">https://trello.com/app-key</a>'),
				],
				[
					'key' => $this->optionPrefix . '-organization-id',
					'name' => $this->optionPrefix . '-organization-id',
					'label' => __('Trello Organization ID or slug', 'tvp-trello-dashboard'),
					'type' => 'text',
					'instructions' => __('If you visit your organization on trello, you can simply use the slug from the url. E.g.: https://trello.com/[organization_slug]', 'tvp-trello-dashboard'),
				],
			],
			'location' => [
				[
					[
						'param' => 'options_page',
						'operator' => '==',
						'value' => $this->slugTrelloIntegration,
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
		// var_dump(TVP_TD()->Trello->API->request('GET', '/1/members/me'));

		/**
		 * ACF is great, but there can be some security concerns. Primary among them from front-end posting,
		 * is Passwords. If you ever find yourself using the “PASSWORD” ACF field type,
		 * make sure to add some security
		 * Read more: https://wordpress.stackexchange.com/questions/244250/hash-password-field-to-database-unhash-in-admin
		 */
		add_filter('acf/update_value/name=' . $this->optionPrefix . '-api-key', [TVP_TD(), 'encryptPassword'], 9001, 1);
		add_filter('acf/update_value/name=' . $this->optionPrefix . '-api-token', [TVP_TD(), 'encryptPassword'], 9001, 1);
		add_filter('acf/load_value/name=' . $this->optionPrefix . '-api-key', [TVP_TD(), 'decryptPassword'], 9001, 1);
		add_filter('acf/load_value/name=' . $this->optionPrefix . '-api-token', [TVP_TD(), 'decryptPassword'], 9001, 1);

		// metabox
		add_action('acf/input/admin_head', [$this, 'registerMetabox'], 10);
		add_action('wp_ajax_' . $this->optionPrefix . '-integration-test', [$this, 'integrationTest']);
		add_action('wp_ajax_nopriv_' . $this->optionPrefix . '-integration-test', [$this, 'integrationTest']);
	}

	/**
	 * Add ACF Options to Sub Pages trough acf acf_add_local_field_group
	 */
	public function addOptions()
	{
		if (isset($this->optionPages[$this->slugTrelloIntegration]) && function_exists('acf_add_local_field_group')) {
			acf_add_local_field_group($this->options);
		}
	}

	/**
	 * Getter function to get the api key field value
	 */
	public function getApiKey()
	{
		return get_field($this->slugTrelloIntegration . '-api-key', 'options');
	}

	/**
	 * Getter function to get the api token field value
	 */
	public function getApiToken()
	{
		return get_field($this->slugTrelloIntegration . '-api-token', 'options');
	}

	/**
	 * Register a metabox to show status of connection with the provided authentication details
	 */
	public function registerMetabox()
	{
		if (strpos(get_current_screen()->base, $this->slugTrelloIntegration) !== false) {
			// Add meta box
			add_meta_box($this->connectionMetaboxId, __('Trello Integration Test', 'tvp-trello-dashboard'), function () {
				if (empty($this->getApiKey()) || empty($this->getApiToken())) {
					echo '<div id="'.$this->optionPrefix . '-api-test'.'" class="missing">';
					echo '<h3 class="title">Not connected!</h3>';
					echo '<p class="info">';
					if (empty($this->getApiKey())) {
						echo '<strong>' . __('Missing API Key', 'tvp-trello-dashboard') . '</strong>';
					}
					if (empty($this->getApiToken())) {
						echo '<strong>' . __('Missing API Token', 'tvp-trello-dashboard') . '</strong>';
					}
					echo '</p>';
				} else {
					echo '<div id="'.$this->optionPrefix . '-api-test'.'" class="testing">';
					echo '<figure>';
					echo '<span class="spinner is-active"></span>';
					echo '</figure>';
					echo '<span class="label">Testing Connection …</span>';
				}
				echo '</div>';
			}, 'acf_options_page', 'side');
		}
	}

	/**
	 * The ajax integration test function called by integrationTest(); in javascript `assets/scripts/admin.js`
	 */
	public function integrationTest()
	{
		$url = 'https://api.trello.com/1/members/me?key=' . $this->getApiKey() . '&token=' . $this->getApiToken();

		$data = file_get_contents($url);
		if (empty($data)) {
			return false;
		}

		if (!$parsedData = json_decode($data, true)) {
			return false;
		}

		$response = [
			'username' => $parsedData['username'],
			'avatarUrl' => $parsedData['avatarUrl'],
		];

		echo json_encode($parsedData);

		//Don't forget to always exit in the ajax function.
		wp_die();
	}
}
