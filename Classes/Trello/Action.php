<?php

namespace TVP\TrelloDashboard\Trello;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Action
{
	/**
	 * Class Properties
	 */
	public $postType = '';
	public $boardTaxonomy = '';
	public $cardTaxonomy = '';
	public $listTaxonomy = '';

	public $optionPrefixAction = '';
	public $optionPrefixBoard = '';
	public $optionPrefixCard = '';
	public $optionPrefixList = '';

	public $optionsBoard = [];
	public $optionsCard = [];
	public $optionsList = [];
	public $optionsAction = [];

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->postType = TVP_TD()->prefix . '-trello-action';
		$this->boardTaxonomy = TVP_TD()->prefix . '-trello-board';
		$this->cardTaxonomy = TVP_TD()->prefix . '-trello-card';
		$this->listTaxonomy = TVP_TD()->prefix . '-trello-list';
		$this->optionPrefixAction = $this->postType;
		$this->optionPrefixBoard = $this->boardTaxonomy;
		$this->optionPrefixCard = $this->cardTaxonomy;
		$this->optionPrefixList = $this->listTaxonomy;

		$this->optionsAction = [
			'key' => $this->optionPrefixAction,
			'title' => __('TVP Trello Action Metainformation', 'tvp-trello-dashboard'),
			'fields' => [
				[
					'key' => $this->optionPrefixAction . '-id',
					'name' => $this->optionPrefixAction . '-id',
					'label' => __('Action ID', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixAction . '-type',
					'name' => $this->optionPrefixAction . '-type',
					'label' => __('Action Type', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixAction . '-id-creator',
					'name' => $this->optionPrefixAction . '-id-creator',
					'label' => __('Creator ID', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixAction . '-date',
					'name' => $this->optionPrefixAction . '-date',
					'label' => __('Date', 'tvp-trello-dashboard'),
					'type' => 'date_picker',
					'required' => 0,
					'readonly' => 1,
					'display_format' => 'Y-m-d',
					'return_format' => 'Y-m-d',
				],
				[
					'key' => $this->optionPrefixAction . '-id-board',
					'name' => $this->optionPrefixAction . '-id-board',
					'label' => __('Board ID', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixAction . '-id-list',
					'name' => $this->optionPrefixAction . '-id-list',
					'label' => __('List ID', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixAction . '-id-card',
					'name' => $this->optionPrefixAction . '-id-card',
					'label' => __('Card ID', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1,
				],
			],
			'location' => [
				[
					[
						'param' => 'post_type',
						'operator' => '==',
						'value' => $this->postType,
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

		$this->optionsBoard = [
			'key' => $this->optionPrefixBoard,
			'title' => __('TVP Trello Board Metainformation', 'tvp-trello-dashboard'),
			'fields' => [
				[
					'key' => $this->optionPrefixBoard . '-name',
					'name' => $this->optionPrefixBoard . '-name',
					'label' => __('Board Name', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixBoard . '-id',
					'name' => $this->optionPrefixBoard . '-id',
					'label' => __('Board ID', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixBoard . '-url',
					'name' => $this->optionPrefixBoard . '-url',
					'label' => __('Board URL', 'tvp-trello-dashboard'),
					'type' => 'url',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixBoard . '-members',
					'name' => $this->optionPrefixBoard . '-members',
					'label' => __('Members', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixBoard . '-memberships',
					'name' => $this->optionPrefixBoard . '-memberships',
					'label' => __('Memberships', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixBoard . '-date',
					'name' => $this->optionPrefixBoard . '-date',
					'label' => __('Date', 'tvp-trello-dashboard'),
					'type' => 'date_picker',
					'required' => 0,
					'readonly' => 1,
					'display_format' => 'Y-m-d',
					'return_format' => 'Y-m-d',
				],
				[
					'key' => $this->optionPrefixBoard . '-closed',
					'name' => $this->optionPrefixBoard . '-closed',
					'label' => __('Closed', 'tvp-trello-dashboard'),
					'type' => 'true_false',
					'required' => 0,
					'readonly' => 1
				],
				[
					'key' => $this->optionPrefixBoard . '-date-closed',
					'name' => $this->optionPrefixBoard . '-date-closed',
					'label' => __('Date Closed', 'tvp-trello-dashboard'),
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
						'param' => 'taxonomy',
						'operator' => '==',
						'value' => $this->boardTaxonomy,
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

		$this->optionsCard = [
			'key' => $this->optionPrefixCard,
			'title' => __('TVP Trello Card Metainformation', 'tvp-trello-dashboard'),
			'fields' => [
				[
					'key' => $this->optionPrefixCard . '-name',
					'name' => $this->optionPrefixCard . '-name',
					'label' => __('Card Name', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixCard . '-id',
					'name' => $this->optionPrefixCard . '-id',
					'label' => __('Card ID', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixCard . '-url',
					'name' => $this->optionPrefixCard . '-url',
					'label' => __('Card URL', 'tvp-trello-dashboard'),
					'type' => 'url',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixCard . '-members',
					'name' => $this->optionPrefixCard . '-members',
					'label' => __('Card Members', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixCard . '-date',
					'name' => $this->optionPrefixCard . '-date',
					'label' => __('Card Date', 'tvp-trello-dashboard'),
					'type' => 'date_picker',
					'required' => 0,
					'readonly' => 1,
					'display_format' => 'Y-m-d',
					'return_format' => 'Y-m-d',
				],
				[
					'key' => $this->optionPrefixCard . '-closed',
					'name' => $this->optionPrefixCard . '-closed',
					'label' => __('Card Closed', 'tvp-trello-dashboard'),
					'type' => 'true_false',
					'required' => 0,
					'readonly' => 1
				],
				[
					'key' => $this->optionPrefixCard . '-id-board',
					'name' => $this->optionPrefixCard . '-id-board',
					'label' => __('Board ID', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1
				],
				[
					'key' => $this->optionPrefixCard . '-id-list',
					'name' => $this->optionPrefixCard . '-id-list',
					'label' => __('List ID', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1
				]
			],
			'location' => [
				[
					[
						'param' => 'taxonomy',
						'operator' => '==',
						'value' => $this->cardTaxonomy,
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

		$this->optionsList = [
			'key' => $this->optionPrefixList,
			'title' => __('TVP Trello List Metainformation', 'tvp-trello-dashboard'),
			'fields' => [
				[
					'key' => $this->optionPrefixList . '-name',
					'name' => $this->optionPrefixList . '-name',
					'label' => __('List Name', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixList . '-id',
					'name' => $this->optionPrefixList . '-id',
					'label' => __('List ID', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1,
				],
				[
					'key' => $this->optionPrefixList . '-date',
					'name' => $this->optionPrefixList . '-date',
					'label' => __('List Date', 'tvp-trello-dashboard'),
					'type' => 'date_picker',
					'required' => 0,
					'readonly' => 1,
					'display_format' => 'Y-m-d',
					'return_format' => 'Y-m-d',
				],
				[
					'key' => $this->optionPrefixList . '-closed',
					'name' => $this->optionPrefixList . '-closed',
					'label' => __('List Closed', 'tvp-trello-dashboard'),
					'type' => 'true_false',
					'required' => 0,
					'readonly' => 1
				],
				[
					'key' => $this->optionPrefixList . '-id-board',
					'name' => $this->optionPrefixList . '-id-board',
					'label' => __('Board ID', 'tvp-trello-dashboard'),
					'type' => 'text',
					'required' => 0,
					'readonly' => 1
				],
			],
			'location' => [
				[
					[
						'param' => 'taxonomy',
						'operator' => '==',
						'value' => $this->listTaxonomy,
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
		add_action('init', [$this, 'registerTaxonomy']);
		add_action('init', [$this, 'registerPostType']);
		add_action('acf/init', [$this, 'options']);

		add_filter('manage_' . $this->postType . '_posts_columns', [$this, 'addActionsColumns'], 10, 1);
		add_action('manage_' . $this->postType . '_posts_custom_column', [$this, 'outputActionsColumns'], 10, 2);

		add_filter('manage_edit-' . $this->boardTaxonomy . '_columns', [$this, 'addBoardsColumns'], 10, 1);
		add_action('manage_' . $this->boardTaxonomy . '_custom_column', [$this, 'outputBoardsColumns'], 10, 3);

		add_filter('manage_edit-' . $this->cardTaxonomy . '_columns', [$this, 'addCardsColumns'], 10, 1);
		add_action('manage_' . $this->cardTaxonomy . '_custom_column', [$this, 'outputCardsColumns'], 10, 3);

		add_filter('manage_edit-' . $this->listTaxonomy . '_columns', [$this, 'addListsColumns'], 10, 1);
		add_action('manage_' . $this->listTaxonomy . '_custom_column', [$this, 'outputListsColumns'], 10, 3);
	}

	public function registerTaxonomy()
	{
		register_taxonomy($this->boardTaxonomy, $this->postType, [
			'labels'            => [
				'name'              => _x('Trello Boards', 'taxonomy general name', 'tvp-trello-dashboard'),
				'singular_name'     => _x('Trello Board', 'taxonomy singular name', 'tvp-trello-dashboard'),
				'search_items'      => __('Search Trello Boards', 'tvp-trello-dashboard'),
				'all_items'         => __('All Trello Boards', 'tvp-trello-dashboard'),
				'view_item'         => __('View Trello Board', 'tvp-trello-dashboard'),
				'parent_item'       => __('Parent Trello Board', 'tvp-trello-dashboard'),
				'parent_item_colon' => __('Parent Trello Board:', 'tvp-trello-dashboard'),
				'edit_item'         => __('Edit Trello Board', 'tvp-trello-dashboard'),
				'update_item'       => __('Update Trello Board', 'tvp-trello-dashboard'),
				'add_new_item'      => __('Add New Trello Board', 'tvp-trello-dashboard'),
				'new_item_name'     => __('New Trello Board Name', 'tvp-trello-dashboard'),
				'not_found'         => __('No Trello Boards Found', 'tvp-trello-dashboard'),
				'back_to_items'     => __('Back to Trello Boards', 'tvp-trello-dashboard'),
				'menu_name'         => __('Trello Boards', 'tvp-trello-dashboard'),
			],
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => false,
			'show_in_rest'      => true,
		]);

		register_taxonomy($this->cardTaxonomy, $this->postType, [
			'labels'            => [
				'name'              => _x('Trello Cards', 'taxonomy general name', 'tvp-trello-dashboard'),
				'singular_name'     => _x('Trello Card', 'taxonomy singular name', 'tvp-trello-dashboard'),
				'search_items'      => __('Search Trello Cards', 'tvp-trello-dashboard'),
				'all_items'         => __('All Trello Cards', 'tvp-trello-dashboard'),
				'view_item'         => __('View Trello Card', 'tvp-trello-dashboard'),
				'parent_item'       => __('Parent Trello Card', 'tvp-trello-dashboard'),
				'parent_item_colon' => __('Parent Trello Card:', 'tvp-trello-dashboard'),
				'edit_item'         => __('Edit Trello Card', 'tvp-trello-dashboard'),
				'update_item'       => __('Update Trello Card', 'tvp-trello-dashboard'),
				'add_new_item'      => __('Add New Trello Card', 'tvp-trello-dashboard'),
				'new_item_name'     => __('New Trello Card Name', 'tvp-trello-dashboard'),
				'not_found'         => __('No Trello Cards Found', 'tvp-trello-dashboard'),
				'back_to_items'     => __('Back to Trello Cards', 'tvp-trello-dashboard'),
				'menu_name'         => __('Trello Cards', 'tvp-trello-dashboard'),
			],
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => false,
			'show_in_rest'      => true,
		]);

		register_taxonomy($this->listTaxonomy, $this->postType, [
			'labels'            => [
				'name'              => _x('Trello Lists', 'taxonomy general name', 'tvp-trello-dashboard'),
				'singular_name'     => _x('Trello List', 'taxonomy singular name', 'tvp-trello-dashboard'),
				'search_items'      => __('Search Trello Lists', 'tvp-trello-dashboard'),
				'all_items'         => __('All Trello Lists', 'tvp-trello-dashboard'),
				'view_item'         => __('View Trello List', 'tvp-trello-dashboard'),
				'parent_item'       => __('Parent Trello List', 'tvp-trello-dashboard'),
				'parent_item_colon' => __('Parent Trello List:', 'tvp-trello-dashboard'),
				'edit_item'         => __('Edit Trello List', 'tvp-trello-dashboard'),
				'update_item'       => __('Update Trello List', 'tvp-trello-dashboard'),
				'add_new_item'      => __('Add New Trello List', 'tvp-trello-dashboard'),
				'new_item_name'     => __('New Trello List Name', 'tvp-trello-dashboard'),
				'not_found'         => __('No Trello Lists Found', 'tvp-trello-dashboard'),
				'back_to_items'     => __('Back to Trello Lists', 'tvp-trello-dashboard'),
				'menu_name'         => __('Trello Lists', 'tvp-trello-dashboard'),
			],
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => false,
			'show_in_rest'      => true,
		]);
	}

	public function registerPostType()
	{
		register_post_type($this->postType, [
			'labels'             => [
				'name'                  => _x('Trello Actions', 'Post type general name', 'tvp-trello-dashboard'),
				'singular_name'         => _x('Trello Action', 'Post type singular name', 'tvp-trello-dashboard'),
				'menu_name'             => _x('Trello Actions', 'Admin Menu text', 'tvp-trello-dashboard'),
				'name_admin_bar'        => _x('Trello Action', 'Add New on Toolbar', 'tvp-trello-dashboard'),
				'add_new'               => __('Add Trello Action', 'tvp-trello-dashboard'),
				'add_new_item'          => __('Add New Trello Action', 'tvp-trello-dashboard'),
				'new_item'              => __('New Trello Action', 'tvp-trello-dashboard'),
				'edit_item'             => __('Edit Trello Action', 'tvp-trello-dashboard'),
				'view_item'             => __('View Trello Action', 'tvp-trello-dashboard'),
				'all_items'             => __('All Trello Actions', 'tvp-trello-dashboard'),
				'search_items'          => __('Search Trello Actions', 'tvp-trello-dashboard'),
				'parent_item_colon'     => __('Parent Trello Action:', 'tvp-trello-dashboard'),
				'not_found'             => __('No Trello Actions found.', 'tvp-trello-dashboard'),
				'not_found_in_trash'    => __('No Trello Actions found in Trash.', 'tvp-trello-dashboard'),
				'featured_image'        => _x('Trello Action Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'tvp-trello-dashboard'),
				'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'tvp-trello-dashboard'),
				'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'tvp-trello-dashboard'),
				'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'tvp-trello-dashboard'),
				'archives'              => _x('Trello Action archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'tvp-trello-dashboard'),
				'insert_into_item'      => _x('Insert into Trello Action', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'tvp-trello-dashboard'),
				'uploaded_to_this_item' => _x('Uploaded to this Trello Action', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'tvp-trello-dashboard'),
				'filter_items_list'     => _x('Filter Trello Actions list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'tvp-trello-dashboard'),
				'items_list_navigation' => _x('Trello Actions list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'tvp-trello-dashboard'),
				'items_list'            => _x('Trello Actions list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'tvp-trello-dashboard'),
			],
			'description'        => __('Trello Action custom post type.'),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => TVP_TD()->menuPositionBase,
			'supports'           => [ 'title' ],
			'taxonomies'         => [ $this->boardTaxonomy, $this->cardTaxonomy, $this->listTaxonomy ],
			'show_in_rest'       => false
		]);
	}

	public function options()
	{
		if (function_exists('acf_add_local_field_group')) {
			acf_add_local_field_group($this->optionsAction);
			acf_add_local_field_group($this->optionsBoard);
			acf_add_local_field_group($this->optionsCard);
			acf_add_local_field_group($this->optionsList);
		}
	}

	public function addActionsColumns($columns)
	{
		$customColumns = [];

		foreach ($columns as $key => $title) {
			if ($key === 'cb') {
				$customColumns[$key] = $title;
			}
			if ($key === 'title') {
				$customColumns[$key] = __('ID', 'tvp-trello-dashboard');
			}
			if ($key == 'taxonomy-tvptd-trello-board') {
				$customColumns['custom-taxonomy-tvptd-trello-board'] = __('Board', 'tvp-trello-dashboard'); // Our New Colomn Name
			}
			if ($key == 'taxonomy-tvptd-trello-card') {
				$customColumns['custom-taxonomy-tvptd-trello-card'] = __('Card', 'tvp-trello-dashboard'); // Our New Colomn Name
			}
			if ($key == 'taxonomy-tvptd-trello-list') {
				$customColumns['custom-taxonomy-tvptd-trello-list'] = __('List', 'tvp-trello-dashboard'); // Our New Colomn Name
			}
			if ($key === 'date') {
				$customColumns['creator'] = __('Creator', 'tvp-trello-dashboard');
				$customColumns[$key] = $title;
			}
		}

		unset($customColumns['taxonomy-tvptd-trello-board']);
		unset($customColumns['taxonomy-tvptd-trello-list']);
		unset($customColumns['taxonomy-tvptd-trello-card']);
		return $customColumns;
	}

	public function outputActionsColumns($column, $postId)
	{
		if ($column == 'custom-taxonomy-tvptd-trello-board') {
			$terms = wp_get_post_terms($postId, $this->boardTaxonomy);
			$tags = [];
			foreach ($terms as $key => $term) {
				$tags[] = '<a href="'.get_edit_term_link($term->term_id, $this->boardTaxonomy).'">'.get_field($this->optionPrefixBoard . '-name', $this->boardTaxonomy . '_' . $term->term_id).'</a>';
			}
			echo implode(', ', $tags);
		}
		if ($column == 'custom-taxonomy-tvptd-trello-card') {
			$terms = wp_get_post_terms($postId, $this->cardTaxonomy);
			$tags = [];
			foreach ($terms as $key => $term) {
				$tags[] = '<a href="'.get_edit_term_link($term->term_id, $this->cardTaxonomy).'">'.get_field($this->optionPrefixCard . '-name', $this->cardTaxonomy . '_' . $term->term_id).'</a>';
			}
			echo implode(', ', $tags);
		}
		if ($column == 'custom-taxonomy-tvptd-trello-list') {
			$terms = wp_get_post_terms($postId, $this->listTaxonomy);
			$tags = [];
			foreach ($terms as $key => $term) {
				$tags[] = '<a href="'.get_edit_term_link($term->term_id, $this->listTaxonomy).'">'.get_field($this->optionPrefixList . '-name', $this->listTaxonomy . '_' . $term->term_id).'</a>';
			}
			echo implode(', ', $tags);
		}
		if ($column == 'creator') {
			$userExists = false;

			$args = [
				'meta_key'   => TVP_TD()->Member->UserMeta->optionsPrefix . '-id',
				'meta_value' => get_field(TVP_TD()->Trello->Action->optionPrefixAction . '-id-creator', $postId),
			];

			$userExists = get_users($args);
			if (!empty($userExists)) {
				echo $userExists[0]->data->user_login;
			}
		}
	}

	public function addBoardsColumns($columns)
	{
		$customColumns = [];

		foreach ($columns as $key => $title) {
			if ($key === 'name') {
				$customColumns[$key] = __('ID', 'tvp-trello-dashboard');
				$customColumns['title'] = __('Name', 'tvp-trello-dashboard');
			}
			if ($key === 'description') {
				$customColumns[$key] = $title;
				$customColumns['members'] = __('Members', 'tvp-trello-dashboard');
			}
			if ($key === 'cb' || $key === 'posts') {
				$customColumns[$key] = $title;
			}
		}

		return $customColumns;
	}

	public function outputBoardsColumns($output, $column, $termId)
	{
		if ($column == 'title') {
			return '<a href="'.get_field($this->optionPrefixBoard . '-url', $this->boardTaxonomy . '_' . $termId).'" target="_blank">'.get_field($this->optionPrefixBoard . '-name', $this->boardTaxonomy . '_' . $termId).'</a>';
		}
		if ($column == 'members') {
			return count(explode(',', get_field(TVP_TD()->Trello->Action->optionPrefixBoard . '-members', $this->boardTaxonomy . '_' . $termId)));
		}
	}

	public function addCardsColumns($columns)
	{
		$customColumns = [];

		foreach ($columns as $key => $title) {
			if ($key === 'name') {
				$customColumns[$key] = __('ID', 'tvp-trello-dashboard');
				$customColumns['title'] = __('Name', 'tvp-trello-dashboard');
			}
			if ($key === 'slug') {
				$customColumns['board'] = __('Board', 'tvp-trello-dashboard');
				$customColumns['list'] = __('List', 'tvp-trello-dashboard');
			}
			if ($key === 'cb' || $key === 'posts' || $key === 'description') {
				$customColumns[$key] = $title;
			}
		}

		return $customColumns;
	}

	public function outputCardsColumns($output, $column, $termId)
	{
		if ($column == 'board') {
			$board = TVP_TD()->API->Action->getBoard(get_field($this->optionPrefixCard . '-id-board', $this->cardTaxonomy . '_' . $termId));
			if (!empty($board)) {
				return get_field($this->optionPrefixBoard . '-name', $this->boardTaxonomy . '_' . $board->term_id);
			}
		}
		if ($column == 'list') {
			$list = TVP_TD()->API->Action->getList(get_field($this->optionPrefixCard . '-id-list', $this->cardTaxonomy . '_' . $termId));
			if (!empty($list)) {
				return get_field($this->optionPrefixList . '-name', $this->listTaxonomy . '_' . $list->term_id);
			}
		}
		if ($column == 'title') {
			return get_field($this->optionPrefixCard . '-name', $this->cardTaxonomy . '_' . $termId);
		}
	}

	public function addListsColumns($columns)
	{
		$customColumns = [];

		foreach ($columns as $key => $title) {
			if ($key === 'name') {
				$customColumns[$key] = __('ID', 'tvp-trello-dashboard');
				$customColumns['title'] = __('Name', 'tvp-trello-dashboard');
				$customColumns['board'] = __('Board', 'tvp-trello-dashboard');
			}
			if ($key === 'cb' || $key === 'posts' || $key === 'description') {
				$customColumns[$key] = $title;
			}
		}

		return $customColumns;
	}

	public function outputListsColumns($output, $column, $termId)
	{
		if ($column == 'board') {
			$board = TVP_TD()->API->Action->getBoard(get_field($this->optionPrefixList . '-id-board', $this->listTaxonomy . '_' . $termId));
			if (!empty($board)) {
				return get_field($this->optionPrefixBoard . '-name', $this->boardTaxonomy . '_' . $board->term_id);
			}
		}
		if ($column == 'title') {
			return get_field($this->optionPrefixList . '-name', $this->listTaxonomy . '_' . $termId);
		}
	}
}
