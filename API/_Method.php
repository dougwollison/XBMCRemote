<?php
class Method{
	protected $api;
	protected $namespace;
	
	public function __construct($api){
		$this->api = $api;
		$this->namespace = get_called_class();
	}
	
	public function call($name, $args){
		$request = array(
			'jsonrpc' => '2.0',
			'method' => "$this->namespace.$name"
		);
		
		if(isset($args[0]) && is_array($args[0])){
			$request['params'] = $args[0];
			
			if(isset($args[1])){
				$request['id'] = $args[0];
			}
		}elseif(isset($args[0])){
			$request['id'] = $args[0];
		}
		
		$json = json_encode($request);
		
		$command = sprintf(
			"curl -X POST -u '%s' -H 'Content-Type: application/json' --data '%s' %s",
			$this->api->login(),
			str_replace("'", "\'", $json),
			$this->api->uri()
		);
		
		$response = exec($command);
		
		return json_decode($response);
	}
	
	public function __call($name, $args){
		return $this->call($name, $args);
	}
}