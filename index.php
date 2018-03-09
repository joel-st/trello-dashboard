<?php   
	session_start();
	
	include($_SERVER['DOCUMENT_ROOT']. '/config/config.php'); 

		if(isset($APItoken) || $APItoken){
			//There is a token to check
			$login = new login($APItoken);
			if(isset($login->isTVP) && $login->isTVP == "1"){
			// the member is part of the organisation
			$_SESSION['memberIDS'] = $login->userID;
			setcookie("memberIDC", $login->userID, time()+60*60*24*30, "/", "dashboard.thevenusproject.com", true );
			setcookie("UserTokenC", $APItoken, time()+60*60*24*30, "/", "dashboard.thevenusproject.com", true);
			header('Location: /');
			exit();	
			}
		$login->logout();
		header('Location:'.$OuthURL);	
	    exit();	
		}
		
	if(((isset($_SESSION['memberIDS']) && $_SESSION['memberIDS']) || (isset($isCron) && $isCron == $isCronPass && $_SERVER['REMOTE_ADDR'] == $TVPIIP))){
	//it is either a session or a cron request
	$action['authenticated'] = "1";
	}else{
		// it is neither a session or a cron request
		// is there a cookie?
		if((isset($_COOKIE['memberIDC']) && $_COOKIE['memberIDC'] && isset($_COOKIE['UserTokenC']) && $_COOKIE['UserTokenC'])){
		// there is a cookie
			
			//check the user is authentic
			$cookieAuth = new cookieAuth($_COOKIE['UserTokenC'], $_COOKIE['memberIDC']);
			if(isset($cookieAuth->CookieAuthenticated) && $cookieAuth->CookieAuthenticated == "1"){
			// the user is authentic
			$action['authenticated'] = "1";
			}
		}	
	}
	
	
	if($action['authenticated'] != "1"){
	// the user is not authenticated
	header('Location:'.$OuthURL);	
	exit();
	}
	
	// the user is authenticated
	setcookie("memberIDC", $_COOKIE['memberIDC'], time()+60*60*24*30, "/", "dashboard.thevenusproject.com", true );
	setcookie("UserTokenC", $_COOKIE['UserTokenC'], time()+60*60*24*30, "/", "dashboard.thevenusproject.com", true);
	$view = new trelloDash;
	if (file_exists($cachefile) && $_GET['refresh'] != "TVP") { 
		echo $view->cache();          
	}else{
		ob_start();  
		echo $view->outputView('DashMain');
		$fp = fopen($cachefile, 'w');    
		fwrite($fp, ob_get_contents());    
		fclose($fp);       
		ob_end_flush();
	}
	
	exit(); 
?>
