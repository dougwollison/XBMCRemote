<?php
require 'API/_Method.php';
require 'API/Addons.php';
require 'API/Application.php';
require 'API/AudioLibrary.php';
require 'API/Files.php';
require 'API/GUI.php';
require 'API/Input.php';
require 'API/JSONRPC.php';
require 'API/PVR.php';
require 'API/Player.php';
require 'API/Playlist.php';
require 'API/System.php';
require 'API/VideoLibrary.php';
require 'API/XBMC.php';

class API{
	public $Addons;
	public $Application;
	public $AudioLibrary;
	public $Files;
	public $GUI;
	public $Input;
	public $JSONRPC;
	public $PVR;
	public $Player;
	public $Playlist;
	public $System;
	public $VideoLibrary;
	public $XBMC;
	
	protected $host;
	protected $port;
	protected $user;
	protected $pass;
	
	protected function setProps($vars, $args){
		$vars = explode(' ', $vars);
		foreach($vars as $i => $var){
			if(!isset($args[$i])) break;
			$this->$var = $args[$i];
		}
	}
	
	protected function addChildren($classes){
		$classes = explode(' ', $classes);
		foreach($classes as $class){
			$this->$class = new $class($this);
		}
	}
	
	public function __construct(){
		$this->setProps('host port user pass', func_get_args());
		$this->addChildren('Addons Application AudioLibrary Files GUI Input JSONRPC PVR Player Playlist System VideoLibrary XBMC');
	}
	
	public function login(){
		return "$this->user:$this->pass";
	}
	
	public function uri(){
		return "http://$this->host:$this->port/jsonrpc";
	}
}