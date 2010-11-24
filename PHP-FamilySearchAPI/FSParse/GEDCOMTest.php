<?php
/**
 * This is a test of the GEDCOM conversion portion of the 
 * Parser library.
 *
 * PHP FamilySearch API Parser
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
 * @author Cameron Thompson
 * @author John Condrey
 * @author Aeris Forrest
 * @author John Finlay
 */
include_once("XMLGEDCOM.php");
ini_set('error_reporting', E_ALL);
$xmlGed = new XmlGedcom();

$filename = "washington.xml";
$data = file_get_contents($filename);
$data=eregi_replace(">[[:space:]]+<", "><", $data);
$xmlGed->parseXML($data);

$persons = $xmlGed->getPersons();
print "Number of people: ".count($persons)."<br />";
foreach($persons as $p=>$person) {
	/* @var $person XG_Person*/
	print "<i>Name:</i> ".$person->getPrimaryName()->getFullText();
	$birth = $person->getBirthAssertion();
	if (!empty($birth)) {
		print " <i>Birth:</i> ".$birth->getDate()->getOriginal();
		print " ".$birth->getPlace()->getOriginal(); 
	}
	$death = $person->getDeathAssertion();
	if (!empty($death)) {
		print "  <i>Death:</i> ".$death->getDate()->getOriginal(); 
		print " ".$death->getPlace()->getOriginal();
	}
	print "<br />";
}
print "<table><tr><td><h2>XML Record</h2></td><td>";
print "<h2>Indi Gedcom record:</h2>";
print "</td></tr><tr><td valign=\"top\">";
$data = preg_replace("/></",">\n<", $data);
$data = preg_replace("/id=\"(.){15,100}\"/","id=\"#\"", $data);
print nl2br(htmlentities($data));

print "</td><td valign=\"top\">";
print nl2br($xmlGed->getIndiGedcom());

print "<br /><br /><hr /><br /><h3>Fam C Gedcom record:</h3>";
print nl2br($xmlGed->getFamCGedcom());

print "<br /><br /><hr /><br /><h3>Fam S Gedcom record:</h3>";
print nl2br($xmlGed->getFamSGedcom());
print "</td></tr></table>";
?>