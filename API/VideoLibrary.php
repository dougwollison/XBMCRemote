<?php
namespace API;

class VideoLibrary extends Method{
	public function GetTVShows($params){
		return $this->call(__FUNCTION__, $params, $this->API->allow_cache);
	}
	
	public function GetTVShowDetails($params, $showid){
		$params = array_merge((array) $params, array('tvshowid' => $showid));
	
		return $this->call(__FUNCTION__, $params, $this->API->allow_cache);
	}
}