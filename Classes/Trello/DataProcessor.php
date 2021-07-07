<?php

namespace TVP\TrelloDashboard\Trello;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class DataProcessor
{
	/**
	 * Class Properties
	 */
	public $prefix = '';
	public $optionPrefix = '';
	public $options = '';
	public $optionLastFetch = '';

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->prefix = TVP_TD()->prefix . '-data-processor';
		$this->optionPrefix = $this->prefix;
		$this->optionLastFetch = TVP_TD()->prefix . '-last-fetch';
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		add_action('wp_ajax_' . $this->prefix, [$this, 'ajaxDataProcessor']);
		add_action('wp_ajax_nopriv_' . $this->prefix, [$this, 'ajaxDataProcessor']);

		// add_action('init', [$this, 'addUpdateCards']);
		add_action('admin_action_tvptd', [ $this, 'doAction' ]);

		// cron
		add_action('init', [$this, 'scheduleImport']);
		add_action(TVP_TD()->prefix . '-cron-import', [ $this, 'cronImport']);
	}

	/**
	 * Development function to initially fetch trello data for test purposes
	 * If you have a fresh setup without any data, you can fetch trello data by call those action urls manually
	 * It is best to follow this order: members, boards, lists, cards, actions so all the data can be correctly connected
	 * https://yoursite.com/wp-admin/?action=tvptd&do=members
	 * https://yoursite.com/wp-admin/?action=tvptd&do=boards
	 * https://yoursite.com/wp-admin/?action=tvptd&do=lists
	 * https://yoursite.com/wp-admin/?action=tvptd&do=cards
	 * https://yoursite.com/wp-admin/?action=tvptd&do=actions
	 * The "actions" call will mostly fail because it takes to much time to process the data, or the process runs out of memory.
	 * There are several function parameter to fetch the data per e.g. board etc.
	 * TODO: Find a way to run the initial actions fetch without failure.
	 */
	public function doAction()
	{
		echo '<div style="margin-left:180px;margin-top:10px;">';

		if (isset($_GET['do']) && $_GET['do'] === 'members') {
			var_dump($this->addUpdateMembers());
		} elseif (isset($_GET['do']) && $_GET['do'] === 'boards') {
			var_dump($this->addUpdateBoards());
		} elseif (isset($_GET['do']) && $_GET['do'] === 'lists') {
			var_dump($this->addUpdateLists());
		} elseif (isset($_GET['do']) && $_GET['do'] === 'cards') {
			var_dump($this->addUpdateCards());
		} elseif (isset($_GET['do']) && $_GET['do'] === 'actions') {
			if (isset($_GET['filter'])) {
				var_dump($this->addUpdateActions(false, [$_GET['filter']]));
			} else {
				var_dump($this->addUpdateActions());
			}
		} elseif (isset($_GET['do']) && $_GET['do'] === 'delete') {
			var_dump($this->deleteAllActions());
		} else {
			var_dump('nothing specified');
		}

		echo '</div>';
	}

	/**
	 * Each item in a trello response has an id. This id represents a mongo date which can be converted into a timestamp.
	 * This function converts a mongo date into an array with human readable date and unix timestamp
	 */
	public function parseMongoDate($id)
	{
		$mongoDateparse = [];
		$timestamp = intval(substr($id, 0, 8), 16);
		$dateObj = (new \DateTime())->setTimestamp($timestamp);
		$mongoDateparse['date'] = $dateObj->format('Y-m-d');
		$mongoDateparse['timestamp'] = $timestamp;
		return $mongoDateparse;
	}

	/**
	 * Batch process all memberships of the specified trello organization, pass each membership to the WordPress user creation function.
	 */
	public function addUpdateMembers($filter = [])
	{
		if (!empty($filter) && gettype($filter) === 'string') {
			$filter = [$filter];
		}

		$memberships = TVP_TD()->Trello->API->getFromOrganization('memberships?&member=true');
		$processed = [
			'added' => 0,
			'exists' => 0,
		];

		if ($memberships) {
			foreach ($memberships as $key => $membership) {
				$membership = (array)$membership;
				if (empty($filter) || (gettype($filter) === 'array' && in_array($membership['idMember'], $filter))) {
					$nfo = $this->addUpdateMember($membership);
					if ($nfo['added']) {
						$processed['added']++;
					}
					if ($nfo['exists']) {
						$processed['exists']++;
					}
				}
			}
		} else {
			return false;
		}

		$this->updateOptionLastFetch();

		return $processed;
	}

	/**
	 * Turn a trello membership into a WordPress user.
	 */
	public function addUpdateMember($membership)
	{
		if (!empty($membership) && isset($membership['member'])) {
			$dateArray = $this->parseMongoDate($membership['id']);
			$member = (array)$membership['member'];

			$processInfo = [
				'trelloId' => $member['id'],
				'added' => false,
				'exists' => false,
				'errors' => [],
				'deleted' => false,
			];

			// check if user exists
			$userExists = false;

			$args = [
				'meta_key'   => TVP_TD()->Member->UserMeta->optionsPrefix . '-id',
				'meta_value' => $member['id'],
			];

			$userExists = get_users($args);
			$userId = false;
			$user = false;

			if (empty($userExists)) {
				// if user does not exist, check if username already taken
				// if already taken, append number
				$username = $member['username'];
				$originalName = $username;
				$suffix = 1;

				while (username_exists($username)) {
					$username = (string)$originalName . '_' . $suffix;
				}

				$userId = wp_create_user($username, bin2hex(random_bytes(16)), '');

				if ($userId) {
					$processInfo['added'] = true;
					$user = get_user_by('id', $userId);
					$user->remove_role('subscriber');
				}
			} else {
				$userId = (int)$userExists[0]->data->ID;

				if ($userId) {
					$processInfo['exists'] = true;
					$user = get_user_by('id', $userId);
				}
			}

			$processInfo['userId'] = $userId;
			$processInfo['userName'] = $user->user_login;
			$processInfo['editLink'] = get_edit_user_link($userId);

			if ($userId && $user) {
				$processInfo['userObject'] = $user;
				$nameArray = explode(' ', $member['fullName']);

				// update first name
				if (isset($nameArray[0]) && empty(get_user_meta($userId, 'first_name', true))) {
					update_user_meta($userId, 'first_name', $nameArray[0]);
				}

				// update last name
				if (isset($nameArray[1]) && empty(get_user_meta($userId, 'last_name', true))) {
					unset($nameArray[0]);
					update_user_meta($userId, 'last_name', implode(' ', $nameArray));
				}

				// update email
				if (isset($member['email']) && empty(get_user_meta($userId, 'user_email', true))) {
					update_user_meta($userId, 'user_email', $member['email']);
				}

				// add the role
				$user->add_role(TVP_TD()->Member->Role->role);

				// update meta fields
				update_field(TVP_TD()->Member->UserMeta->optionsPrefix . '-id', $member['id'], 'user_' . $userId);
				update_field(TVP_TD()->Member->UserMeta->optionsPrefix . '-type', $membership['memberType'], 'user_' . $userId);
				update_field(TVP_TD()->Member->UserMeta->optionsPrefix . '-unconfirmed', $membership['unconfirmed'] ? 1 : 0, 'user_' . $userId);
				update_field(TVP_TD()->Member->UserMeta->optionsPrefix . '-deactivated', $membership['deactivated'] ? 1 : 0, 'user_' . $userId);
				update_field(TVP_TD()->Member->UserMeta->optionsPrefix . '-avatar-url', $member['avatarUrl'] ? $member['avatarUrl'] : '', 'user_' . $userId);
				update_field(TVP_TD()->Member->UserMeta->optionsPrefix . '-date', $dateArray['date'], 'user_' . $userId);
			}

			return $processInfo;
		}
	}

	/**
	 * Turn a trello board into an action taxonomy.
	 * TVP_TD()->Trello->Action->boardTaxonomy;
	 */
	public function addUpdateBoard($board)
	{
		$taxonomy = TVP_TD()->Trello->Action->boardTaxonomy;
		if (!empty($board)) {
			$dateArray = $this->parseMongoDate($board['id']);
			$processInfo = [
				'trelloId' => $board['id'],
				'added' => false,
				'exist' => false,
				'errors' => [],
				'deleted' => false,
			];

			$args = [
				'taxonomy' => $taxonomy,
				'meta_key' => TVP_TD()->Trello->Action->optionPrefixBoard . '-id',
				'meta_value' => $board['id'],
				'hide_empty' => false,
			];

			$termExists = get_terms($args);
			$termId = false;
			$term = false;

			if (empty($termExists)) {
				$insert = wp_insert_term($board['id'], $taxonomy);

				if (!is_wp_error($insert)) {
					$processInfo['added'] = true;
					$term = get_term_by('id', $insert['term_id'], $taxonomy, OBJECT);
					$termId = $term->term_id;
				} else {
					$processInfo['errors'][] = $insert;
				}
			} else {
				$termId = (int)$termExists[0]->data->term_id;

				if ($termId) {
					$processInfo['exist'] = true;
					$term = get_term_by('id', $termId, $taxonomy, OBJECT);
				}
			}

			$processInfo['termId'] = $termId;
			$processInfo['termName'] = isset($term->name) ? $term->name : '';
			$processInfo['editLink'] = get_edit_term_link($termId, $taxonomy);

			if ($termId && $term) {
				$processInfo['termObject'] = $term;
				$processInfo['updated'] = true;

				// update description
				if (isset($board['desc'])) {
					wp_update_term($termId, $taxonomy, ['description' => $board['desc']]);
				}

				// update members on board
				$boardMembers = [];
				$boardMemberships = [];
				foreach ($board['memberships'] as $key => $boardMember) {
					$membershipDateArray = $this->parseMongoDate($boardMember->id);
					$boardMembers[] = $boardMember->idMember;
					$boardMemberships[] = [
						'id' => $boardMember->id,
						'memberId' => $boardMember->idMember,
						'date' => $membershipDateArray['date'],
					];
				}

				// add meta fields
				update_field(TVP_TD()->Trello->Action->optionPrefixBoard . '-name', $board['name'], $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixBoard . '-id', $board['id'], $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixBoard . '-url', $board['url'], $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixBoard . '-members', implode(',', $boardMembers), $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixBoard . '-memberships', json_encode($boardMemberships), $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixBoard . '-closed', $board['closed'] ? 1 : 0, $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixBoard . '-date', $dateArray['date'], $taxonomy . '_' . $termId);
				if ($board['closed']) {
					update_field(TVP_TD()->Trello->Action->optionPrefixBoard . '-date-closed', date('Y-m-d', strtotime($board['dateClosed'])), $taxonomy . '_' . $termId);
				}
			}

			return $processInfo;
		}
	}

	/**
	 * Batch process all trello boards from the specified organization into an action taxonomy.
	 * TVP_TD()->Trello->Action->boardTaxonomy;
	 */
	public function addUpdateBoards($filter = [])
	{
		if (!empty($filter) && gettype($filter) === 'string') {
			$filter = [$filter];
		}

		$boards = TVP_TD()->Trello->API->getFromOrganization('boards?fields=name,desc,closed,dateClosed,id,url,memberships');
		$processed = [
			'added' => [],
			'exist' => [],
		];

		if ($boards) {
			foreach ($boards as $key => $board) {
				$board = (array)$board;
				if (empty($filter) || (gettype($filter) === 'array' && in_array($board['id'], $filter))) {
					if ($board['name'] !== 'z[Unused board]') {
						$nfo = $this->addUpdateBoard($board);

						if ($nfo['added']) {
							$processed['added'][] = $board['name'];
						}

						if ($nfo['exist']) {
							$processed['exist'][] = $board['name'];
						}
					}
				}
			}
		}

		$this->updateOptionLastFetch();

		return $processed;
	}

	/**
	 * Turn a trello card into an action taxonomy.
	 * TVP_TD()->Trello->Action->cardTaxonomy;
	 */
	public function addUpdateCard($card)
	{
		$taxonomy = TVP_TD()->Trello->Action->cardTaxonomy;
		if (!empty($card)) {
			$dateArray = $this->parseMongoDate($card['id']);
			$processInfo = [
				'trelloId' => $card['id'],
				'added' => false,
				'exist' => false,
				'errors' => [],
				'deleted' => false,
			];

			$args = [
				'taxonomy' => $taxonomy,
				'meta_key' => TVP_TD()->Trello->Action->optionPrefixCard . '-id',
				'meta_value' => $card['id'],
				'hide_empty' => false,
			];

			$termExists = get_terms($args);
			$termId = false;
			$term = false;

			if (empty($termExists)) {
				$insert = wp_insert_term($card['id'], $taxonomy);

				if (!is_wp_error($insert)) {
					$processInfo['added'] = true;
					$term = get_term_by('id', $insert['term_id'], $taxonomy, OBJECT);
					$termId = $term->term_id;
				} else {
					$processInfo['errors'][] = $insert;
				}
			} else {
				$termId = (int)$termExists[0]->data->term_id;

				if ($termId) {
					$processInfo['exist'] = true;
					$term = get_term_by('id', $termId, $taxonomy, OBJECT);
				}
			}

			$processInfo['termId'] = $termId;
			$processInfo['termName'] = isset($term->name) ? $term->name : '';
			$processInfo['editLink'] = get_edit_term_link($termId, $taxonomy);

			if ($termId && $term) {
				// $processInfo['termObject'] = $term;
				$processInfo['updated'] = true;

				// update description
				if (isset($card['desc'])) {
					wp_update_term($termId, $taxonomy, ['description' => $card['desc']]);
				}

				// add meta fields
				update_field(TVP_TD()->Trello->Action->optionPrefixCard . '-name', $card['name'], $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixCard . '-id', $card['id'], $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixCard . '-url', $card['url'], $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixCard . '-members', implode(',', $card['idMembers']), $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixCard . '-closed', $card['closed'] ? 1 : 0, $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixCard . '-id-board', $card['idBoard'], $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixCard . '-id-list', $card['idList'], $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixCard . '-date', $dateArray['date'], $taxonomy . '_' . $termId);
			}

			return $processInfo;
		}
	}

	/**
	 * Batch process all trello cards from the specified organization into an action taxonomy.
	 * TVP_TD()->Trello->Action->cardTaxonomy;
	 */
	public function addUpdateCards($boardFilter = [], $cardFilter = [])
	{
		if (!empty($boardFilter) && gettype($boardFilter) === 'string') {
			$boardFilter = [$boardFilter];
		}
		if (!empty($cardFilter) && gettype($cardFilter) === 'string') {
			$cardFilter = [$cardFilter];
		}

		$boards = TVP_TD()->API->Action->getBoards();
		$processed = [
			'added' => 0,
			'exist' => 0,
		];

		foreach ($boards as $key => $board) {
			if (empty($boardFilter) || (gettype($boardFilter) === 'array' && in_array($board['id'], $boardFilter))) {
				$cards = TVP_TD()->Trello->API->get('boards/'.$board['id'].'/cards?fields=id,closed,desc,idBoard,idList,name,idMembers,url');

				foreach ($cards as $key => $card) {
					$card = (array)$card;
					if (empty($cardFilter) || (gettype($cardFilter) === 'array' && in_array($card['id'], $cardFilter))) {
						$nfo = $this->addUpdateCard($card);

						if ($nfo['added']) {
							$processed['added']++;
						}

						if ($nfo['exist']) {
							$processed['exist']++;
						}
					}
				}
			}
		}

		$this->updateOptionLastFetch();

		return $processed;
	}

	/**
	 * Turn a trello list into an action taxonomy.
	 * TVP_TD()->Trello->Action->listTaxonomy;
	 */
	public function addUpdateList($list)
	{
		$taxonomy = TVP_TD()->Trello->Action->listTaxonomy;
		if (!empty($list)) {
			$dateArray = $this->parseMongoDate($list['id']);
			$processInfo = [
				'trelloId' => $list['id'],
				'added' => false,
				'exist' => false,
				'errors' => [],
				'deleted' => false,
			];

			$args = [
				'taxonomy' => $taxonomy,
				'meta_key' => TVP_TD()->Trello->Action->optionPrefixList . '-id',
				'meta_value' => $list['id'],
				'hide_empty' => false,
			];

			$termExists = get_terms($args);
			$termId = false;
			$term = false;

			if (empty($termExists)) {
				$insert = wp_insert_term($list['id'], $taxonomy);

				if (!is_wp_error($insert)) {
					$processInfo['added'] = true;
					$term = get_term_by('id', $insert['term_id'], $taxonomy, OBJECT);
					$termId = $term->term_id;
				} else {
					$processInfo['errors'][] = $insert;
				}
			} else {
				$termId = (int)$termExists[0]->data->term_id;

				if ($termId) {
					$processInfo['exist'] = true;
					$term = get_term_by('id', $termId, $taxonomy, OBJECT);
				}
			}

			$processInfo['termId'] = $termId;
			$processInfo['termName'] = isset($term->name) ? $term->name : '';
			$processInfo['editLink'] = get_edit_term_link($termId, $taxonomy);

			if ($termId && $term) {
				$processInfo['termObject'] = $term;
				$processInfo['updated'] = true;

				// update description
				if (isset($list['desc'])) {
					wp_update_term($termId, $taxonomy, ['description' => $list['desc']]);
				}

				// add meta fields
				update_field(TVP_TD()->Trello->Action->optionPrefixList . '-name', $list['name'], $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixList . '-id', $list['id'], $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixList . '-date', $dateArray['date'], $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixList . '-closed', $list['closed'] ? 1 : 0, $taxonomy . '_' . $termId);
				update_field(TVP_TD()->Trello->Action->optionPrefixList . '-id-board', $list['idBoard'], $taxonomy . '_' . $termId);
			}

			return $processInfo;
		}
	}

	/**
	 * Batch process all trello lists from the specified organization into an action taxonomy.
	 * TVP_TD()->Trello->Action->listTaxonomy;
	 */
	public function addUpdateLists($boardFilter = [], $listFilter = [])
	{
		if (!empty($boardFilter) && gettype($boardFilter) === 'string') {
			$boardFilter = [$boardFilter];
		}
		if (!empty($listFilter) && gettype($listFilter) === 'string') {
			$listFilter = [$listFilter];
		}

		$boards = TVP_TD()->API->Action->getBoards();
		$processed = [
			'added' => 0,
			'exist' => 0,
		];

		foreach ($boards as $key => $board) {
			if (empty($boardFilter) || (gettype($boardFilter) === 'array' && in_array($board['id'], $boardFilter))) {
				$lists = TVP_TD()->Trello->API->get('boards/'.$board['id'].'/lists');

				foreach ($lists as $key => $list) {
					$list = (array)$list;
					if (empty($listFilter) || (gettype($listFilter) === 'array' && in_array($list['id'], $listFilter))) {
						$nfo = $this->addUpdateList($list);

						if ($nfo['added']) {
							$processed['added']++;
						}

						if ($nfo['exist']) {
							$processed['exist']++;
						}
					}
				}
			}
		}

		$this->updateOptionLastFetch();

		return $processed;
	}

	/**
	 * Turn a trello action into an action post.
	 * TVP_TD()->Trello->Action->postType;
	 */
	public function addUpdateAction($action, $exists = false)
	{
		$postType = TVP_TD()->Trello->Action->postType;
		$processInfo = [
			'trelloId' => false,
			'added' => false,
			'exist' => false,
			'errors' => [],
		];

		if (!empty($action)) {
			$processInfo['trelloId'] = $action['id'];
			$processInfo['exists'] = $exists;

			$dateArray = $this->parseMongoDate($action['id']);
			$postId = false;
			$post = false;

			if ($exists) {
				$postId = post_exists($action['id'], '', '', TVP_TD()->Trello->Action->postType);
				if ($postId) {
					$processInfo['exist'] = true;
					$post = get_post($postId);
				}
			}

			if (!$processInfo['exist']) {
				$postArgs = [
					'post_type' => $postType,
					'post_date' => date('Y-m-d H:i:s', strtotime($action['date'])),
					'post_title' => $action['id'],
					'post_status' => 'publish',
				];

				$insert = wp_insert_post($postArgs);

				if (!is_wp_error($insert)) {
					$processInfo['added'] = true;
					$post = get_post($insert);
					$postId = $post->ID;
				} else {
					$processInfo['errors'][] = $insert;
				}
			}

			$processInfo['postId'] = $postId;
			$processInfo['editLink'] = get_edit_post_link($postId);

			if ($postId && $post) {
				// Prepare and insert the custom post meta
				$metaKeys = [];
				$metaKeys[TVP_TD()->Trello->Action->optionPrefixAction . '-id'] = $action['id'];
				$metaKeys[TVP_TD()->Trello->Action->optionPrefixAction . '-type'] =  $action['type'];
				$metaKeys[TVP_TD()->Trello->Action->optionPrefixAction . '-id-creator'] = $action['idMemberCreator'];
				$metaKeys[TVP_TD()->Trello->Action->optionPrefixAction . '-date'] = $dateArray['date'];

				if (isset($action['data']->board->id)) {
					$metaKeys[TVP_TD()->Trello->Action->optionPrefixAction . '-id-board'] = $action['data']->board->id;
					$termExists = term_exists($action['data']->board->id, TVP_TD()->Trello->Action->boardTaxonomy);
					if (!empty($termExists)) {
						wp_set_post_terms($postId, [$termExists['term_id']], TVP_TD()->Trello->Action->boardTaxonomy);
					}
				}
				if (isset($action['data']->list->id)) {
					$metaKeys[TVP_TD()->Trello->Action->optionPrefixAction . '-id-list'] = $action['data']->list->id;
					$termExists = term_exists($action['data']->list->id, TVP_TD()->Trello->Action->listTaxonomy);
					if (!empty($termExists)) {
						wp_set_post_terms($postId, [$termExists['term_id']], TVP_TD()->Trello->Action->listTaxonomy);
					}
				}
				if (isset($action['data']->card->id)) {
					$metaKeys[TVP_TD()->Trello->Action->optionPrefixAction . '-id-card'] = $action['data']->card->id;
					$termExists = term_exists($action['data']->card->id, TVP_TD()->Trello->Action->cardTaxonomy);
					if (!empty($termExists)) {
						wp_set_post_terms($postId, [$termExists['term_id']], TVP_TD()->Trello->Action->cardTaxonomy);
					}
				}

				// set the metavalues in one query for better performance
				global $wpdb;
				$customFields = [];
				$placeHodlers = [];
				$queryString = "INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value) VALUES ";
				foreach ($metaKeys as $key => $value) {
					array_push($customFields, $postId, $key, $value);
					$placeHodlers[] = "('%d', '%s', '%s')";
				}
				$queryString .= implode(', ', $placeHodlers);
				$wpdb->query($wpdb->prepare("$queryString ", $customFields));
			}
		}

		return $processInfo;
	}

	/**
	 * Batch process all trello actions from the specified organization into an action post.
	 * TVP_TD()->Trello->Action->postType;
	 */
	public function addUpdateActions($processAll = false, $boardFilter = [], $actionFilter = [])
	{
		// global $wpdb;
		$startTime = microtime(true);

		if ('true' === get_field($this->optionPrefix . '-processing', 'options')) {
			return 'already processing';
		}

		update_field($this->optionPrefix . '-processing', 'true', 'options');

		if (!defined('WP_IMPORTING')) {
			define('WP_IMPORTING', true);
		}
		ini_set("memory_limit", -1);
		set_time_limit(0);
		ignore_user_abort(true);

		wp_defer_term_counting(true);
		wp_defer_comment_counting(true);
		// $wpdb->query('SET autocommit = 0;');

		// https://wordpress.stackexchange.com/a/314226
		register_shutdown_function(function () {
			// global $wpdb;
			// $wpdb->query('COMMIT;');
			// $wpdb->query('SET autocommit = 1;');
			wp_defer_term_counting(false);
			wp_defer_comment_counting(false);
			update_field($this->optionPrefix . '-processing', 'false', 'options');
		});

		if (!empty($boardFilter) && gettype($boardFilter) === 'string') {
			$boardFilter = [$boardFilter];
		}
		if (!empty($actionFilter) && gettype($actionFilter) === 'string') {
			$actionFilter = [$actionFilter];
		}

		$postType = TVP_TD()->Trello->Action->postType;
		$boards = TVP_TD()->API->Action->getBoards();
		$processed = [];

		if ($boards) {
			foreach ($boards as $key => $board) {
				$board = (array)$board;
				if (empty($boardFilter) || (gettype($boardFilter) === 'array' && in_array($board['id'], $boardFilter))) {
					if ($board['name'] !== 'z[Unused board]') {
						$limit = 1000; // max
						$done = false;
						$before = false;
						$amount = 0;

						$processed[$board['name']] = [ 'added' => 0 ];

						if ($processAll) {
							$processed[$board['name']]['exist'] = 0;
						}

						$existingIdsOnCurrentBoard = [];
						$allExisitngActionsOnCurrentBoard = get_posts([
							'post_type' => TVP_TD()->Trello->Action->postType,
							'meta_key' => TVP_TD()->Trello->Action->optionPrefixAction . '-id-board',
							'meta_value' => $board['id'],
							'numberposts' => -1,
							'fields' => 'ids',
						]);

						foreach ($allExisitngActionsOnCurrentBoard as $key => $postId) {
							$existingIdsOnCurrentBoard[] = get_post_meta($postId, TVP_TD()->Trello->Action->optionPrefixAction . '-id', true);
						}

						while (!$done) {
							$request = 'boards/' . $board['id'] . '/actions?fields=id,idMemberCreator,data,type,date&member=false&memberCreator=false&limit=' . $limit;
							if ($before) {
								$request = $request . '&before=' . $before;
							}

							$actions = TVP_TD()->Trello->API->get($request);

							foreach ($actions as $key => $action) {
								$action = (array)$action;
								if (empty($actionFilter) || (gettype($actionFilter) === 'array' && in_array($action['id'], $actionFilter))) {
									$exists = in_array($action['id'], $existingIdsOnCurrentBoard);
									if ($processAll) {
										$nfo = $this->addUpdateAction($action, $exists);

										if ($nfo['added']) {
											$processed[$board['name']]['added']++;
										}
										if ($nfo['exist']) {
											$processed[$board['name']]['exist']++;
										}
									} elseif (!$exists) {
										$nfo = $this->addUpdateAction($action, $exists);
										if ($nfo['added']) {
											$processed[$board['name']]['added']++;
										}
									} else {
										$done = true;
										break;
										// $processed[$board['name']]['exist']++;
									}
								}
							}

							// if requests is empty or amount of actions is less not 1000 as the limit
							if (empty($actions) || count($actions) !== $limit) {
								$done = true;
							}

							// if not done and response was not empty set before parameter to the last item id in current response
							if (!$done) {
								$before = end($actions)->id;
							}
						}
					}
				}
			}
		}

		wp_defer_term_counting(false);
		wp_defer_comment_counting(false);
		update_field($this->optionPrefix . '-processing', 'false', 'options');
		$processed['time'] = round((microtime(true) - $startTime), 2) . 's';

		$this->updateOptionLastFetch();

		return $processed;
	}

	/**
	 * Function to delete all action posts programatically
	 * TVP_TD()->Trello->Action->postType;
	 */
	public function deleteAllActions()
	{
		global $wpdb;
		$startTime = microtime(true);

		if ('true' === get_field($this->optionPrefix . '-processing', 'options')) {
			return 'currently adding actions';
		}

		$removed = 0;
		if (!defined('WP_IMPORTING')) {
			define('WP_IMPORTING', true);
		}
		ini_set("memory_limit", -1);
		set_time_limit(0);
		$wpdb->query('SET autocommit = 0;');

		wp_defer_term_counting(true);
		wp_defer_comment_counting(true);

		// https://wordpress.stackexchange.com/a/314226
		register_shutdown_function(function () {
			global $wpdb;
			$wpdb->query('COMMIT;');
			$wpdb->query('SET autocommit = 1;');
			wp_defer_term_counting(false);
			wp_defer_comment_counting(false);
		});

		$allposts= get_posts(['post_type'=>TVP_TD()->Trello->Action->postType,'numberposts'=>10000]);
		while (!empty($allposts)) {
			$allposts= get_posts(['post_type'=>TVP_TD()->Trello->Action->postType,'numberposts'=>10000]);
			foreach ($allposts as $eachpost) {
				wp_delete_post($eachpost->ID, true);
				$removed++;
			}
		}

		wp_defer_term_counting(false);
		wp_defer_comment_counting(false);

		return [
			'removed' => $removed,
			'time' => round((microtime(true) - $startTime), 2) . 's'
		];
	}

	/**
	 * Check for user duplicates
	 */
	public function checkForUserDuplicates()
	{
		$users = get_users([ 'role__in' => [ TVP_TD()->Member->Role->role ], 'fields' => 'ids' ]);
		$test = [];
		$duplicates = [];
		foreach ($users as $key => $userId) {
			$id = get_user_meta($userId, TVP_TD()->Member->UserMeta->optionsPrefix . '-id', true);
			if (isset($test[$id])) {
				$test[$id]++;
				$duplicates[] = [
					'userId' => $userId,
					'trelloId' => $id,
					'userLoging' => get_user_data($userId)->user_login,
				];
			} else {
				$test[$id] = 1;
			}
		}
		return $duplicates;
	}

	/**
	 * Check for card duplicates
	 */
	public function checkForCardDuplicates()
	{
		$terms = get_terms([
			'taxonomy' => TVP_TD()->Trello->Action->cardTaxonomy,
			'hide_empty' => false,
		]);

		$test = [];
		$duplicates = [];
		foreach ($terms as $key => $term) {
			if (isset($test[$term->name])) {
				$test[$term->name]++;
				$duplicates[] = [
					'termId' => $term->term_id,
					'trelloId' => $term->name,
				];
			} else {
				$test[$term->term_id] = 1;
			}
		}

		return $duplicates;
	}

	public function checkForListDuplicates()
	{
		$terms = get_terms([
			'taxonomy' => TVP_TD()->Trello->Action->listTaxonomy,
			'hide_empty' => false,
		]);

		$test = [];
		$duplicates = [];
		foreach ($terms as $key => $term) {
			if (isset($test[$term->name])) {
				$test[$term->name]++;
				$duplicates[] = [
					'termId' => $term->term_id,
					'trelloId' => $term->name,
				];
			} else {
				$test[$term->term_id] = 1;
			}
		}

		return $duplicates;
	}

	/**
	 * Check for action duplicates
	 */
	public function checkForActionDuplicates()
	{
		$posts = get_posts([
			'post_type' => TVP_TD()->Trello->Action->postType,
			'numberposts' => -1,
		]);

		$test = [];
		$duplicates = [];
		foreach ($posts as $key => $post) {
			if (isset($test[$post->post_title])) {
				$test[$post->post_title]++;
				$duplicates[] = [
					'postId' => $post->post_id,
					'trelloId' => $post->post_title,
				];
			} else {
				$test[$post->term_id] = 1;
			}
		}

		return $duplicates;
	}

	/**
	 * Update tvpdt-last-fetch option
	 */
	public function updateOptionLastFetch()
	{
		update_option($this->optionLastFetch, time());
	}

	public function scheduleImport()
	{
		if (! wp_next_scheduled(TVP_TD()->prefix . '-cron-import')) {
			wp_schedule_event(strtotime('02:00:00'), 'daily', TVP_TD()->prefix . '-cron-import');
		}
	}

	public function cronImport()
	{
		$this->addUpdateMembers();
		$this->addUpdateBoards();
		$this->addUpdateLists();
		$this->addUpdateCards();
		$this->addUpdateActions();
	}
}
