<?php

namespace TVP\TrelloDashboard\Public;

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

		if ($this->isDashboard()) {
			header('HTTP/1.1 200 OK');
			header('Content-Type: text/html; charset=utf-8');

			echo $this->getHeader();

			echo '<body class="tvp-td" id="tvp-td-loading">';
			echo '<div class="tvp-td__dashboard tvp-td__dashboard--loading">';
			echo '<div class="tvp-td__spinner spinner"></div>';
			echo '</div>'; // .tvp-td__dashboard
			echo $this->getFooter();
			echo '</body>';

			exit;
		}
	}

	public function getDashboardContent()
	{
		$content = '<div class="tvp-td__dashboard">';

		$content .= '<header class="tvp-td__header">';
		$content .= $this->getBrand();
		$content .= $this->getUserProfile();
		$content .= '</header>'; // .tvp-td__header

		$content .= '<div class="tvp-td__pre-content">';
		$content .= get_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-dashboard-pre-content', 'options');
		$content .= '</div>'; // .tvp-td__pre-content

		$content .= '<div class="tvp-td__content">';

		$content .= '<div class="tvp-td__aside">';

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

		$content .= '</div>'; // .tvp-td__aside

		$content .= '<div class="tvp-td__main">';
		$content .= 'main';
		$content .= '</div>'; // .tvp-td__main

		$content .= '</div>'; // .tvp-td__content

		$content .= '</div>'; // .tvp-td__dashboard

		return $content;
	}

	public function getLatestNews()
	{
		$content = '<div class="tvp-td__widget tvp-td__widget--latest-news">';
		$content .= '<h4>'.__('Latest News', 'tvp-trello-dashboard').'</h4>';

		$content .= '<ul class="tvp-td__list tvp-td__list--latest-news">';

		while (have_rows(TVP_TD()->Options->DashboardManager->optionPrefix . '-latest-news', 'options')) {
			the_row();

			$link = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-latest-news-link');
			$label = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-latest-news-label');

			$content .= '<li class="tvp-td__list-item">';
			$content .= '<a class="tvp-td__list-item-permalink" href="'.$link.'" target="_blank">' . $label . '</a>';
			$content .= '</li>';
		}

		$content .= '</ul>';

		$content .= '</div>'; // .tvp-td__widget--latest-news

		return $content;
	}

	public function getUsefulInformation()
	{
		$content = '<div class="tvp-td__widget tvp-td__widget--useful-information">';
		$content .= '<h4>'.__('Useful Information', 'tvp-trello-dashboard').'</h4>';

		$content .= '<ul class="tvp-td__list tvp-td__list--useful-information">';

		while (have_rows(TVP_TD()->Options->DashboardManager->optionPrefix . '-useful-information', 'options')) {
			the_row();

			$link = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-useful-information-link');
			$label = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-useful-information-label');

			$content .= '<li class="tvp-td__list-item">';
			$content .= '<a class="tvp-td__list-item-permalink" href="'.$link.'" target="_blank">' . $label . '</a>';
			$content .= '</li>';
		}

		$content .= '</ul>';

		$content .= '</div>'; // .tvp-td__widget--useful-information

		return $content;
	}

	public function getVolunteerResources()
	{
		$content = '<div class="tvp-td__widget tvp-td__widget--volunteer-resources">';
		$content .= '<h4>'.__('Volunteer Resources', 'tvp-trello-dashboard').'</h4>';

		$content .= '<ul class="tvp-td__list tvp-td__list--volunteer-resources">';

		while (have_rows(TVP_TD()->Options->DashboardManager->optionPrefix . '-volunteer-resources', 'options')) {
			the_row();

			$link = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-volunteer-resources-link');
			$label = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-volunteer-resources-label');

			$content .= '<li class="tvp-td__list-item">';
			$content .= '<a class="tvp-td__list-item-permalink" href="'.$link.'" target="_blank">' . $label . '</a>';
			$content .= '</li>';
		}

		$content .= '</ul>';

		$content .= '</div>'; // .tvp-td__widget--volunteer-resources

		return $content;
	}

	public function getHelpNeeded()
	{
		$content = '<div class="tvp-td__widget tvp-td__widget--help-needed">';
		$content .= '<h4>'.__('Help Needed', 'tvp-trello-dashboard').'</h4>';

		$content .= '<ul class="tvp-td__list tvp-td__list--help-needed">';

		while (have_rows(TVP_TD()->Options->DashboardManager->optionPrefix . '-help-needed', 'options')) {
			the_row();

			$link = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-help-needed-link');
			$label = get_sub_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-help-needed-label');

			$content .= '<li class="tvp-td__list-item">';
			$content .= '<a class="tvp-td__list-item-permalink" href="'.$link.'" target="_blank">' . $label . '</a>';
			$content .= '</li>';
		}

		$content .= '</ul>';

		$content .= '</div>'; // .tvp-td__widget--help-needed

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

		$profile = '<div class="tvp-td__user-profile">';

		$profile .= '<div class="tvp-td__user-avatar">';
		$profile .= get_avatar($currentUser->get('ID'));
		$profile .= '</div>'; // .tvp-td__user-avatar

		$profile .= '<h4 class="tvp-td__user-name">' . $currentUser->get('user_nicename') . '</h4>';
		$profile .= '<ul class="tvp-td__user-actions">';
		$profile .= '<li class="tvp-td__user-action">';
		$profile .= '<a href="' . get_edit_user_link($currentUser->get('ID')) . '">' . _x('Edit Profile', 'Dashboard profile edit action', 'tvp-trello-dashboard') . '</a>';
		$profile .= '</li>';
		$profile .= '<li class="tvp-td__user-action">';
		$profile .= '<button class="button button--link" id="' . TVP_TD()->prefix . '-logout' . '">' . _x('Logout', 'Dashboard logout action', 'tvp-trello-dashboard') . '</button>';
		$profile .= '</li>';
		$profile .= '</ul>'; // .tvp-td__user-actions


		$profile .= '</div>'; // .tvp-td__user-profile

		return $profile;
	}

	public function getBrand()
	{
		$profile = 'logo';

		return $profile;
	}
}
