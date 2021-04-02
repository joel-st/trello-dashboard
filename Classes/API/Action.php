<?php

namespace TVP\TrelloDashboard\API;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Action
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

	public function getActions($metaQuery = [])
	{
		$args = [
			'post_type' => TVP_TD()->Trello->Action->postType,
			'numberposts' => -1,
			'fields' => 'ids',
		];

		if (!empty($metaQuery)) {
			$args['meta_query'] = $metaQuery;
		}

		$actions = get_posts($args);

		return $actions;
	}

	public function getBoards($metaQuery = [])
	{
		$taxonomy = TVP_TD()->Trello->Action->boardTaxonomy;

		$args = [
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
			'meta_key' => TVP_TD()->Trello->Action->optionPrefixBoard . '-name',
			'orderby'  => 'meta_value',
			'order'    => 'ASC'
		];

		if (!empty($metaQuery)) {
			$args['meta_query'] = $metaQuery;
		}

		$terms = get_terms($args);

		$boards = [];
		foreach ($terms as $key => $term) {
			$boards[] = [
				'id' => get_field(TVP_TD()->Trello->Action->optionPrefixBoard . '-id', $taxonomy . '_' . $term->term_id),
				'termId' => $term->term_id,
				'name' => get_field(TVP_TD()->Trello->Action->optionPrefixBoard . '-name', $taxonomy . '_' . $term->term_id),
				'url' => get_field(TVP_TD()->Trello->Action->optionPrefixBoard . '-url', $taxonomy . '_' . $term->term_id),
			];
		}

		return $boards;
	}

	public function getBoard($id)
	{
		return get_term_by('name', $id, TVP_TD()->Trello->Action->boardTaxonomy);
	}

	public function getList($id)
	{
		return get_term_by('name', $id, TVP_TD()->Trello->Action->listTaxonomy);
	}

	public function getBoardTotal()
	{
		$numTerms = wp_count_terms(TVP_TD()->Trello->Action->boardTaxonomy, [
			'hide_empty'=> false,
		]);
		return $numTerms;
	}


	public function getCardTotal()
	{
		$numTerms = wp_count_terms(TVP_TD()->Trello->Action->cardTaxonomy, [
			'hide_empty'=> false,
		]);
		return $numTerms;
	}

	public function getActionTotal($metaQuery = [])
	{
		if (!empty($metaQuery)) {
			$actions = $this->getActions($metaQuery);
			$numPosts = count($actions);
		} else {
			$numPosts = wp_count_posts(TVP_TD()->Trello->Action->postType);
			$numPosts = $numPosts->publish;
		}
		return $numPosts;
	}

	public function getMemberTotalAtLeastOneBoard()
	{
		$memberTotalAtLeastOneBoard = [];
		$boards = $this->getBoards();

		$taxonomy = TVP_TD()->Trello->Action->boardTaxonomy;
		foreach ($boards as $key => $board) {
			$boardMembers = explode(',', get_field(TVP_TD()->Trello->Action->optionPrefixBoard . '-members', $taxonomy . '_' . $board['termId']));
			foreach ($boardMembers as $key => $id) {
				$memberTotalAtLeastOneBoard[$id] = isset($memberTotalAtLeastOneBoard[$id]) ? $memberTotalAtLeastOneBoard[$id]++ : 0;
			}
		}
		return count($memberTotalAtLeastOneBoard);
	}

	public function getMemberTotalAtLeastOneAction($metaQuery = [])
	{
		$users = [];
		$args = [
			'post_type' => TVP_TD()->Trello->Action->postType,
			'numberposts' => -1,
			'fields' => 'ids',
		];

		if (!empty($metaQuery)) {
			$args['meta_query'] = $metaQuery;
		}

		$posts = get_posts($args);

		foreach ($posts as $key => $postId) {
			$creator = get_field(TVP_TD()->Trello->Action->optionPrefixAction . '-id-creator', $postId);
			$users[$creator] = isset($users[$creator]) ? $users[$creator] + 1 : 1;
		}
		return count($users);
	}

	public function getActionsOnBoardsTotal($metaQuery = [])
	{
		$actions = $this->getActions($metaQuery);
		$boards = [];
		foreach ($actions as $key => $postId) {
			$board = get_field(TVP_TD()->Trello->Action->optionPrefixAction . '-id-board', $postId);
			$boards[$board] = isset($boards[$board]) ? $boards[$board] + 1 : 1;
		}
		return count($boards);
	}

	public function getMemberAddedJoinedBoard($metaQuery = [])
	{
		$memberAddedJoinedBoard = [];
		$members = TVP_TD()->API->Member->getMember($metaQuery);
		foreach ($members as $key => $member) {
			$boards = $this->getBoards([
				[
					'key'=> TVP_TD()->Trello->Action->optionPrefixBoard . '-members',
					'value' => get_field(TVP_TD()->Member->UserMeta->optionsPrefix . '-id', 'user_' . $member->ID),
					'compare' => 'LIKE',
				]
			]);

			foreach ($boards as $key => $board) {
				if (isset($memberAddedJoinedBoard[$board['name']])) {
					$memberAddedJoinedBoard[$board['name']]['number']++;
				} else {
					$memberAddedJoinedBoard[$board['name']] = [
						'number' => 1,
					];
				}
			}
		}

		foreach ($memberAddedJoinedBoard as $boardName => $data) {
			$memberAddedJoinedBoard[$boardName]['percentual'] = round(100 * $memberAddedJoinedBoard[$boardName]['number'] / count($members));
		}

		return $memberAddedJoinedBoard;
	}

	public function getMemberAddedPerformedActions($metaQuery = [])
	{
		$memberAddedPerformedActions = [
			'0' => ['number' => 0, 'percentual' => 0],
			'1' => ['number' => 0, 'percentual' => 0],
			'3' => ['number' => 0, 'percentual' => 0],
			'5' => ['number' => 0, 'percentual' => 0],
			'10' => ['number' => 0, 'percentual' => 0],
			'20' => ['number' => 0, 'percentual' => 0],
		];

		$members = TVP_TD()->API->Member->getMember($metaQuery);
		foreach ($members as $key => $member) {
			$actions = $this->getActions([
				[
					'key'=> TVP_TD()->Trello->Action->optionPrefixAction . '-id-creator',
					'value' => get_field(TVP_TD()->Member->UserMeta->optionsPrefix . '-id', 'user_' . $member->ID),
				]
			]);

			if (empty($actions)) {
				$memberAddedPerformedActions['0']['number']++;
			} else {
				$count = count($actions);
				if ($count >= 1) {
					$memberAddedPerformedActions['1']['number']++;

					if ($count >= 3) {
						$memberAddedPerformedActions['3']['number']++;

						if ($count >= 5) {
							$memberAddedPerformedActions['5']['number']++;

							if ($count >= 10) {
								$memberAddedPerformedActions['10']['number']++;

								if ($count >= 20) {
									$memberAddedPerformedActions['20']['number']++;
								}
							}
						}
					}
				}
			}
		}

		$countMember = count($members);
		foreach ($memberAddedPerformedActions as $key => $data) {
			$memberAddedPerformedActions[$key]['percentual'] = round(100 * $memberAddedPerformedActions[$key]['number'] / $countMember);
		}

		return $memberAddedPerformedActions;
	}

	public function getNumberOfActionsOnBoards($metaQuery = [])
	{
		$numberOfActionsOnBoards = [];

		$actions = $this->getActions($metaQuery);

		foreach ($actions as $key => $postId) {
			$boardId = get_field(TVP_TD()->Trello->Action->optionPrefixAction . '-id-board', $postId);
			$board = $this->getBoards([
				[
					'key'=> TVP_TD()->Trello->Action->optionPrefixBoard . '-id',
					'value' => $boardId,
				]
			]);
			if (!empty($board)) {
				$board = $board[0];
				$numberOfActionsOnBoards[$board['name']] = isset($numberOfActionsOnBoards[$board['name']]) ? $numberOfActionsOnBoards[$board['name']] + 1 : 1;
			}
		}

		asort($numberOfActionsOnBoards);
		$numberOfActionsOnBoards = array_reverse($numberOfActionsOnBoards);

		return $numberOfActionsOnBoards;
	}
}
