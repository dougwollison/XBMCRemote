<?php
define('AJAX', !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

require 'config.php';

require 'API.php';

header('Content-type: application/json');

function reply($message, $error = true){
	if($error){
		$message = array('error' => array('message' => $message));
	}
	
	die(json_encode($message));
}

if(AJAX){
	if(!isset($_POST['action'])) reply('No action specified');
	if(!strpos($_POST['action'], '.')) reply('Invalid action specified');
	list($ns, $method) = explode('.', $_POST['action']);
	
	$params = $objid = null;
	
	if(isset($_POST['params'])) $params = $_POST['params'];
	if(isset($_POST['objid'])) $objid = $_POST['objid'];
	
	$API = new API(XBMC_HOST, XBMC_PORT, XBMC_USER, XBMC_PASS, CACHE_USER, CACHE_PASS, CACHE_NAME, CACHE_HOST, !isset($_POST['fresh']));
	
	if(!property_exists($API, $ns)) reply('Namespace does not exist');
	
	$response = $API->$ns->$method($params, $objid);
	
	reply($response, false);
}