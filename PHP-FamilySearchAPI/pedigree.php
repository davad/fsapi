<?php
/**
 * Pedigree Tree Example
 * This script will generate a basic pedigree tree using the FamilySearch API
 * to obtain its data.
 * 
 * This pedigree chart expects a person's ID to be passed in as a request parameter
 * $rootId.  The chart will start with $rootId as the root person and then print 
 * a 4 generation pedigree chart.
 * 
 * This script uses the PHP-FSAPI library to connect and retrieve data from the 
 * FamilySearch web service API.  It then uses the PHP-FSParse library to parse
 * the results.  It uses its own functions to print the data.
 *
 * PHP FamilySearch API Parser
 * Copyright (C) 2007  Neumont University
 * 
 * This code was created by students from Neumont University under the 
 * instruction of John Finlay. Questions and comments regarding this code
 * may be directed to him at john.finlay@neumont.edu.
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
 * @author John Finlay
 */

ini_set('error_reporting', E_ALL);
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR."./FSAPI");

include_once("FSAPI/FamilySearchProxy.php");
include_once("FSParse/XMLGEDCOM.php");

//set the username and password for familysearch
$username = '';
$password = '';
$url = 'http://www.dev.usys.org';
$rootId = 'me';
if (!empty($_REQUEST['rootId'])) $rootId = $_REQUEST['rootId'];

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

//-- create an XML parser
$xmlGed = new XmlGedcom();
$xmlGed->setProxy($client);
//-- get the first person and his ancestors in one shot
$xml = $client->getPersonById($rootId, "ancestors=3");
$xmlGed->parseXml($xml);
//--get the first person 
$arr = $xmlGed->getPersons();
if ($rootId=="me") $person = current($arr);
else $person = getPerson($rootId);

/*
$assertions = $person->getAssertions();
$ordinances = array();
foreach($assertions as $assertion) {
	if (get_class($assertion)=='XG_Ordinance') {
		$ordinances[] = $assertion;
		print $assertion->getType()." ".$assertion->getTemple();
	}
}
*/

//-------------------------------------- setup some helper functions

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

/**
 * function to retrieve and parse a person's data from the api
 * @return XG_Person
 */
function &getPerson($id) {
	global $client, $xmlGed;
	
	/* -- because we set the proxy on the $xmlGed object we can ask for a person 
	//-- get the person's xml data from the API
	$person = null;
	$xml = $client->getPersonById($id);
	
	//-- parse the XML
	$xmlGed->parseXML($xml);
	if (!empty($xmlGed->error)) {
		//var_dump($xmlGed->error);
		if ($xmlGed->error->getCode()==401) basicAuthentication($xmlGed->error->getMessage());
		else {
			print "<b style=\"color:red;\">".$xmlGed->error->getMessage()."</b><br />";
			print htmlentities($xml);
			exit;
		}
	}
	*/
	//-- get the person, which would normally be the first person in the array
	$person = $xmlGed->getPerson($id);
	return $person;
}

/**
 * Recursively print out the HTML for a person box.  This function
 * recurses on the given person's parents until $gen==0 or the given
 * person is null
 * @param $person XG_Person
 */
function printPersonBox(&$person, $gen=4) {
	if ($gen==0) return;
	if (is_null($person)) return;
	$father = null;
	$mother = null;
	//-- get the person's default parents
	$parents = $person->getParents();
	if (isset($parents[1])) {
		$father = getPerson($parents[1]->getRef());
	}
	if (isset($parents[0])) {
		$mother = getPerson($parents[0]->getRef());
	}
	//-- get the persons birth and death information
	$birthDate = "";
	$deathDate = "";
	$marriageDate = "";
	$birthPlace = "";
	$deathPlace = "";
	$marriagePlace = "";
	$assert = $person->getBirthAssertion();
	if (!is_null($assert)) {
		$date = $assert->getDate();
		if (!is_null($date)) $birthDate = $date->getOriginal();
		$place = $assert->getPlace();
		if (!is_null($place)) $birthPlace = $place->getOriginal();
	}
	$assert = $person->getDeathAssertion();
	if (!is_null($assert)) {
		$date = $assert->getDate();
		if (!is_null($date)) $deathDate = $date->getOriginal();
		$place = $assert->getPlace();
		if (!is_null($place)) $deathPlace = $place->getOriginal();
	}	
	$assert = $person->getMarriageAssertion();
	if (!is_null($assert)) {
		$date = $assert->getDate();
		if (!is_null($date)) $marriageDate = $date->getOriginal();
		$place = $assert->getPlace();
		if (!is_null($place)) $marriagePlace = $place->getOriginal();
	}
	//-- generate the HTML
	?>
	<table cellspacing="0" cellpadding="0" border="0">
	<tr>
	<td>
	<div id="<?php print $person->getId();?>" class="personbox">
		<a class="name" href="pedigree.php?rootId=<?php print $person->getId();?>"><?php print $person->getPrimaryName()->getFullText(); ?></a><br />
		<span class="label"><b>Birth:</b></span> <?php print $birthDate." ".$birthPlace; ?><br />
		<span class="label"><b>Marriage:</b></span> <?php print $marriageDate." ".$marriagePlace; ?><br />
		<span class="label"><b>Death:</b></span> <?php print $deathDate." ".$deathPlace; ?><br />
	</div>
	</td>
	<td>
	<table>
	<tr>
		<td>
			<?php printPersonBox($father, $gen-1); ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php printPersonBox($mother, $gen-1); ?>
		</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	<?php
}

//------------------------------------------------------ start the HTML
?>
<html>
<head>
<title>FamilySearch API Pedigree Test</title>
<style>
.personbox {
	width: 220px; 
	height: 80px; 
	border: solid blue 1px; 
	background-color: #AAAAFF;
	overflow: hidden;
	font-size: 10pt;
}
.name {
	font-weight: bold;
}
.label {
	font-decoration: underline;
}
</style>
</head>
<body>
<h2>Pedigree Chart for <?php print $person->getPrimaryName()->getFullText(); ?></h2>
<?php printPersonBox($person, 3); ?>
</body>
</html>