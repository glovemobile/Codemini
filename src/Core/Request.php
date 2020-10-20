<?php 

/**
 * Codemini Framework
 *
 * An open source application development small framework
 *
 * @package		Codemini Framework
 * @author		Fabricio Polito <fabriciopolito@gmail.com>
 * @copyright	Copyright (c) 2020 - 2020.
 * @license		https://github.com/fabriciopolito/Codemini/blob/master/LICENSE
 * @link		https://github.com/fabriciopolito/Codemini
 * @since		Version 1.0
 */

namespace Codemini\Core;

use \Exception;

class Request
{
	static private $_directoryController = "";
	static private $_controller;
	static private $_method;
	static private $_args;
		
	/**
	 * start
	 *
	 * @return void
	 */
	public static function start()
	{
		/**
		 * If the project is located in subfolder of www or htdocs then remove uri from url project folders
		 */
		$app_uri = strtolower(configItem('app_project_uri'));
		
		if( ! empty($app_uri)) {

			$app_uri = ltrim($app_uri, "/");
			$app_uri = rtrim($app_uri, "/");
			$app_uri = "/" . $app_uri . "/";

			$_SERVER['REQUEST_URI'] = str_replace($app_uri, "", strtolower($_SERVER['REQUEST_URI']));
		}

        $parts = explode('/',$_SERVER['REQUEST_URI']);
        $parts = array_filter($parts);
        $parts = array_values($parts);

        //Remove index.php from URI
        if(isset($parts[0]) && $parts[0] == 'index.php'){
            array_shift($parts);
		}
		
		//check if the current controller is a directory
		if(isset($parts[0]) && self::isValidDirectoryURI($parts[0]) && is_dir(DIR_CONTROLLER . ucfirst($parts[0]))) {
			self::$_directoryController = ucfirst($parts[0]);
			array_shift($parts);
		}

		self::$_controller = ($c = array_shift($parts))? self::isValidURI($c) : 'Home';
		self::$_controller = str_replace("-", "_", self::$_controller);
		self::$_controller = ucfirst(self::$_controller);

		//Check if query string exists
		$c = array_shift($parts);
		$c = (strpos($c, "?") === false) ? $c : current(explode("?", $c));
		self::$_method = ($c) ? self::isValidURI($c) : 'index';
		self::$_method = str_replace("-", "_", self::$_method);

		self::$_args = (isset($parts[0])) ? $parts : array();
    }
    
    /**
	 * Filter segments for malicious characters
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public static function isValidURI($str)
	{
		if(preg_match('/[^a-z_\-0-9]/i', $str)) return 'Invalid-filterPartURI';
		return $str;
	}

	/**
	 * Filter segments for malicious characters
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public static function isValidDirectoryURI($str)
	{
		return (!preg_match('/[^a-z_\-0-9]/i', $str));
	}
	
	/**
	 * getRequestMethod
	 *
	 * @return string
	 */
	public static function getRequestMethod()
	{
		return $_SERVER['REQUEST_METHOD'];
	}
	
	/**
	 * Check is ajax
	 *
	 * @return boolean
	 */
	public static function isAjax(){
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') return true;
		return false;
	}
	
	/**
	 * Check is GET request
	 *
	 * @return boolean
	 */
	public static function isGet(){
		if(!empty($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'get') return true;
		return false;
	}

	/**
	 * Check is POST request
	 *
	 * @return boolean
	 */
	public static function isPost(){
		if(!empty($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') return true;
		return false;
	}

	/**
	 * Check is SSL request
	 *
	 * @return boolean
	 */
	public static function isSSL(){
        return isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off";
	}
	
	/**
	 * Get client IP
	 *
	 * @return string
	 */
	public static function clientIp(){
        return isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']: null;
	}
	
	/**
	 * Get User Agent
	 *
	 * @return string
	 */
	public static function userAgent(){
        return isset($_SERVER['HTTP_USER_AGENT'])? $_SERVER['HTTP_USER_AGENT']: null;
    }
	
	/**
	 * getDirectoryController
	 *
	 * @return string
	 */
	public static function getDirectoryController()
	{
		return self::$_directoryController;
	}
	
	/**
	 * getController
	 *
	 * @return string
	 */
	public static function getController()
	{
		return self::$_controller;
	}
	
	/**
	 * getMethod
	 *
	 * @return string
	 */
	public static function getMethod()
	{
		return self::$_method;
	}
		
	/**
	 * getArgs
	 *
	 * @return array
	 */
	public static function getArgs()
	{
		return self::$_args;
	}
}