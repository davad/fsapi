<?php
/**
 * Retrieves the person XML from the FamilySearch API and outputs 
 * the XML and the GEDCOM. 
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
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR."./FSAPI");

include_once("FSAPI/FamilySearchProxy.php");
include_once("FSParse/XMLGEDCOM.php");
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

//set the username and password for familysearch
$username = '';
$password = '';
// devkey can be set here or in a file called familysearchdev.key
$devkey = '';
$url = 'http://www.dev.usys.org';
//$url = 'https://api.familysearch.org/';
$id = 'KWCY-C74';
if (!empty($_REQUEST['id'])) $id = $_REQUEST['id'];

//-- check for authentication if a preset username and password were not provided
if ((empty($username) || empty($password)) && !isset($_SERVER['PHP_AUTH_USER'])) {
   basicAuthentication();
}
else {
	$username = $_SERVER['PHP_AUTH_USER'];
	$password = $_SERVER['PHP_AUTH_PW'];
	$client = new FamilySearchProxy($url, $username, $password, $devkey);
	$client->setAgent("PHP-FSAPI");
	$client->authenticate();
	if (empty($client->sessionid)) basicAuthentication();
}
$client->DEBUG=true;
//-- create a proxy client class
$xml = $client->getPersonById($id, "&view=summary&view=information");
$errors = $client->checkErrors($xml);
if ($client->hasError) basicAuthentication();	

$xmlGed = new XmlGedcom();
$xmlGed->setProxy($client);
$xmlGed->parseXML($xml);
$arr = $xmlGed->getPersons();
if ($id=="me") $person = current($arr);
else $person = $xmlGed->getPerson($id);
$gedcom = "";
if (!empty($person)) {
	$gedcom = $person->getIndiGedcom();
	$gedcom .= "\r\n<b>FAMS</b>\r\n";
	foreach($person->getFamSGedcom() as $ged) $gedcom .= $ged['gedcom'];
	$gedcom .= "\r\n<b>FAMC</b>\r\n";
	foreach($person->getFamCGedcom() as $ged) $gedcom .= $ged['gedcom'];
}
?>
<html>
<head>
	<title>FamilySearch API XML to GEDCOM Example</title>
</head>
<body>
<?php if (is_null($person)) {
	print "Unable to find person with id: ".$id;
	print htmlentities($xml);
	exit;
}
?>
<h2>XML to GEDCOM Conversion Test for <?php if (!is_null($person->getPrimaryName())) print $person->getPrimaryName()->getFullText(); ?></h2>
<table border="1">
<tr><th>GEDCOM</th><th>XML</th></tr>
<tr>
	<td valign="top">
		<pre><?php print $gedcom; ?></pre>
	</td>
	<td valign="top">
		<pre><?php print htmlentities(preg_replace("/></", ">\n<", $xml)); ?></pre>
	</td>
</tr>
</table>
</body>
</html>