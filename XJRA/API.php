<?php
namespace XJRA;

/*
 * The API class for XBMC
 * For sending requests and returning responses to the...
 *		XJRA... or
 *		XBMC JSON RPC API... or
 *		XBox Media Center JavaScript Object Notation Remote Procedural Call Application Programming Interface...
 *		Damn, initialception.
 *
 * @since 1.0
 */

class API{
	// The namespaces
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

	// Allow cache flag
	public $allow_cache;

	// Connection info
	protected $xbmc_host;
	protected $xbmc_port;
	protected $xbmc_user;
	protected $xbmc_pass;

	// Cache object
	protected $cache;

	/*
	 * Constructor method, for configuration
	 *
	 * @since 1.0
	 *
	 * @param string $xbmc_host The host name of the XBMC server
	 * @param string $xbmc_host The host name of the XBMC server
	 * @param string $xbmc_host The host name of the XBMC server
	 * @param string $xbmc_host The host name of the XBMC server
	 *
	 * @param string $cache_user The username for the cache database
	 * @param string $cache_pass The password for the cache databse
	 * @param string $cache_name The name of the cache databse
	 * @param string $cache_host Default 'localhost' The hostname of the cache databse
	 *
	 * @param (bool) $allow_cache Default true Wether or not to allow caching
	 */
	public function __construct(
		$xbmc_host, $xbmc_port, $xbmc_user, $xbmc_pass,
		$cache_user, $cache_pass, $cache_name, $cache_host = 'localhost',
		$allow_cache = true
	){
		// Store the connection data
		$this->xbmc_host = $xbmc_host;
		$this->xbmc_port = $xbmc_port;
		$this->xbmc_user = $xbmc_user;
		$this->xbmc_pass = $xbmc_pass;

		// Initialize the cache object
		$this->Cache = new Cache($cache_host, $cache_pass, $cache_user, $cache_name);

		// Initialize the namespaces
		$this->Addons		= new Addons		($this);
		$this->Application	= new Application	($this);
		$this->AudioLibrary	= new AudioLibrary	($this);
		$this->Files		= new Files			($this);
		$this->GUI			= new GUI			($this);
		$this->Input		= new Input			($this);
		$this->JSONRPC		= new JSONRPC		($this);
		$this->PVR			= new PVR			($this);
		$this->Player		= new Player		($this);
		$this->Playlist		= new Playlist		($this);
		$this->System		= new System		($this);
		$this->VideoLibrary	= new VideoLibrary	($this);
		$this->XBMC			= new XBMC			($this);

		// Set the allow_cache flag
		$this->allow_cache = $allow_cache;
	}

	/*
	 * Get the username:password string for the connection
	 *
	 * @since 1.0
	 *
	 * @return string $auth The authentication string for cURLs -u flag
	 */
	public function login(){
		return "$this->xbmc_user:$this->xbmc_pass";
	}

	/*
	 * Get the URL for the connection
	 *
	 * @since 1.0
	 *
	 * @return string $url The url for cURLs
	 */
	public function url(){
		return "http://$this->xbmc_host:$this->xbmc_port/jsonrpc";
	}
}