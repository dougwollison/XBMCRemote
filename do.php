<?php
// Returning JSON data ideally
header('Content-type: application/json');

// Load config
require 'php/config.php';

// Load XJRA
require 'php/XJRA/_load.php';

// Reply function, outputs the JSON data; an erro message by default
function reply($message, $error = true){
	if($error){
		$message = array('error' => array('message' => $message));
	}

	die(json_encode($message));
}

// Check for AJAX, proceed if so.
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	// Make sure action is passed and in Namespace.Method format
	if(!isset($_POST['action'])) reply('No action specified');
	if(!strpos($_POST['action'], '.')) reply('Invalid action specified');

	// Split the action into the namespace and method
	list($ns, $method) = explode('.', $_POST['action']);

	$params = $objid = null;

	// Load the params and objid if passed
	if(isset($_POST['params'])) $params = $_POST['params'];
	if(isset($_POST['objid'])) $objid = $_POST['objid'];

	// Load the API with config data
	$API = new XJRA\API(
		XBMC_HOST,
		XBMC_PORT,
		XBMC_USER,
		XBMC_PASS,
		CACHE_USER,
		CACHE_PASS,
		CACHE_NAME,
		CACHE_HOST,
		!isset($_POST['fresh']) // Allow caching unless "fresh" parameter is passed
	);

	// Check if namespace exists
	if(!property_exists($API, $ns)) reply('Namespace does not exist');

	// Fetch the response from the reqeusted method of the specified namespace
	$response = $API->$ns->$method($params, $objid);

	// Reply with the response (specify not an error)
	reply($response, false);
}