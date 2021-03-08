<?php

/**
 * The TVPUserList shown under Trello Dashboard > Members based on WP_List_Table
 */

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

//Our class extends the WP_List_Table class, so we need to make sure that it's there
if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class TVPUserList extends \WP_List_Table
{
	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items()
	{
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$data = $this->table_data();
		usort($data, [ &$this, 'sort_data' ]);

		$perPage = 20;
		$currentPage = $this->get_pagenum();
		$totalItems = count($data);

		$this->set_pagination_args([
			'total_items' => $totalItems,
			'per_page'    => $perPage
		]);

		$data = array_slice($data, (($currentPage-1)*$perPage), $perPage);

		$this->_column_headers = [$columns, $hidden, $sortable];
		$this->items = $data;
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns()
	{
		$columns = [
			'user_login'     => 'Username',
			'id'             => 'ID',
			'fname'          => 'First Name',
			'lname'          => 'Last Name',
			'email'          => 'E-Mail',
			'trello_user_id' => 'Trello User ID',
		];

		return $columns;
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns()
	{
		return [];
	}

	/**
	 * Define the sortable columns
	 *
	 * @return Array
	 */
	public function get_sortable_columns()
	{
		return ['user_login' => ['user_login', false]];
	}

	/**
	 * Get the table data
	 *
	 * @return Array
	 */
	private function table_data()
	{
		$user_query = new \WP_User_Query([ 'role' => TVP_TD()->Member->Role->role ]);

		$data = [];

		if (! empty($user_query->get_results())) {
			foreach ($user_query->get_results() as $user) {
				$fname = get_user_meta($user->ID, 'first_name', true);
				$lname = get_user_meta($user->ID, 'last_name', true);
				$trelloUserId = get_field(TVP_TD()->Member->UserMeta->optionsPrefix . '-id', 'user_' . $user->ID);

				$data[] = [
					'user_login' => '<a href="'.get_edit_user_link($user->ID).'" target="_blank" title="'.__('Edit User Profile', 'tvp-trello-dashboard').'">'.$user->user_login.'</a>',
					'id' => $user->ID,
					'fname' =>  $fname ? $fname : '–',
					'lname' =>  $lname ? $lname : '–',
					'email' => $user->user_email,
					'trello_user_id' => $trelloUserId,
				];
			}
		}

		return $data;
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  Array $item        Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default($item, $column_name)
	{
		switch ($column_name) {
			case 'id':
			case 'user_login':
			case 'fname':
			case 'lname':
			case 'email':
			case 'trello_user_id':
				return $item[ $column_name ];
			default:
				return print_r($item, true) ;
		}
	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @return Mixed
	 */
	private function sort_data($a, $b)
	{
		// Set defaults
		$orderby = 'user_login';
		$order = 'asc';

		// If orderby is set, use this as the sort column
		if (!empty($_GET['orderby'])) {
			$orderby = $_GET['orderby'];
		}

		// If order is set use this as the order
		if (!empty($_GET['order'])) {
			$order = $_GET['order'];
		}


		$result = strcmp($a[$orderby], $b[$orderby]);

		if ($order === 'asc') {
			return $result;
		}

		return -$result;
	}
}
