<?php

namespace TVP\TrelloDashboard\View;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Dashboard
{
	/**
	 * Class Properties
	 */
	public $prefix = '';
	public $slug = 'trello-dashboard';
	public $authCookie = '';

	/**
	 * Set Class Properties
	 */
	public function __construct()
	{
		$this->authCookie = TVP_TD()->authCookie;
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
		add_action('init', [$this, 'redirect']);
		add_action('init', [$this, 'loadDashboard']);
	}

	public function getPermalink()
	{
		$dashboardSlug = get_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-dashboard-slug', 'options');

		if (empty($dashboardSlug)) {
			return false;
		}

		$dashboardUrl = trailingslashit(home_url($dashboardSlug));

		return $dashboardUrl;
	}

	public function isDashboard()
	{
		$currentUrl = home_url($_SERVER['REQUEST_URI']);

		if ($currentUrl === $this->getPermalink()) {
			return true;
		}

		return false;
	}

	public function redirect()
	{
		if (home_url($_SERVER['REQUEST_URI']) === untrailingslashit($this->getPermalink())) {
			wp_redirect($this->getPermalink());
			exit;
		}
	}

	public function loadDashboard()
	{
		global $wp_query;
		$currentUser = wp_get_current_user();

		if ($this->isDashboard()) {
			header('HTTP/1.1 200 OK');
			header('Content-Type: text/html; charset=utf-8');

			echo $this->getHeader();

			echo '<body id="tvptd">';
			echo '<div class="tvptd__dashboard tvptd__dashboard--loading" id="tvptd-loading">';
			echo '<div class="tvptd__spinner spinner"></div>';
			echo '</div>';

			echo $this->getFooter();
			echo '</body>';

			exit;
		}
	}

	public function getDashboardContent()
	{
		$content = '<div class="tvptd__dashboard tvptd__dashboard--overview">';

		$content .= '<header class="tvptd__header">';
		$content .= $this->getBrand();
		$content .= $this->getUserProfile();
		$content .= '</header>'; // .tvptd__header

		$content .= '<div class="tvptd__pre-content">';
		$content .= get_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-dashboard-pre-content', 'options');
		$content .= '</div>'; // .tvptd__pre-content

		$content .= '<div class="tvptd__content">';

		$content .= '<div class="tvptd__main">';
		$content .= $this->getOrganizationOverview();
		$content .= $this->getOrganizationStatistics();
		$content .= '</div>'; // .tvptd__main

		$content .= '<div class="tvptd__aside">';

		if (have_rows(TVP_TD()->Options->DashboardManager->optionPrefix . '-latest-news', 'options')) {
			$content .= $this->getLatestNews();
		}

		if (have_rows(TVP_TD()->Options->DashboardManager->optionPrefix . '-useful-information', 'options')) {
			$content .= $this->getUsefulInformation();
		}

		if (have_rows(TVP_TD()->Options->DashboardManager->optionPrefix . '-volunteer-resources', 'options')) {
			$content .= $this->getVolunteerResources();
		}

		if (have_rows(TVP_TD()->Options->DashboardManager->optionPrefix . '-help-needed', 'options')) {
			$content .= $this->getHelpNeeded();
		}

		$content .= $this->getBoardList();

		$content .= '</div>'; // .tvptd__aside

		$content .= '</div>'; // .tvptd__content

		$content .= '</div>'; // .tvptd__dashboard

		return $content;
	}

	public function getLatestNews()
	{
		$content = '<div class="tvptd__widget tvptd__widget--latest-news" id="tvptd-latest-news">';

		$content .= '<header class="tvptd__widget-head">';
		$content .= '<h3 class="tvptd__widget-title">'.__('Latest News', 'tvp-trello-dashbaord').'</h3>';
		$content .= '</header>'; // .widget__head

		$content .= '<main class="tvptd__widget-content">';
		$content .= '<section class="tvptd__widget-section">';

		$content .= '<ul class="tvptd__widget-list tvptd__widget-list--latest-news">';
		while (have_rows(TVP_TD()->Options->DashboardManager->optionPrefix . '-latest-news', 'options')) {
			the_row();

			$link = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-latest-news-link');
			$label = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-latest-news-label');

			$content .= '<li class="tvptd__widget-list-item">';
			$content .= '<a class="tvptd__widget-list-item-permalink" href="'.$link.'" target="_blank">' . $label . '</a>';
			$content .= '</li>';
		}

		$content .= '</ul>';

		$content .= '</section>'; // .tvptd__widget-section
		$content .= '</main>'; // .tvptd__widget-content

		$content .= '</div>'; // .tvptd__widget

		return $content;
	}

	public function getUsefulInformation()
	{
		$content = '<div class="tvptd__widget tvptd__widget--useful-information" id="tvptd-useful-information">';

		$content .= '<header class="tvptd__widget-head">';
		$content .= '<h3 class="tvptd__widget-title">'.__('Useful Information', 'tvp-trello-dashbaord').'</h3>';
		$content .= '</header>'; // .widget__head

		$content .= '<main class="tvptd__widget-content">';
		$content .= '<section class="tvptd__widget-section">';

		$content .= '<ul class="tvptd__widget-list tvptd__widget-list--useful-information">';

		while (have_rows(TVP_TD()->Options->DashboardManager->optionPrefix . '-useful-information', 'options')) {
			the_row();

			$link = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-useful-information-link');
			$label = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-useful-information-label');

			$content .= '<li class="tvptd__widget-list-item">';
			$content .= '<a class="tvptd__widget-list-item-permalink" href="'.$link.'" target="_blank">' . $label . '</a>';
			$content .= '</li>';
		}

		$content .= '</ul>';

		$content .= '</section>'; // .tvptd__widget-section
		$content .= '</main>'; // .tvptd__widget-content

		$content .= '</div>'; // .tvptd__widget

		return $content;
	}

	public function getVolunteerResources()
	{
		$content = '<div class="tvptd__widget tvptd__widget--volunteer-resources" id="tvptd-volunteer-resources">';

		$content .= '<header class="tvptd__widget-head">';
		$content .= '<h3 class="tvptd__widget-title">'.__('Volunteer Ressources', 'tvp-trello-dashbaord').'</h3>';
		$content .= '</header>'; // .widget__head

		$content .= '<main class="tvptd__widget-content">';
		$content .= '<section class="tvptd__widget-section">';

		$content .= '<ul class="tvptd__widget-list tvptd__widget-list--volunteer-resources">';

		while (have_rows(TVP_TD()->Options->DashboardManager->optionPrefix . '-volunteer-resources', 'options')) {
			the_row();

			$link = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-volunteer-resources-link');
			$label = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-volunteer-resources-label');

			$content .= '<li class="tvptd__widget-list-item">';
			$content .= '<a class="tvptd__widget-list-item-permalink" href="'.$link.'" target="_blank">' . $label . '</a>';
			$content .= '</li>';
		}

		$content .= '</ul>';

		$content .= '</section>'; // .tvptd__widget-section
		$content .= '</main>'; // .tvptd__widget-content

		$content .= '</div>'; // .tvptd__widget

		return $content;
	}

	public function getHelpNeeded()
	{
		$content = '<div class="tvptd__widget tvptd__widget--help-needed" id="tvptd-help-needed">';

		$content .= '<header class="tvptd__widget-head">';
		$content .= '<h3 class="tvptd__widget-title">'.__('Help Needed', 'tvp-trello-dashbaord').'</h3>';
		$content .= '</header>'; // .widget__head

		$content .= '<main class="tvptd__widget-content">';
		$content .= '<section class="tvptd__widget-section">';

		$content .= '<ul class="tvptd__widget-list tvptd__widget-list--help-needed">';

		while (have_rows(TVP_TD()->Options->DashboardManager->optionPrefix . '-help-needed', 'options')) {
			the_row();

			$link = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-help-needed-link');
			$label = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-help-needed-label');

			$content .= '<li class="tvptd__widget-list-item">';
			$content .= '<a class="tvptd__widget-list-item-permalink" href="'.$link.'" target="_blank">' . $label . '</a>';
			$content .= '</li>';
		}

		$content .= '</ul>';

		$content .= '</section>'; // .tvptd__widget-section
		$content .= '</main>'; // .tvptd__widget-content

		$content .= '</div>'; // .tvptd__widget

		return $content;
	}

	public function getHeader()
	{
		$header = '<head>';
		if ($this->isDashboard()) {
			$header .= '<title>'.__('TVP Trello Dashboard', 'tvp-trello-dashboard').'</title>';
			$header .= '<meta name="viewport" content="initial-scale=1">';
			$header .= '<meta charset="utf-8" />';
			$header .= '<link rel="stylesheet" type="text/css" href="' . TVP_TD()->assetsDirUrl . 'styles/public.css' . '"/>';
		}
		$header .= '</head>';

		return $header;
	}

	public function getFooter()
	{
		$tvpTdVars = TVP_TD()->getTdVars();
		$footer = '<script src="' . TVP_TD()->assetsDirUrl . 'scripts/jquery-3.2.1.min.js?ver=' . filemtime(TVP_TD()->assetsDirPath . 'scripts/jquery-3.2.1.min.js') . '"></script>';
		$footer .= '<script>var tvpTdVars = '.json_encode($tvpTdVars).'</script>';
		$footer .= '<script src="https://trello.com/1/client.js?key=' . TVP_TD()->Options->TrelloIntegration->getApiKey() . '"></script>';
		$footer .= '<script src="' . TVP_TD()->assetsDirUrl . 'scripts/public.js?ver=' . filemtime(TVP_TD()->assetsDirPath . 'scripts/public.js') . '"></script>';
		return $footer;
	}

	public function getUserProfile()
	{
		$currentUser = wp_get_current_user();

		$profile = '<div class="tvptd__user-profile" id="tvptd-profile">';

		$profile .= '<div class="tvptd__user-avatar">';
		$profile .= get_avatar($currentUser->get('ID'));
		$profile .= '</div>'; // .tvptd__user-avatar

		$profile .= '<div class="tvptd__user-wrap">';
		$profile .= '<h4 class="tvptd__user-name">' . $currentUser->get('user_nicename') . '</h4>';
		$profile .= '<ul class="tvptd__user-actions">';
		$profile .= '<li class="tvptd__user-action">';
		$profile .= '<a href="' . get_edit_user_link($currentUser->get('ID')) . '">' . _x('Edit Profile', 'Dashboard profile edit action', 'tvp-trello-dashboard') . '</a>';
		$profile .= '</li>';
		$profile .= '<li class="tvptd__user-action">';
		$profile .= '<button class="button button--textlink" id="' . TVP_TD()->prefix . '-logout' . '">' . _x('Logout', 'Dashboard logout action', 'tvp-trello-dashboard') . '</button>';
		$profile .= '</li>';
		$profile .= '</ul>'; // .tvptd__user-actions
		$profile .= '</div>'; // .tvptd__user-wrap


		$profile .= '</div>'; // .tvptd__user-profile

		return $profile;
	}

	public function getBrand()
	{
		$brand = '<div class="tvptd__brand">';
		$brand .= get_custom_logo();
		$brand .= '</div>'; // .tvptd__user-brand

		return $brand;
	}

	public function getOrganizationOverview()
	{
		$organizationOverview = '<div class="tvptd__widget tvptd__widget--organization-overview" id="tvptd-organization-overview">';

		$organizationOverview .= '<header class="tvptd__widget-head">';
		$organizationOverview .= '<h3 class="tvptd__widget-title">'.__('Organization Overview', 'tvp-trello-dashbaord').'</h3>';
		$organizationOverview .= '<div class="tvptd__widget-actions">';
		$organizationOverview .= '<div class="tvptd__widget-action tvptd__widget-action--select">';
		$organizationOverview .= '<select>';
		$organizationOverview .= '<option>'.__('All Time', 'tvp-trello-dashbaord').'</option>';
		$organizationOverview .= '<option>'.__('This Month', 'tvp-trello-dashbaord').'</option>';
		$organizationOverview .= '</select>';
		$organizationOverview .= '<span class="tvptd__widget-action-arrow"></span>';
		$organizationOverview .= '</div>';
		$organizationOverview .= '</div>';
		$organizationOverview .= '</header>'; // .widget__head

		$organizationOverview .= '<main class="tvptd__widget-content tvptd__widget-content--loading">';
		$organizationOverview .= '<div class="tvptd__spinner spinner"></div>';
		$organizationOverview .= '</main>'; // .tvptd__widget-content

		$organizationOverview .= '</div>'; // .tvptd__widget

		return $organizationOverview;
	}

	public function getOrganizationStatistics()
	{
		$organizationStatistics = '<div class="tvptd__widget tvptd__widget--organization-overview" id="tvptd-organization-statistics">';
		$organizationStatistics .= '<header class="tvptd__widget-head">';
		$organizationStatistics .= '<h3 class="tvptd__widget-title">'.__('Organization Statistics', 'tvp-trello-dashbaord').'</h3>';
		$organizationStatistics .= '<div class="tvptd__widget-actions">';
		$organizationStatistics .= '<div class="tvptd__widget-action tvptd__widget-action--select">';
		$organizationStatistics .= '<select>';
		$organizationStatistics .= '<option>'.__('Last 7 Days', 'tvp-trello-dashbaord').'</option>';
		$organizationStatistics .= '<option>'.__('This Month', 'tvp-trello-dashbaord').'</option>';
		$organizationStatistics .= '</select>';
		$organizationStatistics .= '<span class="tvptd__widget-action-arrow"></span>';
		$organizationStatistics .= '</div>';
		$organizationStatistics .= '</div>';
		$organizationStatistics .= '</header>'; // .widget__head

		$organizationStatistics .= '<main class="tvptd__widget-content tvptd__widget-content--loading">';
		$organizationStatistics .= '<div class="tvptd__spinner spinner"></div>';
		$organizationStatistics .= '</main>'; // .tvptd__widget-content

		$organizationStatistics .= '</div>'; // .tvptd__widget

		return $organizationStatistics;
	}

	public function getBoardList()
	{
		$content = '<div class="tvptd__widget tvptd__widget--help-needed" id="tvptd-board-list">';

		$content .= '<header class="tvptd__widget-head">';
		$content .= '<h3 class="tvptd__widget-title">'.__('Our Teams', 'tvp-trello-dashbaord').'</h3>';
		$content .= '</header>'; // .widget__head

		$content .= '<main class="tvptd__widget-content">';
		$content .= '<section class="tvptd__widget-section">';

		$boards = TVP_TD()->API->Action->getBoards();
		if (empty($boards)) {
			$content .= '<p class="tvptd__widget-message tvptd__widget-message--empty">';
			$content .= __('No Boards found', 'tvp-trello-dashboard');
			$content .= '</p>';
		} else {
			$content .= '<ul class="tvptd__widget-list tvptd__widget-list--boards">';
			foreach ($boards as $key => $board) {
				$content .= '<li class="tvptd__widget-list-item">';
				$content .= '<a class="tvptd__widget-list-item-permalink" href="' . $board['url'] . '" title="' . __('Visit Board', 'tvp-trello-dashboard') . '" target="_blank">';
				$content .= $board['name'];
				$content .= '</a>';
				$content .= '</li>';
			}
			$content .= '</ul>';
		}

		$content .= '</section>'; // .tvptd__widget-section
		$content .= '</main>'; // .tvptd__widget-content

		$content .= '</div>'; // .tvptd__widget

		return $content;
	}
}
