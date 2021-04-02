<?php

namespace TVP\TrelloDashboard\API;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Member
{
	/**
	 * Class Properties
	 */
	public $prefix = '';

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		// var_dump('API Member');
	}

	public function getMember($metaQuery = [])
	{
		$args = [
			'role__in' => [ TVP_TD()->Member->Role->role ],
		];

		if (!empty($metaQuery)) {
			$args['meta_query'] = $metaQuery;
			$args['meta_query']['key'] = TVP_TD()->Member->UserMeta->optionsPrefix . '-date';
		}
		$members = get_users($args);

		return $members;
	}

	public function getMemberTotal()
	{
		$memberTotal = count_users();
		return $memberTotal['avail_roles'][TVP_TD()->Member->Role->role];
	}

	public function getMemberAddedTotal($metaQuery = [])
	{
		$members = $this->getMember($metaQuery);
		return count($members);
	}
}
