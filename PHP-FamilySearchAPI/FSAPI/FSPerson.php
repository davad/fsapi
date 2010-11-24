<?php
/**
 * Retrieves the person XML from the FamilySearch API
 * Send in "id" as a request parameter to specify the id you want
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
 * @author John Finlay
 */

ini_set('error_reporting', E_ALL);

include_once("FamilySearchProxy.php");

//set the username and password for familysearch
$username = '';
$password = '';
//$url = 'http://ref.dev.usys.org';
$url = 'https://apibeta.familysearch.org/';
$id = 'KW3-BNM6';  // George Washington
if (!empty($_REQUEST['id'])) $id = $_REQUEST['id'];

//-- check for authentication if a preset username and password were not provided
if ((empty($username) || empty($password)) && !isset($_SERVER['PHP_AUTH_USER'])) {
   basicAuthentication();
}
else {
	$username = $_SERVER['PHP_AUTH_USER'];
	$password = $_SERVER['PHP_AUTH_PW'];
	$client = new FamilySearchProxy($url, $username, $password);
	$client->authenticate();
	if (empty($client->sessionid)) basicAuthentication();
}

//-- create a proxy client class
$client = new FamilySearchProxy($url, $username, $password);
$xml = $client->getPersonById($id);

header("Content-type: text/xml");
print $xml;

/**
 * Send the appropriate authentication headers
 */
function basicAuthentication($message = '') {
	if (empty($message)) $message = 'Unable to authenticate user.  Access denied.';
	header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo $message;
    exit;
}


?>