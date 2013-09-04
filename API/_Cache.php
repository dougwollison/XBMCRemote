<?php
namespace API;

class Cache{
	protected $dbh;
	protected $table = 'XBMCRemote_Cache';
	
	public function __construct($host, $user, $pass, $name){
		$this->dbh = new \MySQLi($host, $user, $pass, $name);
	}
	
	public function __call($name, $args){
		return call_user_func_array(array($this->dbh, $name), $args);
	}
	
	public function __get($var){
		return $this->dbh->$var;
	}
	
	public function store($request, $response){
		if(!is_string($request)) $request = json_encode($request);
		if(!is_string($response)) $response = json_encode($response);
		
		$key = $this->dbh->real_escape_string(sha1($request));
		$request = $this->dbh->real_escape_string($request);
		$response = $this->dbh->real_escape_string($response);
		
		$query = "REPLACE INTO $this->table (`key`, `request`, `response`) VALUES ('$key', '$request', '$response')";
		
		$this->dbh->query($query);
	}
	
	public function fetch($request){
		if(!is_string($request)) $request = json_encode($request);
	
		$key = $this->dbh->real_escape_string(sha1($request));
		
		$query = "SELECT `response` FROM $this->table WHERE `key` = '$key'";
		
		$result = $this->dbh->query($query);
		
		if(!$result) return false;
		
		$object = $result->fetch_object();
		
		if(!$object) return false;
		
		$response = $object->response;
		$result->free();
		
		$json = json_decode($response);
		
		return json_last_error() === JSON_ERROR_NONE ? $json : $response;
	}
}