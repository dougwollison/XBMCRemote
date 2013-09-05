<?php
namespace XJRA;

/*
 * XJRA Caching system
 *
 * @since 1.0
 */

class Cache{
	// Database handler
	protected $dbh;

	// Table name
	protected $table = 'XBMCRemote_Cache';
	
	// Ready flag
	public $ready;

	/*
	 * Constructor method, for configuration and creating the MySQLi handler
	 *
	 * @since 1.0
	 */
	public function __construct($host, $user, $pass, $name){
		// Initialize the MySQLi object for $dbh
		$this->dbh = new \MySQLi($host, $user, $pass, $name);
		
		// Create the table if it doesn't exist
		$this->dbh->query("
		CREATE TABLE `$this->table` (
			`key` varchar(40) NOT NULL,
			`request` text NOT NULL,
			`response` longtext NOT NULL,
			`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			UNIQUE KEY `key` (`key`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");
	}

	/*
	 * Call magic method, aliasing to $dbh's methods
	 *
	 * @since 1.0
	 *
	 * @param string $name The name of the attempted method being called
	 * @param array $args The arguments passed to the attempted method
	 * @return mixed $result The returned data from the $dbh method
	 */
	public function __call($name, $args){
		return call_user_func_array(array($this->dbh, $name), $args);
	}

	/*
	 * Get magic method, aliasing to $dbh's properties
	 *
	 * @since 1.0
	 *
	 * @param string $var The name of the attempted variable being accessed
	 * @return mixed $result The returned data from the $dbh method
	 */
	public function __get($var){
		return $this->dbh->$var;
	}

	/*
	 * Store the request/response pair in the database
	 * Will replace the cache of the request with new data if already present
	 *
	 * @since 1.0
	 *
	 * @param mixed $request The request data (raw array or encoded json data)
	 * @param mixed $response The response data (raw array or encoded json data)
	 */
	public function store($request, $response){
		// JSON encode the request and response if needed
		if(!is_string($request)) $request = json_encode($request);
		if(!is_string($response)) $response = json_encode($response);

		// Create the unique key; a SHA1 of the request data
		$key = sha1($request);

		// Escape the request and response strings
		$request = $this->dbh->real_escape_string($request);
		$response = $this->dbh->real_escape_string($response);

		// Build the query
		$query = "REPLACE INTO $this->table (`key`, `request`, `response`) VALUES ('$key', '$request', '$response')";

		// Execute the query
		$this->dbh->query($query);
	}

	/*
	 * Retrieve the cached response for the specified request
	 *
	 * @since 1.0
	 *
	 * @param mixed $request The request data (raw array or encoded json data)
	 * @return stdClass $response The response data if present (false if error)
	 */
	public function fetch($request){
		// JSON encode the request if needed
		if(!is_string($request)) $request = json_encode($request);

		// Create the unique key; a SHA1 of the request data
		$key = sha1($request);

		// Build the query
		$query = "SELECT `response` FROM $this->table WHERE `key` = '$key'";

		// Execute the query
		$result = $this->dbh->query($query);

		// If no result, return false
		if(!$result) return false;

		// Fetch the object from the result
		$object = $result->fetch_object();

		//Free the result
		$result->free();

		// If empty or not an object, return false
		if(!$object) return false;

		// Retrieve the response data
		$response = $object->response;

		// Decode the JSON data
		$json = json_decode($response);

		// Return the json data
		return $json;
	}
}