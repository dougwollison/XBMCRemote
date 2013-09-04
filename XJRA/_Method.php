<?php
namespace XJRA;

class Method{
	// Link to the API
	protected $API;

	// The actual namespace (called class name)
	protected $namespace;

	// A list of parameters that need to be a certain non-string type
	// int (integer), bool (boolean), or toggle (boolean/"toggle")
	// ? at end denotes optional and NULL is acceptable
	protected static $field_types = array(
		// Integers
		'albumlimit'		=>	'int',
		'displaytime'		=>	'int',
		'end'				=>	'int',
		'percentage'		=>	'int',
		'position'			=>	'int',
		'position1'			=>	'int',
		'position2'			=>	'int',
		'start'				=>	'int',
		'volume'			=>	'int',

		// Integers - IDs
		'albumid'			=>	'int',
		'channelgroupid'	=>	'int',
		'channelid'			=>	'int',
		'episodeid'			=>	'int',
		'movieid'			=>	'int',
		'musicvideoid'		=>	'int',
		'playerid'			=>	'int',
		'playlistid'		=>	'int',
		'setid'				=>	'int',
		'songid'			=>	'int',
		'tvshowid'			=>	'int',

		// Optinal Integers
		'disc'				=>	'int?',
		'duration'			=>	'int?',
		'episode'			=>	'int?',
		'playcount'			=>	'int?',
		'rating'			=>	'int?',
		'runtime'			=>	'int?',
		'season'			=>	'int?',
		'top250'			=>	'int?',
		'track'				=>	'int?',
		'year'				=>	'int?',

		// Booleans
		'done'				=>	'bool',
		'filterbytransport'	=>	'bool',
		'fullscreen'		=>	'bool',
		'getdescriptions'	=>	'bool',
		'getmetadata'		=>	'bool',
		'getreferences'		=>	'bool',
		'ignorearticle'		=>	'bool',
		'wait'				=>	'bool',

		// Optional Booleans
		'albumartistsonly'	=>	'bool?',
		'application'		=>	'bool?',
		'audiolibrary'		=>	'bool?',
		'gui'				=>	'bool?',
		'input'				=>	'bool?',
		'other'				=>	'bool?',
		'player'			=>	'bool?',
		'playlist'			=>	'bool?',
		'shuffled'			=>	'bool?',
		'system'			=>	'bool?',
		'videolibrary'		=>	'bool?',

		// Toggles
		'enabled'			=>	'toggle',
		'mute'				=>	'toggle',
		'partymode'			=>	'toggle',
		'play'				=>	'toggle',
		'record'			=>	'toggle',
	); // This list may be incomplete or inaccurate

	/*
	 * Constructor method
	 *
	 * @since 1.0
	 *
	 * @param API $API The API object that called it.
	 */
	public function __construct($API){
		// Store the API link
		$this->API = $API;

		// Store the namespace (strip PHP namespace form class name)
		$this->namespace = str_replace(__NAMESPACE__.'\\', '', get_called_class());
	}

	/*
	 * Place the request to the XBMC server, using caching if specified
	 *
	 * @since 1.0
	 * @use API\Cache::fetch()
	 * @use API\Cache::store()
	 *
	 * @param string $name The name of the method for the request
	 * @param array $params The parameters for the request
	 * @param bool $cache Default false Wether or not to use caching
	 * @return stdClass $response The JSON decoded response object
	 */
	public function call($name, $params = null, $cache = false){
		// Build the request array
		$request = array(
			'jsonrpc' => '2.0', // The RPC requires the version number
			'method' => sprintf('%s.%s', $this->namespace, $name), // Build the Namespace.Method String
			'params' => array(), // The RPC also requires the params entry to be present, set with empty array
			'id' => 1 // The RPC also needs an ID number, seems to not return data otherwise
		);

		// Set $request['params'] with $params if array
		if(is_array($params)){
			// But first, sanitize each value by casting integer fields as integers
			array_walk_recursive($params, function(&$value, $key, $typelist){
				if(isset($typelist[$key]) && $type = $typelist[$key]){
					$optional = false;
					if(strpos($type, '?')){
						// This is optional, skip if null
						if(is_null($value)) return;

						// Lop off the ?
						$type = rtrim($type, '?');
					}
					// Cast value based on $type
					switch($type){
						case 'int': // Convert to integer
							$value = intval($value);
							break;
						case 'bool': // Convert to boolean
							$value = (bool) $value;
							break;
						case 'toggle': // Conver to boolean if value isn't "toggle"
							if($value != 'toggle'){
								$value = (bool) $value;
							}
							break;
					}
				}
			}, self::$field_types);

			$request['params'] = $params;
		}

		// If caching is specified, and the API will allow caching,
		// return the cached response if present.
		if($cache && $this->API->allow_cache && $response = $this->API->Cache->fetch($request)){
			return $response;
		}

		// JSON encode the request
		$json = json_encode($request);

		// Make sure params is an empty OBJECT, not array (throws error in the RPC)
		$json = str_replace('"params":[]', '"params":{}', $json);

		// Build the cURL command
		$command = sprintf(
			"curl -X POST -u '%s' -H 'Content-Type: application/json' --data '%s' %s",
			$this->API->login(),
			str_replace("'", "\'", $json),
			$this->API->url()
		);

		// Execute and pass the output
		exec($command, $output);

		// Get the first line of the output as the $response data
		$response = $output[0];

		// Cache the result (passing the $params array) if specified
		if($cache){
			$this->API->Cache->store($request, $response);
		}

		// Return the JSON decoded data
		return json_decode($response);
	}

	/*
	 * Call magic method, aliasing to the call() method
	 *
	 * @since 1.0
	 * @uses self::call()
	 *
	 * @param string $name The name of the attempted method being called
	 * @param array $args The arguments passed to the attempted method
	 * @return mixed $result The returned data from the call method
	 */
	public function __call($name, $args){
		array_unshift($args, $name);
		return call_user_func_array(array($this,'call'), $args);
	}
}