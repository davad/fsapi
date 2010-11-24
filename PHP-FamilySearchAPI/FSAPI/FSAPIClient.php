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

define('FSAPI_VERSION', '0.92a');

class FamilySearchAPIClient {
	var $agent;
	var $userName;
	var $password;
	var $url;
	var $_cookies = null;
	var $timeout = 2;
	var $readTimeout = array(0, 500000);
	var $loggedin = false;
	var $sessionid = null;
	var $SESSIONID_NAME = "sessionId";
	var $RETURN_TYPE = "unknown";
	var $hasError;
	var $maxRetries = 3;
	var $currentRetries = 0;
	var $autoThrottling = true;
	
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
	function FamilySearchAPIClient($url='', $userName='', $password=''){
		$this->agent = 'PHP-FSAPI/'.FSAPI_VERSION;
		$this->url = $url;
		$this->userName = $userName;
		$this->password = $password;
		
		if (isset($_SESSION['phpfsapi_sessionid'])) {
			$this->sessionid = $_SESSION['phpfsapi_sessionid'];
			$this->loggedin = true;
		}
		if (isset($_SESSION['phpfsapi_sessionname'])) $this->SESSIONID_NAME = $_SESSION['phpfsapi_sessionname'];
	}

	function setAgent($agent) {
		$this->agent = $agent;
	}

	function getAgent() {
		return $this->agent;
	}

	/**
	 * set the username
	 */
	function setUserName($user){
		$this->userName = $user;
	}
	
	function getUserName() {
		return $this->userName;
	}

	/**
	 * set the password
	 */
	function setPassword($pass){
		$this->password = $pass;
	}

	/**
	 * set the url
	 */
	function setUrl($url){
		if (strpos($url, "http")!==0) $url = "http://".$url;
		$this->url = $url;
	}
	
	/**
	 * Gets the type of data that is returned by this client usually XML or GEDCOM
	 *
	 * @return string
	 */
	function getReturnType() {
		return $this->RETURN_TYPE;
	}
	
	/**
	 * Returns the number of times to attempt each request 
	 *
	 * @return int
	 */
	function getMaxRetries() {
		return $this->maxRetries;
	}
	
	/**
	 * Set the maximum number of times to attemt a request before giving up
	 *
	 * @param int $retries
	 */
	function setMaxRetries($retries) {
		$this->maxRetries = $retries;
	}
	
	/**
	 * Returns whether the proxy class will try to automatically handle throttling
	 *
	 * @return boolean
	 */
	function getAutoThrottling() {
		return $this->autoThrottling;
	}
	
	/**
	 * Set whether this proxy will automatically try to handle throttling or return the error back to the client
	 *
	 * @param boolean $throttle
	 */
	function setAutoThrottline($throttle) {
		$this->autoThrottling = $throttle;
	}

	/**
	 * this method will send a request to the webservice getting the current authenticate
	 * user information
	 * @param errorXML if the return value contain errors, setting errorXML to false will
	 * 		return a message decribe the errors, if set to true, will return the error in
	 * 		xml, default is true
	 */
	function authenticate($errorXML = true){
		$this->hasError = true;
		return "This method must be implemented by an overriding class.";
	}
	
	/**
	 * Logout from the web service api
	 * 
	 * @param boolean $errorXML		set to false to automatically parse for errors
	 * @return mixed	a string of the xml data or an array of errors
	 */
	function logout($errorXML = true) {
		$this->_cookies = null;
		$this->loggedin = false;
		$this->sessionid = null;
		unset($_SESSION['phpfsapi_sessionname']);
		unset($_SESSION['phpfsapi_sessionid']);
		return "";
	}

	/**
	 * check the response from the webservice for error
	 * @param $response the response to check for errors
	 * @return return the response if no errors are found
	 * or return an error list.
	 */
	function checkErrors($response){
		$this->hasError = true;
		return "This method must be implemented by an overriding class.";
	}

	/**
	 * Get the XML for a person with the Given ID
	 * Use the $query option to specify optional Query parameters to the API
	 *
	 * @param string $id	the ID of the person to retrieve
	 * @param string $query	Additional query parameters in Query String format
	 * @param boolean $errorXML	Whether or not to try and parse the errors
	 * @return string	an XML string of the resulting data
	 */
	function getPersonById($id, $query='', $errorXML = true){
		return $this->getRequestData($id, 'getPersonById', $query, $errorXML);
	}
	
	/**
	 * Find matches by an ID 
	 *
	 * @param string $id
	 * @param boolean $errorXML
	 * @return string	an XML string of the resulting data
	 */
	function matchById($id, $errorXML=true) {
		return $this->getRequestData($id, 'matchById', '', $errorXML);
	}
	
	/**
	 * Try to match a person from query parameters
	 * A list of possible params can be found in the familysearch api documentation
	 *
	 * @param array $params		An map array of key value pairs for the parameters to search on
	 * @param boolean $errorXML
	 * @return string	an XML string of the resulting data
	 */
	function match($params, $errorXML=true) {
		$query = "";
		foreach($params as $param=>$value) {
			$query .= "&".$param."=".urlencode($value);
		}
		return $this->getRequestData('', 'matchByQuery', $query, $errorXML);
	}
	
	/**
	 * Try to match a person from a query string
	 * @param $query	the query string
	 * @param $errorXML
	 * @return string	an xml string of the resulting data
	 */
	function matchByQuery($query, $errorXML=true) {
		return $this->getRequestData('', 'matchByQuery', $query, $errorXML);
	}

	/**
	 * A generic method that provide all the "GET" methods that are provided by the FS API
	 * This is an example of how to use this method:
	 * 		getRequestData('p.0123455', 'getPersonById', 'view=full');
	 * or
	 * 		getRequestData('', 'getPerson', 'name=Mikey+Wacko&minScore=5&maxResults=2, false);
	 * @param $id this param is optional depend on the Type specify, it is the id of
	 * 			  the person/place/user.  If id is not require, use '' as the parameter.
	 * @param $type choose from the following pre-defined type: getPerson, getPersonById,
	 * 				getPlace, getPlaceById, getUserById
	 * @param $query extra parameters
	 * @param errorXML if the return value contain errors, setting errorXML to false will
	 * 		return a message decribe the errors, if set to true, will return the error in
	 * 		xml, default is true
	 */
	function getRequestData($id, $type, $query, $errorXML= true){

		//-- check that we are loggedin
		if (!$this->loggedin) {
			$result = $this->authenticate($errorXML);
			if (!$this->loggedin) return $result;
		}

		$this->hasError = false;
		//check the current url
		if(empty($this->url)  || !$this->loggedin){
			$this->hasError = true;
			return "connection string is not set or authentication required";
		}

		if (!isset($this->paths[$type])) {
			$this->hasError = true;
			return "Unknown api method type";
		}

		$con = '';

		//build the connection string
		$con = $this->url.$this->paths[$type];
		$sep = '?';
		if (strpos($con, $sep)!==false) $sep = "&";
		if(empty($id)){
			$con .= $sep.$query;
		}//if the $id parameter is not empty
		else{
			$con .= $id.$sep.$query;
		}
		
		//-- add the session id
		$con .= "&".$this->SESSIONID_NAME."=".$this->sessionid;

		//create the request object
		$request = new HTTP_Request();
		$request->_useBrackets = false;
		if (!empty($this->_cookies)||is_null($this->sessionid)) $request->setBasicAuth($this->userName, $this->password);
		else if (!empty($this->_cookies)) {
			foreach($this->_cookies as $c=>$cookie) {
				$request->addCookie($cookie['name'], $cookie['value']);
			}
		}
		$request->addHeader("User-Agent", $this->agent);
		$request->setURL($con);
		if ($this->DEBUG) print "<br /><pre>".$request->_buildRequest()."</pre>\n";

		//send the request and return the xml
		$request->sendRequest();
		//print "Getting data at: ".$request->getUrl()."<br />\n";

		if (is_null($this->_cookies)) $this->_cookies = $request->getResponseCookies();
		$response = $request->getResponseBody();
		//-- check if authenticated
		if (preg_match("/error code=\"401\"/", $response) && isset($_SESSION['phpfsapi_sessionid'])) {
			if ($this->currentRetries < $this->maxRetries) {
				$this->currentRetries++;
				$this->authenticate($errorXML);
				return $this->getRequestData($id, $type, $query, $errorXML);
			}
		}
		else if (preg_match("/error code=\"503\"/", $response) && isset($_SESSION['phpfsapi_sessionid'])) {
			if ($this->currentRetries < $this->maxRetries) {
				$this->currentRetries++;
				//-- wait 2 seconds and try again
				print "throttled waiting... ";
				sleep(2);
				return $this->getRequestData($id, $type, $query, $errorXML);
			}
		}
		if($errorXML) 
			return $response;
		else {
			return $this->checkErrors($response);
		}
	}

	/**
	 * Requests with insufficient elements or non-valid values will
	 * generate error messages.
	 * A success response will return the Person ID.
	 * @param $person the person to be add
	 * @param $errorXML if the return value contain errors, setting errorXML to 'false' will
	 * 		return a message decribe the errors, if set to true, will return the error in
	 * 		xml, default is true
	 */
	function addPerson($person, $errorXML = true){
		//-- check that we are loggedin
		if (!$this->loggedin) {
			$result = $this->authenticate($errorXML);
			if (!$this->loggedin) return $result;
		}

		$this->hasError = false;
		//check the current url
		if(empty($this->url)  || !$this->loggedin){
			$this->hasError = true;
			return "connection string is not set or authentication required";
		}

		$con = '';
		//build the connection string
		$con = $this->url.$this->paths['addPerson'];
		
		$sep = '?';
		if (strpos($con, $sep)!==false) $sep = "&";
		
		//-- add the session id
		$con .= $sep.$this->SESSIONID_NAME."=".$this->sessionid;

		//create the request object
		$request = new HTTP_Request();
		$request->_useBrackets = false;
		$request->addHeader("User-Agent", $this->agent);
		$request->addHeader("Content-Type", "text/xml");
		$request->setURL($con);
		$request->setMethod(HTTP_REQUEST_METHOD_POST);
		$request->setBody($person);
		//print "<br /><pre>".$request->_buildRequest()."</pre>\n";
		//var_dump($request);

		//send the request and return the xml
		$request->sendRequest();

		if($errorXML) 
			return $request->getResponseBody();
		else {
			return $this->checkErrors($request->getResponseBody());
		}

	}
	
	/**
	 * Add a relationship to a remote person
	 * @see https://devnet.familysearch.org/docs/api-manual-reference-system/familytree-v2/examples/person.html/document_view
	 * @param $fsid	the id of the person to add the relationship to
	 * @param $type	the type of the relationship, (parent, child, spouse)
	 * @param $relatedid	the id of the person being related to
	 * @param $xml		the xml payload
	 * @param $errorXML
	 * @return string	the xml response
	 */
	function addRelationship($fsid, $type, $relatedid, $xml, $errorXML = true){
		//-- check that we are loggedin
		if (!$this->loggedin) {
			$result = $this->authenticate($errorXML);
			if (!$this->loggedin) return $result;
		}

		$this->hasError = false;
		//check the current url
		if(empty($this->url)  || !$this->loggedin){
			$this->hasError = true;
			return "connection string is not set or authentication required";
		}

		$con = '';
		//build the connection string
		$con = $this->url.$this->paths['addPerson'];
		$con .= "/".$fsid."/".$type."/".$relatedid;
		
		$sep = '?';
		if (strpos($con, $sep)!==false) $sep = "&";
		
		//-- add the session id
		$con .= $sep.$this->SESSIONID_NAME."=".$this->sessionid;
		//print $con ."\n";
		
		//create the request object
		$request = new HTTP_Request();
		$request->addHeader("User-Agent", $this->agent);
		$request->addHeader("Content-Type", "text/xml");
		$request->setURL($con);
		$request->setMethod(HTTP_REQUEST_METHOD_POST);
		$request->setBody($xml);
		//print "<br /><pre>".htmlentities($request->_buildRequest())."</pre>\n";
		//var_dump($request);

		//send the request and return the xml
		$request->sendRequest();

		if($errorXML) 
			return $request->getResponseBody();
		else {
			return $this->checkErrors($request->getResponseBody());
		}

	}

	/**
	 * Requests with insufficient IDs, elements or non-valid values will
	 * generate error messages.
	 * The successful response for adding assertions, notes, and/or citations
	 * will the return the appropriate new IDs.
	 * @param string $id the familysearch id of the person to be edit
	 * @param string $person the XML representing the person to be edit
	 * @param boolean $errorXML if the return value contain errors, setting errorXML to false will
	 * 		return a message decribe the errors, if set to true, will return the error in
	 * 		xml, default is true
	 * @return string	the XML response
	 */
	function updatePerson($id, $person, $errorXML = true){

		//-- check that we are loggedin
		if (!$this->loggedin) {
			$result = $this->authenticate($errorXML);
			if (!$this->loggedin) return $result;
		}

		$this->hasError = false;
		//check the current url
		if(empty($this->url)  || !$this->loggedin){
			$this->hasError = true;
			return "connection string is not set or authentication required";
		}

		$con = '';
		//build the connection string
		$con = $this->url.$this->paths['updatePersonById'].$id;
		
		$sep = '?';
		if (strpos($con, $sep)!==false) $sep = "&";
		
		//-- add the session id
		$con .= $sep.$this->SESSIONID_NAME."=".$this->sessionid;

		//create the request object
		$request = new HTTP_Request();
		$request->_useBrackets = false;
		if (is_null($this->_cookies)) $request->setBasicAuth($this->userName, $this->password);
		else {
			foreach($this->_cookies as $c=>$cookie) {
				$request->addCookie($cookie['name'], $cookie['value']);
			}
		}
		$request->addHeader("User-Agent", $this->agent);
		$request->addHeader("Content-Type", "text/xml");
		$request->setURL($con);
		$request->setMethod(HTTP_REQUEST_METHOD_POST);
		$request->setBody($person);
		if ($this->DEBUG) print "<br /><pre>".$request->_buildRequest()."</pre>\n";

		//send the request and return the xml
		$request->sendRequest();

		if($errorXML) 
			return $request->getResponseBody();
		else {
			return $this->checkErrors($request->getResponseBody());
		}
	}

	/**
	 * use this method to customized your own search
	 * EXAMPLE:
	 *	searchByQuery('/v1/person/p.0123456', false);
	 * @param $query define what to search
	 * @param $errorXML if the return value contain errors, setting errorXML to false will
	 * 		return a message decribe the errors, if set to true, will return the error in
	 * 		xml, default is true
	 */
	function searchByQuery($query, $errorXML = true){

		//-- check that we are loggedin
		if (!$this->loggedin) {
			$result = $this->authenticate($errorXML);
			if (!$this->loggedin) return $result;
		}

		$this->hasError = false;
		//check the current url
		if(empty($this->url) || !$this->loggedin){
			$this->hasError = true;
			return "connection string is not set or authentication required";
		}

		$con = $this->url.$query;
		
		$sep = '?';
		if (strpos($con, $sep)!==false) $sep = "&";
		
		//-- add the session id
		$con .= $sep.$this->SESSIONID_NAME."=".$this->sessionid;

		//create the request object
		$request = new HTTP_Request();
		$request->_useBrackets = false;
		$request->addHeader("User-Agent", $this->agent);
		$request->setURL($con);
		if ($this->DEBUG) print "<br /><pre>".$request->_buildRequest()."</pre>\n";

		//send the request and return the xml
		$request->sendRequest();
		$response = $request->getResponseBody();
		if (preg_match("/error code=\"401\"/", $response) && isset($_SESSION['phpfsapi_sessionid'])) {
			$this->authenticate($errorXML);
			return $this->getRequestData($query, $errorXML);
		}
		if($errorXML) 
			return $response;
		else {
			return $this->checkErrors($response);
		}
	}
}
?>