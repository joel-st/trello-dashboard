<?php

namespace TVP\TrelloDashboard\View;

// Security
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class NotInOrganization
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
		$this->prefix = TVP_TD()->prefix . '-not-in-organization';
	}

	/**
	 * Initalization
	 * Checkout the hooks and actions to understand how this class initializes itself.
	 */
	public function run()
	{
	}

	/**
	 * Get the not in organization content, if a trello member tries to login and is not a member of the specified trello organization
	 */
	public function getNotInOrganizationContent()
	{
		$notInOrganization = '<div class="tvptd__dashboard tvptd__dashboard--not-in-organization">';

		$notInOrganization .= '<header class="tvptd__header">';
		$notInOrganization .= get_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-not-in-organization-pre-content', 'options');
		$notInOrganization .= '</header>'; // .tvptd__header

		$notInOrganization .= '<div class="tvptd__content">';
		$notInOrganization .= '<div class="tvptd__main">';
		$notInOrganization .= '<a href="https://www.thevenusproject.com/become-a-volunteer/" target="blank" class="button button--large">'._x('Get Involved Today', 'Trello Dashboard become a volunteer action', 'tvp-trello-dashboard').'</a>';
		$notInOrganization .= '</div>'; // .tvptd__main
		$notInOrganization .= '</div>'; // .tvptd__content

		$notInOrganization .= '</div>'; // .tvp-tvptd__dashboard

		if ($background = get_field(TVP_TD()->Options->DashboardManager->optionPrefix . '-not-in-organization-background', 'options')) {
			$backgroundUrl = $background['url'];
			$backgroundStyle = 'style="background-image: url('.$backgroundUrl.')"';
			$notInOrganization .= '<div class="tvptd__background" ' . $backgroundStyle . '></div>';
		}

		return $notInOrganization;
	}
}
