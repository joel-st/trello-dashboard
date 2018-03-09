<?php
		include($_SERVER['DOCUMENT_ROOT']. '/config/credentials.php?rebuild=1'); 
   
		//if the master Json file is lost or deleted, use this script to re build the lost data
		
		// Master JSON location
		$MASTEROBJLOC = $_SERVER['DOCUMENT_ROOT'] . '/cache/MonthViewJson.json';
		$MASTEROBJs =  file_get_contents($MASTEROBJLOC);
		$MASTEROBJ = json_decode($MASTEROBJs, true);

		$MASTERactionsJsonLoc = $_SERVER['DOCUMENT_ROOT'] . '/cache/OrganisationActions.json';
		$MASTERactionsJson =  file_get_contents($MASTERactionsJsonLoc);
		$MASTERactionsOBJ = json_decode($MASTERactionsJson, true);


		$GetOrganisationActions =  file_get_contents('https://api.trello.com/1/organizations/thevenusproject1/boards?key='.$APIkey.'&token='.$token.'&limit=999&filter=open'); 
		$GetOrganisationActionsResult = json_decode($GetOrganisationActions, true);

		// show how many users in atleaset 1 board
		if($_GET['tetusersinboards'] == "www"){
		function stasfrommems( $MASTEROBJ){ 
		foreach( $MASTEROBJ as $val){
		foreach ($val['admembertoboard'] as $vall){
		foreach ($vall['members'] as $valll){
		$inatleastoneboard[$valll['username']] = $valll['username'];
		}			 
		}
		}
		return $inatleastoneboard;
		}
		echo  count (stasfrommems( $MASTEROBJ));
		exit();
		}
		
		// Only show the Json files 
		if($_GET['show'] == "1"){
		print_r( $MASTEROBJ);
		echo "=================================<br/>================================<br/>===================================";
		print_r($MASTERactionsOBJ);
		exit();
		}

		$boardrefreshlinks = "<h2>This tool is only needed if there is; <br/>A: more than 1000 new entries not in the master JSON, in any end point (ie board/id/actions)  since the laste refresh. <br/>B: If the MAster JSON is lost of corrupt<br/>C: you have a board with more than 1000 actions in that was private but now isn't</h2><br/><br/>";
		$boardrefreshlinks .= "<h3>If you wish to update actions please choose a board.</h3><br/>";
		// get the next 1000 results link
		foreach ($GetOrganisationActionsResult as $board){
		$boardslist[$board['id']]['name'] = $board['name'];
		$boardslist[$board['id']]['shortLink'] = $board['shortLink'];
		if($board['shortLink'] != $_GET['id']){	
		$boardrefreshlinks .= '- <a href = "?id='.$board['shortLink'].'" title= "" >'.$board['name'].'</a> ';
		}else{
		$boardnamee = $board['name'];
		$boardrefreshlinks .= '- <b><a href = "?id='.$board['shortLink'].'" title= "" >'.$board['name'].'</a></b>';
		}
		}

		if($_GET['id'] !=""){
		if($_GET['mongotimestamp'] != ""){
		$mongotimestamp = mongoDate($_GET['mongotimestamp']);
		$urlvars = "&before=".$_GET['mongotimestamp'];
		}

		//create all the actions
		$addMemberToBoardURL = file_get_contents('https://api.trello.com/1/boards/'.$_GET['id'].'/actions?key='.$APIkey.'&token='.$token.'&limit=999'.$urlvars);
		$addMemberToBoardURLactions = json_decode($addMemberToBoardURL, true);

        $ia = 0;
		foreach ($addMemberToBoardURLactions as $valuea){
		$dateparse = parsedate($valuea['date']);
		// format - memberid - actions - boardID - arr
		if(!isset($MASTERactionsOBJ[$valuea['memberCreator']['id']]['actions'][$valuea['id']]['type'])){
		$ia++;	
		$MASTERactionsOBJ[$valuea['memberCreator']['id']]['actions'][$valuea['id']]['type'] = $valuea['type'];	
		$MASTERactionsOBJ[$valuea['memberCreator']['id']]['actions'][$valuea['id']]['timestamp'] = $dateparse['timestamp'];	
		$MASTERactionsOBJ[$valuea['memberCreator']['id']]['actions'][$valuea['id']]['date'] = $valuea['date'];
		$MASTERactionsOBJ[$valuea['memberCreator']['id']]['actions'][$valuea['id']]['boardID'] = $valuea['data']['board']['id'];			
		$MASTERactionsOBJ[$valuea['memberCreator']['id']]['actions'][$valuea['id']]['boardname'] = $valuea['data']['board']['name'];			
		$MASTERactionsOBJ[$valuea['memberCreator']['id']]['actions'][$valuea['id']]['shortLink'] = $valuea['data']['board']['shortLink'];			
		$MASTERactionsOBJ[$valuea['memberCreator']['id']]['actions'][$valuea['id']]['fullName'] = $valuea['memberCreator']['fullName'];	
		$MASTERactionsOBJ[$valuea['memberCreator']['id']]['actions'][$valuea['id']]['username'] = $valuea['memberCreator']['username'];	
		$MASTERactionsOBJ[$valuea['memberCreator']['id']]['actions'][$valuea['id']]['memberID'] = $valuea['memberCreator']['id'];	
		}
		}


		writeJsontoDisk($MASTERactionsOBJ, $MASTERactionsJsonLoc);

		if($ia == ""){ $ia=0; }
		echo  $boardrefreshlinks;
		echo "<br/><br/> <h1>You have just built the master board actions jsons for the board - ".$boardnamee.'</h1>';
		echo $ia." actions added<br/>";

		if($dateparse['date']){
		echo "The last date of the results was ".$dateparse['date']." <a href=\"?id=".$_GET['id']."&mongotimestamp=".$valuea['id']."\" title=\"\" >Get Next 1000 results!</a>";
		}else{
		foreach($MASTERactionsOBJ as $val){
		foreach($val as $valb){
		foreach($valb as $valbc){
		if($valbc['shortLink'] == $_GET['id']){
		$totalactionsofthisboard++ ;
		} 
		}
		}
		}
		echo "There are now -".$totalactionsofthisboard." totoal actions in this board - Now move on to the next board";
		}
		print_r($ouptubJSONadded);
		echo " <br/><br/><a href=\"?show=1&type=MASTERactionsOBJ\" title=\"\" >Show the master JSON MASTERactionsJJSON (all actions ever created on any board. Listed by the member who created the action)</a>";
		echo "<br/> <br/><a href=\"?show=1&type=MASTEROBJ\" title=\"\" >Show the master JSON MASTERJSON (all members added to team and all members added to a board, listed in a month by month breakdown)</a>";
		
		}else{
		echo  $boardrefreshlinks.'<br/></br>';
		echo "<h3>ONLY if you have lost the master JSON files or they are corrupt. (this will do nothing if the data is there already)</h3><br/>";
		
		echo '<a href="?membersinteam=1" title="">Create member added to team json</a>';
		}

		

		if($_GET['membersinteam'] == "1" && $_GET['id'] == ""){
		if($_GET['membersinteammongotimestamp'] != ""){
		$mongotimestamp = mongoDate($_GET['membersinteammongotimestamp']);
		$urlvarsa = "&since=".$_GET['membersinteammongotimestampa'];
		$urlvarsb = "&since=".$_GET['membersinteammongotimestampb'];
		}

		$OrganisationMembershipsURL = file_get_contents('https://api.trello.com/1/organizations/thevenusproject1/memberships?member=true&key='.$APIkey.'&token='.$token.'&limit=999'.$urlvarsb);
		$OrganisationMemberships = json_decode($OrganisationMembershipsURL, true);
		$OrganisationMemberships = array_reverse($OrganisationMemberships);

		$totalmem = 0;
		foreach ($OrganisationMemberships as $value){
		$id =  $value['id'] ;
		$idMember = $value['idMember'];
		$fullName = $value['member']['fullName'];
		$username = $value['member']['username'];
		$dateparse = mongoDate($id);
		if(!isset($MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['memberid'])){
		$totalmem++;
		$MASTEROBJ[$dateparse['dateID']]['month'] = $dateparse['monthstr'];
		$MASTEROBJ[$dateparse['dateID']]['year'] = $dateparse['year'];
		$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['memberid'] = $idMember;
		$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['username'] = $username;
		$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['fullName'] = $fullName;
		$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['mongoid'] = $id;
		$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['Addedday'] = $dateparse['day'];
		$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['Addedmonthstr'] = $dateparse['monthstr'];
		$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['Addedmonth'] = $dateparse['month'];
		$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['Addedyear'] = $dateparse['year'];
		$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['Addeddate'] = $dateparse['date'];
		$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['timestamp'] = $dateparse['timestamp'];
		}
		} 



		// get all the board memberships

		$Getboards =  file_get_contents('https://api.trello.com/1/organizations/thevenusproject1/boards?key='.$APIkey.'&token='.$token.'&limit=999&filter=open'); 
		$GetboardsResult = json_decode($Getboards, true);

		foreach($GetboardsResult as $valboard){

		$boardMembershipsURL = file_get_contents('https://api.trello.com/1/boards/'.$valboard['shortLink'].'/memberships?member=true&key='.$APIkey.'&token='.$token.'&limit=999'.$urlvars);
		$boardMembershipsURLONJ = json_decode($boardMembershipsURL, true);


		//print_R($MASTEROBJ);
		foreach($boardMembershipsURLONJ as $valuea){
		$dateparse = mongoDate($valuea['id']);

		if(isset($valuea['idMember'])){
		if(!isset($MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['members'][$valuea['idMember']]['memberName'])){
		$i++;       

		$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['boardShortLink'] = $valboard['shortlink'];
		$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['boardID'] = $valboard['id'];
		$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['boardName'] = $valboard['name'];	
		$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['members'][$valuea['idMember']]['memberName'] = $valuea['member']['fullName'];
		$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['members'][$valuea['idMember']]['memberID'] = $valuea['memberID'];
		$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['members'][$valuea['idMember']]['memberType'] =  $valuea['memberType'];
		$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['members'][$valuea['idMember']]['username'] = $valuea['member']['username'];
		$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['members'][$valuea['idMember']]['dateaddedtoboard'] = $dateparse['date'];
		$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['members'][$valuea['idMember']]['timestamp'] = $dateparse['timestamp'];

		$ouptubJSONadded[$i]['name'] =  $MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['members'][$valuea['idMember']]['memberName'].'<br/>';
		$ouptubJSONadded[$i]['boardShortLink'] =  $MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['boardShortLink'];



		}

		} 
		}
		}

		writeJsontoDisk($MASTEROBJ, $MASTEROBJLOC); 


		echo '<br/><br/> <h1>You have just updated the master JSON members list</h1>';
		if($dateparse['timestamp']){
		echo "The last date of the results were <a href=\"?membersinteam=1&mongotimestampb=".$id."&mongotimestampa=".$MemberId."\" title=\"\" >".$dateparse['date']."</a>";
		}else{
		echo "Board Results completed - move on to the next board";
		}
		echo " <a href=\"?show=1\" title=\"\" >Show the master JSONs</a>";


		}

		function parsedate($date){
		$dateparse = [];
		$datea = explode('T',$date);
		$dateparts = explode( '-',$datea[0]); 
		$dater = $dateparts[2].'/'.$dateparts[1].'/'.$dateparts[0];
		$dateObj   = DateTime::createFromFormat('!m', $dateparts[1]);
		$monthstr = $dateObj->format('F');
		$dateparse['dateID'] =  "ID".$dateparts[0].$monthstr;
		$dateparse['year'] = $dateparts[0];
		$dateparse['day'] = 
		$dateparse['monthstr'] = $monthstr;
		$dateparse['date'] = $dater;
		$dateparse['timestamp'] = strtotime($dateparts[2].'-'.$dateparts[1].'-'.$dateparts[0]);
		return $dateparse;
		}


		function mongoDate($id) {
		$mongoDateparse = [];
		$timestamp = intval(substr($id, 0, 8), 16);
		$dateObj = (new DateTime())->setTimestamp($timestamp);
		$dater = $dateObj->format('d/m/Y');
		$dateparts = explode('/',$dater);
		$month = str_replace("0", "", $dateparts[1]);
		$dateObj   = DateTime::createFromFormat('!m', $month);
		$monthstr = $dateObj->format('F');
		$mongoDateparse['dateID'] =  "ID".$dateparts[2].$monthstr;
		$mongoDateparse['year'] = $dateparts[2];
		$mongoDateparse['monthstr'] = $monthstr;
		$mongoDateparse['month'] = $month;
		$mongoDateparse['day'] = $dateparts[0];
		$mongoDateparse['date'] = $dater;
		$mongoDateparse['timestamp'] = $timestamp;
		return $mongoDateparse;
		}

		function writeJsontoDisk($json, $jsonLOC){
		$fp = fopen($jsonLOC, 'w');
		fwrite($fp, json_encode($json));
		fclose($fp);
		}

		function parseMASTERJson($JsonLoc){
		// check to see if OrganisationActions object exists
		$json = @ file_get_contents($JsonLoc); 
		if($json === FALSE) { 
		// no JSON found - create new one
		touch($JsonLoc);
		$json = [];
		}else{
		if($json != ""){
		$json = json_decode($json, true);	
		}else{
		$json = [];
		} 
		}
		return $json;
		} 
		exit();

		?>