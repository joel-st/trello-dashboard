<?php

namespace TVP\TrelloDashboard\Public;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Hub
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
		add_action('admin_init', [$this, 'disableEditor']);
		add_action('admin_notices', [$this, 'editPageNotification']);
		add_action('the_content', [$this, 'printDashboard']);
	}

	public function printDashboard($content)
	{
		$dashboardPage = get_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-dashboard-page', 'options');

		if (!$dashboardPage) {
			return;
		}

		if ($dashboardPage->ID === get_post()->ID) {
			$content = $this->getDashboardContent();
		}

		return $content;
	}

	public function disableEditor()
	{
		if (isset($_GET['post']) || isset($_GET['post_ID'])) {
			$postId = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
			$dashboardPage = get_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-dashboard-page', 'options');

			if (!isset($postId) || !$dashboardPage) {
				return;
			}

			if ($postId == $dashboardPage->ID) {
				remove_post_type_support('page', 'editor');
			}
		}
	}

	public function editPageNotification()
	{
		if (isset($_GET['post']) || isset($_GET['post_ID'])) {
			$postId = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
			$dashboardPage = get_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-dashboard-page', 'options');

			if (!isset($postId) || !$dashboardPage) {
				return;
			}

			if ($postId == $dashboardPage->ID) {
				$class    = 'notice notice-warning';
				$infotext = sprintf(
					__('This is the page for the TVP Trello Dashbaord. Editing is disbaled. Edit the content on %1$s.', 'sha'),
					'<a href="'. get_admin_url(get_current_blog_id(), 'admin.php?page=' . TVP_TD()->Admin->OptionPages->slugDashboardManager) .'">'.__('its dedicated plugin options page', 'tvp-trello-dashboard').'</a>'
				);
				printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $infotext);
			}
		}
	}

	public function getDashboardContent()
	{
		$content = '<div id="tvp-td" class="tvp-td">';

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

		$content .= '</div>'; // .tvp-td

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
}
