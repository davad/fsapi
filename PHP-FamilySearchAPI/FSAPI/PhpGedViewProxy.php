<?php
/**
 * The proxy class use to connect to FamilySearch
 *
 * PHP FamilySearch API Client
 * Copyright (C) 2007  Neumont University
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * See LICENSE.txt for the full license.  If you did not receive a
 * copy of the license with this code, you may find a copy online
 * at http://www.opensource.org/licenses/lgpl-license.php
 *
 * @author Hoang Le
 * @author Joseph Phalouka
 */
if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

if (!defined('HTTP_REQUEST_METHOD_GET')) include_once('HTTP/Request.php');
include_once('ErrorParser.php');
include_once('NameParser.php');
include_once('PlaceParser.php');
require_once('FSAPIClient.php');


class PhpGedViewProxy extends FamilySearchAPIClient {
	/* Set DEBUG to true to see the full HTTP requests */
	var $DEBUG = false;

	var $hasError;
	var $hasName;
	var $gedcomid = '';

	var $paths = array(
	'login'		=>	'/client.php?action=connect',
	'logout'	=>	'/client.php?logout=1',
	'getPerson'	=>     '/client.php?action=get&xref=',
	'getPersonById'	=> '/client.php?action=get&xref=',
	'addPerson'	=> '/client.php?action=append',
	'updatePersonById'	=> '/client.php?action=update&xref=',
	'getPlace'	=>	   '',
	'getPlaceById'	=>  '',
	'getUserById'	=>   '',
	'getName'	=>	'',
	'matchById'		=>	'/client.php?action=get&xref=',
	'matchByQuery'	=>	'/client.php?action=search'
	);

	/**
	 * constructor
	 *Example:
	 * 		$proxy = new familySearchProxy('http://ref.dev.usys.org', 'user', 'pass');
	 * or
	 * 		$proxy = new familySearchProxy();
	 $proxy->setUrl('http://ref.dev.usys.org');
	 $proxy->setUserName('user');
	 $proxy->setPassword('pass');
	 * @param String 	username
	 * @param String	password
	 * @param String	gedcomid
	 */
	function PhpGedViewProxy($url='', $userName='', $password='', $ged=''){
		parent::FamilySearchAPIClient($url, $userName, $password);
		$this->hasError = false;
		$this->SESSIONID_NAME=session_name();
		$this->RETURN_TYPE = "GEDCOM";
		$this->gedcomid = $ged;
		if (!empty($this->gedcomid)) {
			//--make sure the GEDCOM id is specified in the URLs
			foreach($this->paths as $pathid=>$path) {
				$this->paths[$pathid] = preg_replace("/php\?/", "php?ged=".$this->gedcomid."&", $path);
			}
		}
	}

	/**
	 * this method will send a request to the webservice getting the current authenticate
	 * user information
	 * @param errorXML if the return value contain errors, setting errorXML to false will
	 * 		return a message decribe the errors, if set to true, will return the error in
	 * 		xml, default is true
	 */
	function authenticate($errorXML = true){
		$this->hasError = false;
		//check the current url
		if(empty($this->url) || empty($this->userName) || empty($this->password)){
			$this->hasError = true;
			return "connection is not set or authentication required";
		}

		$con = $this->url.$this->paths['login'];

		//create the request object
		$params = array("timeout"=>$this->timeout, "readTimeout"=>$this->readTimeout);
		$request = new HTTP_Request($con, $params);
		$request->addHeader("User-Agent", $this->agent);
		$request->setBasicAuth($this->userName, $this->password);
		$request->setURL($con);

		//send the request and return the xml
		$request->sendRequest();
		if ($request->getResponseCode()==200) {
			$this->loggedin = true;
			$this->_cookies = $request->getResponseCookies();
		}

		$response = $request->getResponseBody();
		$ct = preg_match("/(\w+)\s*=\s*([^\s]+)/", $response, $match);
		if ($ct>0) {
			$this->SESSIONID_NAME = $match[1];
			$this->sessionid = $match[2];
		}
		
		//-- store the id on the session for subsequent API calls
		if (session_id() != "") {
			 $_SESSION['phpfsapi_sessionid'] = $this->sessionid;
			 $_SESSION['phpfsapi_sessionname'] = $this->SESSIONID_NAME;
		}

		if($errorXML) return $response;
		else return $this->checkErrors($request->getResponseBody());

	}
	
	/**
	 * Logout from the web service api
	 * 
	 * @param boolean $errorXML		set to false to automatically parse for errors
	 * @return mixed	a string of the xml data or an array of errors
	 */
	function logout($errorXML = true) {
		$result = $this->getRequestData('', 'logout', '', $errorXML);
		parent::logout($errorXML);
		return $result;
	}

	/**
	 * check the response from the webservice for error
	 * @param $response the response to check for errors
	 * @return return the response if no errors are found
	 * or return an error list.
	 */
	function checkErrors($response){
		if (preg_match("/ERROR \d+:/", $response)>0) {
			$this->hasError = true;
		}
		return $response;
	}
	
	function getRequestData($id="", $type="", $query="", $errorXML=false){
		$result = parent::getRequestData($id, $type, $query, $errorXML);
		$result = preg_replace("/SUCCESS\r?\n/", "", $result);
		return result;
	}
}
?>