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

if (!defined('HTTP_REQUEST_METHOD_GET')) {
	//-- check if we are running as a PGV module
	if (file_exists('SOAP/HTTP/Request.php')) include_once('SOAP/HTTP/Request.php');
	else include_once('HTTP/Request.php');
}
include_once('ErrorParser.php');
include_once('NameParser.php');
include_once('PlaceParser.php');
require_once('FSAPIClient.php');


class FamilySearchProxy extends FamilySearchAPIClient {
	/* Set DEBUG to true to see the full HTTP requests */
	var $DEBUG = false;

	var $hasName;
	var $devKey;

	var $paths = array(
	'login'		=>	'/identity/v1/login',
	'logout'	=>	'/identity/v1/logout',
	'getPerson'	=>     '/familytree/v2/search',
	'getPersonById'	=> '/familytree/v2/person/',
	'addPerson'	=> 'familytree/v2/person',
	'updatePersonById'	=> '/familytree/v2/person/',
	'getPlace'	=>	   '/familytree/v2/place?',
	'getPlaceById'	=>  '/familytree/v2/place/',
	'getUserById'	=>   '/familytree/v2/user/',
	'getName'	=>	'/familytree/v2/name?',
	'matchById'		=>	'/familytree/v2/match/',
	'matchByQuery'	=>	'/familytree/v2/match?',
	'mergePerson' => '/familytree/v2/person/',
	'addRelationship' => '/familytree/v2/person/'
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
	 * @param String	GET or POST, default is GET
	 */
	function FamilySearchProxy($url='', $userName='', $password='', $devkey=''){
		parent::FamilySearchAPIClient($url, $userName, $password);
		$this->hasError = false;
		$this->SESSIONID_NAME='sessionId';
		$this->RETURN_TYPE = "XML";
		$this->devKey = $devkey;
		if (empty($this->devKey)) {
			if (file_exists('familysearchdev.key')) $this->devKey = trim(file_get_contents('familysearchdev.key'));
			else $this->devKey = '1234567890';
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
		if (!empty($this->devKey)) $con .= "?key=".$this->devKey;

		//create the request object
		$params = array("timeout"=>$this->timeout, "readTimeout"=>$this->readTimeout);
		$request = new HTTP_Request($con, $params);
		$request->_useBrackets = false;
		$request->addHeader("User-Agent", $this->agent);
		$request->setBasicAuth($this->userName, $this->password);
		$request->setURL($con);

		//$reqstr = $request->_buildRequest();
		//var_dump($reqstr);
		
		//send the request and return the xml
		$request->sendRequest();
		if ($request->getResponseCode()==200) {
			$this->loggedin = true;
			$this->_cookies = $request->getResponseCookies();
		}

		$response = $request->getResponseBody();
		//var_dump($response);
		$ct = preg_match("/session\s+id=\"(.+)\"/", $response, $match);
		if ($ct>0) $this->sessionid = $match[1];
		else $this->hasError = true;
		
		//-- store the id on the session for subsequent API calls
		if (session_id() != "") {
			 $_SESSION['phpfsapi_sessionid'] = $this->sessionid;
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
		$errorParser= new ErrorParser();
		$errors = $errorParser->parseXML($response);
		//if no errors
		if(empty($errors)){
			return $response;
		}else{
			$errorList = "ERROR ENCOUNTERED: \nAccessing URL: ".$this->url." \n";
			$this->hasError = true;
			foreach($errors as $error){
				$errorList = $errorList."\n".$error;
			}
			return $errorList;
		}
	}
	
	/**
	 * return the person from the request query
	 */
	function getPerson($query, $errorXML = true){
		return $this->getRequestData('', 'getPerson', $query, $errorXML);
	}
	
	/**
	 * returns the results of a request to merge several records
	 */
	function mergePerson($recordsXML, $errorXML = true){
		return $this->addPerson($recordsXML, $errorXML);
	}

	/**
	 * check the response from the webservice for name
	 * @param $response the response to check for names
	 * @return return the response if no names are found
	 * or return an name list.
	 */
	function checkName($response) {
		$nameParser = new NameParser();
		$names = $nameParser->parseXml($response);
		if(empty($names)){
			return $response;
		} else {
			$nameList = "NAME: ";
			$this->hasName = true;
			foreach($names as $name) {
				$nameList = $nameList."\n".$name;
			}
			return $nameList;
		}
	}
	
	/**
	 * return the name from the request query
	 */
	function getName($query, $errorXML = true) {
		return $this->getRequestData('', 'getName', $query, $errorXML);
	}

	/**
	 * return the place base on query
	 */
	function getPlace($query, $errorXML = true){
		return $this->getRequestData('', 'getPlace', $query, $errorXML);
	}

	/**
	 * return a place base on id
	 */
	function getPlaceById($id, $query='', $errorXML = true){
		return $this->getRequestData($id, 'getPlaceById', $query, $errorXML);
	}

	/**
	 *
	 */
	function getUserById($id, $query, $errorXML = true){
		return $this->getRequestData($id, 'getUserById', $query, $errorXML);
	}
	
	function setDevKey($key) {
		$this->devKey=$key;
	}
}
?>