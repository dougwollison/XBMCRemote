<?php
define('AJAX', !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

require 'config.php';

require 'API.php';

header('Content-type: application/json');

function reply($message, $error = true){
	if($error){
		$message = array('error' => $message);
	}
	
	die(json_encode($message));
}

if(AJAX){
	if(!isset($_POST['ns'])) reply('No namespace specified');
	if(!isset($_POST['method'])) reply('No method specified');
	
	$ns = $_POST['ns'];
	$method = $_POST['method'];
	
	$params = $id = null;
	
	if(isset($_POST['params'])) $params = $_POST['params'];
	if(isset($_POST['id'])) $id = $_POST['id'];
	
	$API = new API(HOST, PORT, USER, PASS);
	
	if(!property_exists($API, $ns)) reply('Namespace does not exist');
	
	$API->$ns->$method($params, $id);
}