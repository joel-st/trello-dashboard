<?
	class trelloDash{

	//private functions

	private function CollectResults($trelloURL){	 
	$ListResult = @ file_get_contents($trelloURL); 
	if($ListResult === FALSE) { 
	return false;
	}
	$newJson = json_decode($ListResult, true);
	return $newJson;	 
	}

	private function BoardMemberResults($boardID){	 
	global $APIkey;
	global $token;
	if(!$boardID){
	return false;
	}else{
	$trelloURL = 'https://api.trello.com/1/boards/'.$boardID.'/memberships?limit=1000&key='.$APIkey.'&token='.$token;
	$ListResult = @ file_get_contents($trelloURL); 
	if($ListResult === FALSE) { 
	return false;
	}
	$newJson = json_decode($ListResult, true);
	return $newJson;	 
	}
	}

	private function parsedate($date){
	$dateparse = [];
	$datea = explode('T',$date);
	$dateparts = explode( '-',$datea[0]); 
	$dater = $dateparts[2].'/'.$dateparts[1].'/'.$dateparts[0];
	$dateObj   = DateTime::createFromFormat('!m', $dateparts[1]);
	$monthstr = $dateObj->format('F');
	$dateparse['dateID'] =  "ID".$dateparts[0].$monthstr;
	$dateparse['year'] = $dateparts[0];
	$dateparse['day'] = $dateparts[2];
	$dateparse['monthstr'] = $monthstr;
	$dateparse['date'] = $dater;
	$dateparse['timestamp'] = strtotime($dateparts[2].'-'.$dateparts[1].'-'.$dateparts[0]);
	return $dateparse;
	}

	private function mongoDate($id) {
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

	private function parseMASTERJson($JsonLoc){
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
	return array_reverse($json);
	} 



	// get the stats for how many actions a user has made
	private function stasformemactions($arrayofmems, $MASTERactionsOBJ){ 
	$totalmems = count($arrayofmems);
	$totatlactionsforthisuser = 0;
	$actioncount = [];
	$actioncount['none']['count'] = 0;
	$actioncount['one']['count'] = 0;
	$actioncount['three']['count'] = 0;
	$actioncount['five']['count'] = 0;
	$actioncount['between2n3']['count'] = 0;
	$actioncount['ten']['count'] = 0;
	$actioncount['twenty']['count'] = 0;
	foreach($arrayofmems as $keyi => $val){
	$totatlactionsforthisuser = count($MASTERactionsOBJ[$keyi]['actions']);
		if($totatlactionsforthisuser == 0){
		$actioncount['none']['count']++;	
		}
		if($totatlactionsforthisuser >= 1){
		$actioncount['one']['count']++;
		}
		if($totatlactionsforthisuser >= 3){
		$actioncount['three']['count']++;
		}
		if($totatlactionsforthisuser >= 5){
		$actioncount['five']['count']++;
		}
		if($totatlactionsforthisuser >= 10){
		$actioncount['ten']['count']++;
		}
		if($totatlactionsforthisuser >= 20){
		$actioncount['twenty']['count']++;
		}
	}
	foreach($actioncount as $key => $val){
	$actioncount[$key]['pct'] = trelloDash::getpct($totalmems, $actioncount[$key]['count']);
	}
	return $actioncount;
	}


	// now get the stats for how many joined a board this month
	private function stasfrommems($arrayofmems, $boardjoincomparison){ 
	$totalmems = count($arrayofmems);
	$howmany = [];
	$howmany['noboardsjoined']['count'] = 0;
	$howmany['within1']['count'] = 0;
	$howmany['between1n2']['count'] = 0;
	$howmany['between3n4']['count'] = 0;
	$howmany['between2n3']['count'] = 0;
	$howmany['fourormore']['count'] = 0;

	foreach($arrayofmems as $keyi => $val){
	$timeJT = $boardjoincomparison[$keyi]['timestampjoinedteam'];
	$timeJB = $boardjoincomparison[$keyi]['boardjoinedtimestamp'];
	$boardname = $boardjoincomparison[$keyi]['boardname'];
	$memfullName = $boardjoincomparison[$keyi]['fullName'];
	$joinedteamplus7 = strtotime('+7 days', $timeJT);
	$joinedteamplus14 = strtotime('+14 days', $timeJT);
	$joinedteamplus21 = strtotime('+21 days', $timeJT);
	$joinedteamplus28 = strtotime('+28 days', $timeJT);

		if($timeJB == ""){
		$howmany['noboardsjoined']['count']++;
		}
		if($timeJB < $joinedteamplus7 && $timeJB !=""){
		$howmany['within1']['count']++;
		$howmany['totalmemberswhojoined1board']++;
		}	
		if($timeJB < $joinedteamplus14 && $timeJB >= $joinedteamplus7 && $timeJB !=""){	   
		$howmany['between1n2']['count']++ ;
		$howmany['totalmemberswhojoined1board']++;
		}
		if($timeJB < $joinedteamplus21 && $timeJB >= $joinedteamplus14 && $timeJB !=""){	   
		$howmany['between2n3']['count']++ ;
		$howmany['totalmemberswhojoined1board']++;
		}
		if($timeJB <= $joinedteamplus28 && $timeJB >= $joinedteamplus21 && $timeJB !=""){
		$howmany['between3n4']['count']++;
		$howmany['totalmemberswhojoined1board']++;
		}
		if($timeJB > $joinedteamplus28 && $timeJB !=""){
		$howmany['totalmemberswhojoined1board']++;
		$howmany['fourormore']['count']++;	
		}
	
	}
	
	foreach($howmany as $key => $val){ 
	$howmany[$key]['pct'] = trelloDash::getpct($totalmems, $howmany[$key]['count'] );	
	}
	
	return $howmany;
	}
	// get percentage func
	function getpct($total, $val){
	return round(($val / $total * 100));
	}
	function writeJsontoDisk($MASTEROBJ, $jsonLOC){
	$fp = fopen($jsonLOC, 'w');
	fwrite($fp, json_encode($MASTEROBJ ,true));
	fclose($fp);
	}

	/// public functions
	public function cache(){
	global $cachefile;
	$cached = @ file_get_contents($cachefile);
	return $cached;
	}


	public function outputView($viewName){
	global $APIkey;
	global $token;
	global $MASTERactionsOBJ;
	global $MASTEROBJ;
	global $countentriesperboard;
	global $organisationBoardsURL;
	global $organisationMembersURL;
	global $OrganisationMembershipsURL;
	global $MASTERJsonLoc;
	global $MASTERactionsJsonLoc;
	global $isCron;

	if($viewName == 'DashMain'){

	// get the organisation actions  
	$OrganisationMembershipsResult = trelloDash::CollectResults($OrganisationMembershipsURL);
	$OrganisationMembershipsResultCount = count ($OrganisationMembershipsResult);

	// get the total boards  
	$organisationBoardsResult = trelloDash::CollectResults($organisationBoardsURL);
	$organisationBoardsResultCount = count ($organisationBoardsResult);

	// parse master json files
	$MASTEROBJ = trelloDash::parseMASTERJson($MASTERJsonLoc);
	$MASTERactionsOBJ = trelloDash::parseMASTERJson($MASTERactionsJsonLoc);
	 
	// Each time the data is refreshed, it will modify the Master Json file to add anything new and keep all the existing data. 

	// iterate through the new organization/memberships add new memberships to masterJSON
		foreach ($OrganisationMembershipsResult as $value){
	$id =  $value['id'] ;
	$dateparse = trelloDash::mongoDate($id);
	if(!isset($MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['memberid'])){
	$MASTEROBJ[$dateparse['dateID']]['month'] = $dateparse['monthstr'];
	$MASTEROBJ[$dateparse['dateID']]['year'] = $dateparse['year'];
	$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['memberid'] =  $value['idMember'];
	$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['username'] = $value['member']['username'];
	$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['fullName'] = $value['member']['fullName'];
	$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['mongoid'] = $id;
	$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['Addedday'] = $dateparse['day'];
	$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['Addedmonthstr'] = $dateparse['monthstr'];
	$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['Addedmonth'] = $dateparse['month'];
	$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['Addedyear'] = $dateparse['year'];
	$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['Addeddate'] = $dateparse['date'];
	$MASTEROBJ[$dateparse['dateID']]['meberAddedtoTeam'][$id]['timestamp'] = $dateparse['timestamp'];
	}
		}
		
	// iterate through the new organization/boards and add the new board memberships to masterJSON
		
	// get all the board memberships

	foreach($organisationBoardsResult as $valboard){

		foreach($valboard['memberships'] as $valuea){

			$dateparse = trelloDash::mongoDate($valuea['id']);

			if(isset($valuea['idMember'])){
				if(!isset($MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['members'][$valuea['idMember']]['memberName'])){

					$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['boardShortLink'] = $valboard['shortlink'];
					$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['boardID'] = $valboard['id'];
					$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['boardName'] = $valboard['name'];	
					$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['members'][$valuea['idMember']]['memberName'] = $valuea['fullName'];
					$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['members'][$valuea['idMember']]['memberID'] = $valuea['memberID'];
					$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['members'][$valuea['idMember']]['memberType'] =  $valuea['memberType'];
					$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['members'][$valuea['idMember']]['username'] = $valuea['username'];
					$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['members'][$valuea['idMember']]['dateaddedtoboard'] = $dateparse['date'];
					$MASTEROBJ[$dateparse['dateID']]['admembertoboard'][$valboard['id']]['members'][$valuea['idMember']]['timestamp'] = $dateparse['timestamp'];


				}

			} 
		}

	//create all the actions
	$addMemberToBoardURL = file_get_contents('https://api.trello.com/1/boards/'.$valboard['shortLink'].'/actions?key='.$APIkey.'&token='.$token.'&limit=999'.$urlvars);
	$addMemberToBoardURLactions = json_decode($addMemberToBoardURL, true);

		// get the 1000 most recent actions for this board
		foreach ($addMemberToBoardURLactions as $valuea){

		$dateparse = trelloDash::parsedate($valuea['date']);


			// format - memberid - actions - boardID - arr
			if(!isset($MASTERactionsOBJ[$valuea['memberCreator']['id']]['actions'][$valuea['id']]['type'])){
	
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
			
			//make the breakdown of actions per team per person
			 $usernameByActionByTeam[$valuea['memberCreator']['id']][$valuea['data']['board']['id']][$valuea['id']]['ActionType'] = $valuea['type'];
			 $usernameByActionByTeam[$valuea['memberCreator']['id']][$valuea['data']['board']['id']][$valuea['id']]['BoardName'] = $valuea['data']['board']['name'];
			 $usernameByActionByTeam[$valuea['memberCreator']['id']][$valuea['data']['board']['id']][$valuea['id']]['UserName'] = $valuea['memberCreator']['username'];			 
		 
		}

	// get the 1000 most recent memberships for this board

	}
 
	//write the updated json to disk 
	$MASTEROBJ = array_reverse($MASTEROBJ);
	trelloDash::writeJsontoDisk($MASTEROBJ, $MASTERJsonLoc);
	trelloDash::writeJsontoDisk($MASTERactionsOBJ, $MASTERactionsJsonLoc);
	///// completed undate new data to existing saved data (last 999 memberships returned from trello)
	// create additional comparison data
	foreach ($MASTEROBJ as $val){	
	foreach($val['meberAddedtoTeam'] as $valb){
		 
	$boardjoincomparison[$valb['memberid']]['fullName'] = $valb['fullName'];	
	$boardjoincomparison[$valb['memberid']]['memid'] = $valb['memberid'];	
	$boardjoincomparison[$valb['memberid']]['timestampjoinedteam'] = $valb['timestamp'];
	$addedtoboard['ID'.$val['year'].$val['month']][$valb['memberid']] = '@'.$valb['username'];	
	}
	foreach($val['admembertoboard'] as $valbc){
	foreach($valbc['members'] as $keyp =>  $valbcde){	
	$boardjoincomparison[$keyp]['boardname'] = $valbc['boardName'];			  
	$boardjoincomparison[$keyp]['boardjoinedtimestamp'] = $valb['timestamp'];	
	}
	}
	}


	$actionsperboardpermonth = [];
	foreach ($MASTERactionsOBJ as $value){
	foreach($value['actions'] as $key => $valuea){
	$dateparse = trelloDash::parsedate($valuea['date']);
	$actionsperboardpermonth[$dateparse['dateID']][$valuea['boardID']]['boardactioncount'] = ++$actioncount[$dateparse['dateID']][$valuea['boardID']];
	$actionsperboardpermonth[$dateparse['dateID']][$valuea['boardID']]['boardname'] = $valuea['boardname'];
	$actionsperboardpermonth[$dateparse['dateID']][$valuea['boardID']]['actions'][$key] = $valuea['type'];
	$countentriesperboard[$valuea['boardID']]++;
	$totalactionsperboard[$valuea['boardID']] = ++$totalactioncount[$valuea['boardID']];
	}
	}


	// create the output
	$head = "<h1><b>The Venus Project Trello Dashboard</b></h1>";
	$OrganisationbreakdownOutput = "<h2 id='OrganisationActions'><b>Organisation monthly breakdown</b></h2>"; 
	// create the month by month view 
	foreach ($MASTEROBJ as $keyop => $value){
	$total=0;
	// array of dates by month
	if($value['month']){
	$totalmembersaddedthismonth = count($value['meberAddedtoTeam']);
	
	if($UOC == ""){
    $UOC = 1;
	$OrganisationbreakdownOutput .= '<h3  class="clickme" data-ttext="'.$value['year'].' '.$value['month'].'" id="a'.$value['year'].$value['month'].'">Hide '.$value['year'].' '.$value['month'].' &uarr; </h3>
	<div class=" a'.$value['year'].$value['month'].'">';
	}else{
	$OrganisationbreakdownOutput .= '<h3  class="clickme" data-ttext="'.$value['year'].' '.$value['month'].'" id="a'.$value['year'].$value['month'].'">Show '.$value['year'].' '.$value['month'].' &darr; </h3>
	<div class="boardreveal a'.$value['year'].$value['month'].'">';
	}
	
	
	
	$OrganisationbreakdownOutput .= '<b>Number of people added to the organization in '.$value['month'].' : '.$totalmembersaddedthismonth.'</b><br/>';
	if(is_array($addedtoboard[$keyop])){
	$howmany = trelloDash::stasfrommems($addedtoboard[$keyop], $boardjoincomparison);
	$actionstats = trelloDash::stasformemactions($addedtoboard[$keyop], $MASTERactionsOBJ);
	if(is_array($howmany)){

	$totalmemberswhotookatleast1action += $actionstats['one']['count'] ;
	$totalmemberswhojoined1board += $howmany['totalmemberswhojoined1board'] ;
	
	$total = count($addedtoboard[$keyop]);

	$OrganisationbreakdownOutput .= "Out of the $totalmembersaddedthismonth members added this month, <br/>
	how many joined at least 1 board:<br/>
	- Within 1 week of being added: <b>".$howmany['within1']['count']."(".$howmany['within1']['pct']."%)</b><br/>
	- Between 1 and 2 weeks of being added: <b>".$howmany['between1n2']['count']." (".$howmany['between1n2']['pct']."%)</b> <br/>
	- Between 2 and 3 weeks of being added: <b>".$howmany['between2n3']['count']." (".$howmany['between2n3']['pct']."%)</b> <br/>
	- Between 3 and 4 weeks of being added: <b>".$howmany['between3n4']['count']." (".$howmany['between3n4']['pct']."%)</b> <br/>
	- More than 4 weeks after being added: <b>".$howmany['fourormore']['count']." (".$howmany['fourormore']['pct']."%)</b> <br/>
	- Have never joined a board <b>".$howmany['noboardsjoined']['count']."(".$howmany['noboardsjoined']['pct']."%)</b> <br/>
	<br/>
	Out of the $totalmembersaddedthismonth members added this month, <br/>
	how many people performed actions (for all time):<br/>
	- 0 actions: <b>".$actionstats['none']['count']." (".$actionstats['none']['pct']."%)</b><br/>
	- At least 1 action: <b>".$actionstats['one']['count']." (".$actionstats['one']['pct']."%)</b><br/>
	- At least 3 actions: <b>".$actionstats['three']['count']." (".$actionstats['three']['pct']."%)</b><br/>
	- At least 5 actions: <b>".$actionstats['five']['count']." (".$actionstats['five']['pct']."%)</b><br/>
	- At least 10 actions: <b>".$actionstats['ten']['count']." (".$actionstats['ten']['pct']."%)</b><br/>
	- At least 20 actions: <b>".$actionstats['twenty']['count']." (".$actionstats['twenty']['pct']."%)</b><br/><br/>";	
	$OrganisationbreakdownOutput .= "Number of actions within each board this month:<br/>";
	$boardactioncount="";
	usort($actionsperboardpermonth['ID'.$value['year'].$value['month']], function ($a, $b) {
	return $a['boardactioncount'] < $b['boardactioncount'];
	});
	foreach($actionsperboardpermonth['ID'.$value['year'].$value['month']] as $val){
	$boardactioncount .= $val['boardname']."<b>(".$val['boardactioncount']." actions)</b> <br/>";	
	}
	} 
	}

	$OrganisationbreakdownOutput .= $boardactioncount.'<br/><b>Members who were added to the organization in '.$value['month'].'</b>';
	if(is_array($addedtoboard[$keyop])){ 
	 
	$OrganisationbreakdownOutput .= '<ul>';
		foreach($addedtoboard[$keyop] as $keyu => $valc){
		
		// get the actions per team per user details here
		$OrganisationbreakdownOutput .= '<li><b>'.$valc.'</b>';
		
		foreach($usernameByActionByTeam[$keyu] as  $FEboardID){
			$ActionsOnBoard=0;
			foreach($FEboardID as $TeamID => $FEActionID){
				 $ActionsOnBoard++;
			}
			$OrganisationbreakdownOutput .= '<ul><li style="font-size: 14px;">'.$FEActionID['BoardName'].': <b>'.$ActionsOnBoard.'</b></li></ul>';
		}
		$OrganisationbreakdownOutput .= '</li>';
	}
	$OrganisationbreakdownOutput .= '</ul><hr/></div>';
	}
	
	
	}
	}	


	// create the board output
	foreach ($organisationBoardsResult as  $value){
	if($value['closed'] == ""){
	$boardCount++;
	$boardOutput .=  "<li><b>".$value['name']."</b><ul>";
	if($value['id'] != ""){
	$BoardMemberListURL = trelloDash::BoardMemberResults($value['id']); 
	$BoardMemberListURLCount = count ($BoardMemberListURL);
	}else{
	$BoardMemberListURLCount = "Id not found!";
	}
	$boardOutput .=  "<li><b>".$BoardMemberListURLCount/*.'-'.$totalress[$boardname]*/."</b> members in board</li>";
	$boardOutput .=  "<li><b>".$totalactionsperboard[$value['id']]."</b> Total Actions</li></ul></li>";
	}
	}
	$boardOutput = '<h3 class="clickme"  data-ttext="boards" id="showtheboards" >Show boards &darr;</h3> 
	<div class="boardreveal showtheboards" >
	<ul >'.$boardOutput.'</ul>
	</div>
	<hr/>';
	
	//create the top stats
	
	$time = round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]);
    
	if($isCron){
	$processtime = "cron job exec took: $time seconds";
	}else{
	$processtime = "Last refresh took: $time seconds ";
	}
	$stats = "Total boards  =  <b>$organisationBoardsResultCount</b>
	<br/> Total members = <b>$OrganisationMembershipsResultCount</b><br/>
	Total members who joined atleast 1 board =  <b>$totalmemberswhojoined1board</b> (<b>".trelloDash::getpct($OrganisationMembershipsResultCount,$totalmemberswhojoined1board)." %</b>)<br/>
	Total members who performed atleast 1 action = <b>$totalmemberswhotookatleast1action</b> (<b>".trelloDash::getpct($OrganisationMembershipsResultCount,$totalmemberswhotookatleast1action)." %</b>)<br/>
	<a href='?refresh=TVP' title='Refresh Data'>last update ".date('Y-m-d H:i:s')."<br/>Click here to refresh data <br/>($processtime)</a> 
	<br/>
	<br/>
	<hr/>
	";
	
	$JSscript = '<script>
	$( document ).ready(function() {
	$(".clickme").on("click",function(){ 
		var link = $(this);
		var linkID = link.attr("id");
		var ttext = link.data("ttext");
		console.log("hi" + linkID);
		$("."+linkID).slideToggle("slow", function() {
			if ($(this).is(":visible")) {
            link.html("Hide "+ttext+" &uarr;");                
			} else {
            link.html("Show "+ttext+" &darr;");                
			} 
		});
	});
	});
 </script>';
	
	//return the full output
	return  finaloutput($head.$JSscript.$stats.$boardOutput.$OrganisationbreakdownOutput);
	}// end dash main if block
	}// end view
	}// end class

	
	class login{

	private $token;
	public $userID;
	public $userJSon;
	public $isTVP;

	function __construct($token){
	global $APIkey;
	global $TVPID;

	$this->token = $token;

	$trelloURL = 'https://api.trello.com/1/tokens/'.$this->token.'/member?fields=idOrganizations%2C%20id&key='.$APIkey.'&token='.$this->token;
	$ListResult = @ file_get_contents($trelloURL); 
	if($ListResult){
	$newJson = json_decode($ListResult, true);
	$this->userJSon = $newJson;
	$this->userID = $newJson['id'];
	foreach ($newJson['idOrganizations'] as $val){
	if($val == $TVPID){
	$this->isTVP = "1" ;
	} 			
	}
	}
	}
	
	public function logout(){
	unset($_COOKIE['memberIDC']);
	unset($_COOKIE['UserTokenC']);
	unset($_SESSION['memberIDS']);
	setcookie("memberIDC", FALSE , time() - 3600,"/", "dashboard.thevenusproject.com", true );
	setcookie("UserTokenC" , FALSE , time() - 3600,"/", "dashboard.thevenusproject.com", true );
	$_SESSION['memberIDS'] = "";
	
	}
	}


	
	class cookieAuth{
	private $cookieToken;
	private $cookieUserID;
	public $CookieAuthenticated;
	function __construct($cookieToken, $cookieUserID){
	global $APIkey;
	global $TVPID;
	$trelloURL = 'https://api.trello.com/1/tokens/'.$cookieToken.'/member?fields=idOrganizations%2C%20id&key='.$APIkey.'&token='.$cookieToken;
	$ListResult = @ file_get_contents($trelloURL); 
	if($ListResult){
	$newJson = json_decode($ListResult, true);
	foreach ($newJson['idOrganizations'] as $val){
	if($val == $TVPID && $cookieUserID == $newJson['id']){
	$this->CookieAuthenticated = "1" ;
	}
	} 			
	}
	}
	}
	
	
	
	?>
