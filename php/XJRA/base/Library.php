<?php
namespace XJRA;

abstract class Library extends Method{
	protected static $id_fields = array();

	/*
	 * Get Things (Artists, Movies, TV Shows, etc.)
	 * Alias class called by self::__call()
	 *
	 * @since 1.0
	 *
	 * @param string $type The type of thing to get
	 * @param array $params The parameters for the method (see docs)
	 * @return stdClass $result The result of the method call
	 */
	protected function GetThings($type, $params){
		// Make the call (with caching)
		return $this->call("Get{$type}", $params, true);
	}

	/*
	 * Get/Set Thing (Artist, Movie, TV Show, etc.) Details
	 * Alias class called by self::__call()
	 *
	 * @since 1.0
	 *
	 * @param string $action The action to do to the thing
	 * @param string $type The type of thing to get
	 * @param array|null $params The parameters for the method (see docs)
	 * @param int $thingid The ID of the thing in question
	 * @return stdClass $result The result of the method call
	 */
	protected function DoThingDetails($action, $type, $params, $thingid = null){
		// If $params isn't an array, assume it's actually $thingid
		if(!is_array($params)){
			$thingid = $params;
			$params = array();
		}

		// Check if a special id field is needed for $type
		if(isset(self::$id_fields[$type])){
			$idfield = self::$id_fields[$type];
		}else{ // Otherwise, just lowercase it and append "id"
			$idfield = strtolower($type).'id';
		}

		// Set tvshowid param with ${$idfield}
		$params[$idfield] = $thingid;

		// Make the call (with caching)
		return $this->call("{$action}{$type}Details", $params, true);
	}

	/*
	 * Magic call method, for aliasing to general purpose methods
	 *
	 * @since 1.0
	 *
	 * @param string $name The name of the method being called
	 * @param array $args The argumetns passed to the method
	 * @return result The result of the aliased method
	 */
	public function __call($name, $args){
		if(preg_match('/(Get|Set)(\w+)Details/', $name, $matches)){
			// Prepend capture groups 1 & 2 to $args (as $action & $type args)
			$args = array_merge(array($matches[1], $matches[2]), $args);

			// Call and return the result of GetThingDetails
			return call_user_func_array(array($this, 'DoThingDetails'), $args);
		}elseif(preg_match('/Get(\w+)/', $name, $matches)){
			// Prepend capture group to $args (as $type arg)
			array_unshift($args, $matches[1]);

			// Call and return the result of GetThingDetails
			return call_user_func_array(array($this, 'GetThings'), $args);
		}
	}
}