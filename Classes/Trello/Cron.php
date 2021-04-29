<?php

namespace TVP\TrelloDashboard\Trello;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Cron
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
	 * TODO: setup cron scripts to fetch data on intervals
	 * Cron jobs for all those functions:
	 * TVP_TD()->Trello->DataProcessor->addUpdateMembers()
	 * TVP_TD()->Trello->DataProcessor->addUpdateBoards()
	 * TVP_TD()->Trello->DataProcessor->addUpdateLists()
	 * TVP_TD()->Trello->DataProcessor->addUpdateCards()
	 * TVP_TD()->Trello->DataProcessor->addUpdateActions()
	 */
	public function run()
	{
		// var_dump('Trello Cron');
	}
}
