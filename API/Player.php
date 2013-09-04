<?php
namespace API;

class Player extends Method{
	public function PlayPause($play = 'toggle'){
		return $this->call(__FUNCTION__, array('playerid' => 1));
	}
}