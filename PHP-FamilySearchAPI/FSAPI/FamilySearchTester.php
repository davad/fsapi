<?php
/*
 * This is a test file for the PHP FamilySearch Client which tests
 * many of the functions of the API.
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
 * at http://www.opensource.org/licenses/lgpl-license.php\
 *
 * @author Hoang Le
 * @author Joseph Phalouka
 */
include_once('FamilySearchProxy.php');

//set the username and password for familysearch
$username = 'api-user-1217';
$password = '169c';
$url = 'http://ref.dev.usys.org';

?>

<html>
	<head>
		<title>FamilySearch API Tester</title>
	</head>
<body>
Expected result: Success<br />
TESTING making multiple call
<?php  
$proxy = new FamilySearchProxy();
$proxy->setUrl($url);
$proxy->setUserName($username);
$proxy->setPassword($password);

$response9 = $proxy->getUserById('me', '', false);
print "GetUserById: <pre>".htmlentities($response9)."</pre>";

$response8 = $proxy->getPerson('name=George+Washington&minScore=5&maxResults=2', false);
print "<br />GetPerson: <pre>".htmlentities($response8)."</pre>";

$response9 = $proxy->getUserById('me', '', false);
print "GetUserById: <pre>".htmlentities($response9)."</pre>";

$response10 = $proxy->getPersonById('p.14000021822', '', false);
print "GetPersonById: <pre>".htmlentities($response10)."</pre>";

$response11 = $proxy->searchByQuery('/familytree/v1/person/p.14000021822', true);
print "Search: <pre>".htmlentities($response11)."</pre>";

$response11 = $proxy->searchByQuery('/familytree/v1/person/p.14000021822', true);
print "Search: <pre>".htmlentities($response11)."</pre>";

$response22 = $proxy->getPersonById('p.14000021822', '');
print "GetPersonById: <pre>".htmlentities($response22)."</pre>";
?><hr />

Expected result: Success<br />
TESTING getRequestData with getPersonById type
<?php 
$proxy = new FamilySearchProxy($url, $username, $password);
$response = $proxy->getRequestData('p.14000021822', 'getPersonById', 'view=full&citations=false&notes=false');
//print out the recieved data
print "<pre>".htmlentities($response)."</pre>";
?><hr />

Expected result: Success<br />
TESTING getRequestData with getUserById type
<?php 
$proxy = new FamilySearchProxy($url, $username, $password);
$response1 = $proxy->getRequestData('me', 'getUserById', '', false);
//print out the recieved data
print "<pre>".htmlentities($response1)."</pre>";
?><hr />

Expected result: Success<br />
TESTING getRequestData with getPerson type
<?php  
$proxy = new FamilySearchProxy();
$proxy->setUrl('http://ref.dev.usys.org');
$proxy->setUserName($username);
$proxy->setPassword($password);
$response2 = $proxy->getRequestData('', 'getPerson', 'name=George+WADE&minScore=5&maxResults=2');
//print out the recieved data
print "<pre>".htmlentities($response2)."</pre>";
?><hr />

Expected result: Fail, recieve error without xml tag<br />
TESTING wrong authentication
<?php  
$proxy = new FamilySearchProxy();
$proxy->setUrl($url);
$proxy->setUserName('finlayjs');
$proxy->setPassword($password);
$response22 = $proxy->authenticate(false);
//print out the recieved data
print "<pre>".htmlentities($response22)."</pre>";
?><hr />

Expected result: Fail<br />
TESTING getRequestData with wrong authentication
<?php  
$proxy = new FamilySearchProxy();
$proxy->setUrl($url);
$proxy->setUserName('test');
$proxy->setPassword($password);
$response222 = $proxy->getRequestData('', 'getPerson', 'name=George+WADE&minScore=5&maxResults=2', false);
//print out the recieved data
print "<pre>".htmlentities($response222)."</pre>";
?><hr />

Expected result: Fail<br />
TESTING addPerson
<?php  
$proxy = new FamilySearchProxy();
$proxy->setUrl($url);
$proxy->setUserName($username);
$proxy->setPassword($password);
$response3 = $proxy->addPerson($response);
//print out the recieved data
print "<pre>".htmlentities($response3)."</pre>";
?><hr />

Expected result: Fail, recieve error without xml tag<br />
TESTING updatePerson
<?php 
$proxy = new FamilySearchProxy($url, $username, $password);
$response4 = $proxy->updatePerson('p.14000023498', $response, false);
//print out the recieved data
print "<pre>".htmlentities($response4)."</pre>";
?><hr />

Expected result: Success<br />
TESTING searchByQuery
<?php 
$proxy = new FamilySearchProxy($url, $username, $password);
$response5 = $proxy->searchByQuery('/familytree/v1/person/p.14000023498', false);
//print out the recieved data
print "<pre>".htmlentities($response5)."</pre>";
?><hr />

Expected result: Success<br />
TESTING getPersonById
<?php 
$proxy = new FamilySearchProxy($url, $username, $password);
$response6 = $proxy->getPersonById('p.14000023498', 'view=full&citations=false&notes=false', false);
//print out the recieved data
print "<pre>".htmlentities($response6)."</pre>";
?><hr />

Expected result: Success<br />
TESTING getUserById
<?php 
$proxy = new FamilySearchProxy($url, $username, $password);
$response7 = $proxy->getUserById('me', '', false);
//print out the recieved data
print "<pre>".htmlentities($response7)."</pre>";
?><hr />

Expected result: Success<br />
TESTING getPerson
<?php  
$proxy = new FamilySearchProxy();
$proxy->setUrl($url);
$proxy->setUserName($username);
$proxy->setPassword($password);
$response8 = $proxy->getPerson('name=George+WADE&minScore=5&maxResults=2');
//print out the recieved data
print "<pre>".htmlentities($response8)."</pre>";
?><hr />
Expected result: Fail, no result<br />
TESTING getName
<?php
$response12 = $proxy->getName('name=George+WADE&minScore=5&maxResults=2', false);
print "<pre>".htmlentities($response12)."</pre>";
?><hr />
Expected result: Fail, no result<br />
Testing getPlaceById
<?php
$response13 = $proxy->getPlaceById('353289', '', false); 
print "<pre>".htmlentities($response13)."</pre>";
?><hr />
Expected result: Fail, no result<br />
Testing getPlace
<?php
$response14 = $proxy->getPlace('phrase=Shelly,+Idaho', false); 
print "<pre>".htmlentities($response13)."</pre>";
?><hr />
<body>
</html>