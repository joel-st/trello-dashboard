<?php
session_start();  
include($_SERVER['DOCUMENT_ROOT']. '/config/config.php'); 
if($_COOKIE['UserTokenC']){
$logout = new login($_COOKIE['UserTokenC']);
$logout->logout();
}
header('location: /');
exit();

?>