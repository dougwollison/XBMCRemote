<?php
namespace API;

class Method{
	protected $API;
	protected $namespace;
	
	public function __construct($API){
		$this->API = $API;
		$this->namespace = str_replace(__NAMESPACE__.'\\', '', get_called_class());
	}
	
	public function call($name, $params, $cache = false){
		if($cache && $response = $this->API->Cache->fetch($params)){
			return $response;
		}
	
		$request = array(
			'jsonrpc' => '2.0',
			'method' => sprintf('%s.%s', $this->namespace, $name),
			'params' => array(),
			'id' => 0
		);
		
		if(is_array($params)){
			foreach($params as &$val){
				if(is_numeric($val) && ((string) intval($val) === $val)){
					$val = intval($val);
				}
			}
			$request['params'] = $params;
		}
		
		$json = json_encode($request);
		
		$json = str_replace('"params":[]', '"params":{}', $json);
		
		$command = sprintf(
			"curl -X POST -u '%s' -H 'Content-Type: application/json' --data '%s' %s",
			$this->API->login(),
			str_replace("'", "\'", $json),
			$this->API->uri()
		);
		
		exec($command, $response);
		
		$response = json_decode($response[0]);
		
		if($cache){
			$this->API->Cache->store($params, $response);
		}
		
		return $response;
	}
	
	public function __call($name, $args){
		array_unshift($args, $name);
		return call_user_func_array(array($this,'call'), $args);
	}
}