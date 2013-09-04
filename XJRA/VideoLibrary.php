<?php
namespace XJRA;

class VideoLibrary extends Library{
	protected static $id_fields = array('MovieSet' => 'setid');

	public function GetSeasons($params, $tvshowid){
		// If $params isn't an array, assum it's $tvshowid
		if(!is_array($params)){
			$tvshowid = $params;
			$params = array();
		}

		$params['tvshowid'] = $tvshowid;

		// Make the call (with caching)
		return $this->call(__FUNCTION__, $params, true);
	}
}