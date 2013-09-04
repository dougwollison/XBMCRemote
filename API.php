<?php
require 'API/_Cache.php';
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
	
	public $allow_cache;
	
	protected $xbmc_host;
	protected $xbmc_port;
	protected $xbmc_user;
	protected $xbmc_pass;
	
	protected $cache;
	
	public function __construct(
		$xbmc_host, $xbmc_port, $xbmc_user, $xbmc_pass,
		$cache_user, $cache_pass, $cache_name, $cache_host = 'localhost',
		$allow_cache = true
	){
		$this->xbmc_host = $xbmc_host;
		$this->xbmc_port = $xbmc_port;
		$this->xbmc_user = $xbmc_user;
		$this->xbmc_pass = $xbmc_pass;
		
		$this->Cache = new API\Cache($cache_host, $cache_pass, $cache_user, $cache_name);
		
		$this->Addons		= new API\Addons		($this);
		$this->Application	= new API\Application	($this);
		$this->AudioLibrary	= new API\AudioLibrary	($this);
		$this->Files		= new API\Files			($this);
		$this->GUI			= new API\GUI			($this);
		$this->Input		= new API\Input			($this);
		$this->JSONRPC		= new API\JSONRPC		($this);
		$this->PVR			= new API\PVR			($this);
		$this->Player		= new API\Player		($this);
		$this->Playlist		= new API\Playlist		($this);
		$this->System		= new API\System		($this);
		$this->VideoLibrary	= new API\VideoLibrary	($this);
		$this->XBMC			= new API\XBMC			($this);
		
		$this->allow_cache = $allow_cache;
	}
	
	public function login(){
		return "$this->xbmc_user:$this->xbmc_pass";
	}
	
	public function uri(){
		return "http://$this->xbmc_host:$this->xbmc_port/jsonrpc";
	}
}