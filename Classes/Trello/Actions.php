<?php

namespace TVP\TrelloDashboard\Trello;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Actions
{
	/**
	 * Class Properties
	 */
	public $postType = '';
	public $boardTaxonomy = '';
	public $cardTaxonomy = '';

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->postType = TVP_TD()->prefix . '-trello-action';
		$this->boardTaxonomy = TVP_TD()->prefix . '-trello-board';
		$this->cardTaxonomy = TVP_TD()->prefix . '-trello-card';
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		add_action('init', [$this, 'registerTaxonomy']);
		add_action('init', [$this, 'registerPostType']);
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
			'hierarchical'      => false,
			'public'            => false,
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
			'hierarchical'      => false,
			'public'            => false,
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
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'supports'           => [ 'title' ],
			'taxonomies'         => [ $this->boardTaxonomy, $this->cardTaxonomy ],
			'show_in_rest'       => false
		]);
	}
}
