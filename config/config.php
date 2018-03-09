<?php
	include($_SERVER['DOCUMENT_ROOT']. '/config/credentials.php'); 
   	
	$OuthURL = "https://trello.com/1/authorize?return_url=https://dashboard.thevenusproject.com/includes/parseToken.php&expiration=never&name=The%20Venus%20Project%20&scope=read&response_type=token&callback_method=fragment&key=$APIkey";

	$drt = $_SERVER['DOCUMENT_ROOT'] ;
	
	$MASTERJsonLoc = $drt. '/cache/MonthViewJson.json';
	$MASTERactionsJsonLoc = $drt . '/cache/OrganisationActions.json';

	// trello board list url
	$organisationBoardsURL = 'https://api.trello.com/1/organizations/thevenusproject1/boards?key='.$APIkey.'&token='.$token.'&limit=1000&filter=open';

	//  trello organization memberships 
	$OrganisationMembershipsURL = 'https://api.trello.com/1/organizations/thevenusproject1/memberships?filter=all&member=true&key='.$APIkey.'&token='.$token.'&limit=1000';

	
	$ClassLoc = $drt. '/includes/functions.php';
	$layLoc = $drt. '/includes/lay.php'; 
	$cachefile = $drt. '/cache/DashBoard.html';  
	include($layLoc); 
	include($ClassLoc); 


	$APItoken = $_GET['token'];
?>
