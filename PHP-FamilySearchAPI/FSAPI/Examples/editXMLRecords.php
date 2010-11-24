<?php

/**
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
 * @author Chris Hill
 */

/**
 * This code is commented to aid the developer understand exactly how to interact with the XML record format
 * of Family Search.  Before you attempt to modify any records at family search, it is suggested that you
 * log into family search and create some records.  The login information is as follows:
 *
 * Login Url: http://ref.dev.usys.org/
 * Username: api-user-1217
 * Password: 169c
 *
 * This information can also be found in the config.php located at:
 * /modules/FamilySearch/config.php
 *
 * recordID="KW3B-KS2" or "KW31-8YH"
 */

chdir('../'); // By changing the active directory up one, we make it easier to locate the required files such as FamilySearch Proxy.php
include_once '../../config.php'; // This provides the login information in a central location.  If it changes, a single update is needed instead of multiple updates
include_once 'FamilySearchProxy.php'; // This provides the connection and the ability to retrieve, update and create new FamilySearch records.
session_start(); // Starts the session so any refresh of the page brings up the same record that was previously requested
$results; // Stores the results of the record request after it has been converted using the htmlentities function of php (http://www.php.net/manual/en/function.htmlentities.php)
global $proxy;

if(!empty($_POST['id'])) // This sets the session variable to the value entered by the user.  This allows us to reload the data when the page is refreshed
{
	$_SESSION['fsID']=$_POST['id'];

}

if(empty($_SESSION['loginID']) && !empty($_POST['loginID']) && empty($_SESSION['password']) && !empty($_POST['password']))
{
	$_SESSION['loginID']=$_POST['loginID'];
	$_SESSION['password']=$_POST['password'];
}
else
{
	$_SESSION['loginID']=$FS_CONFIG['family_search_username'];
	$_SESSION['password']=$FS_CONFIG['family_search_password'];
}
$proxy=new FamilySearchProxy($FS_CONFIG['family_search_url'], $_SESSION['loginID'], $_SESSION['password'], $FS_CONFIG['family_search_key']); // Creates a new instance of the FamilySearchProxy and provides the login information from the config.php or the input boxes if they were used.

function getXML()
{
	global $proxy;
	global $FS_CONFIG;
	if(!empty($_SESSION['fsID'])) // Verifies that if the session variable for the requested record is present it gets the XML data from FamilySearch
	{
		$xml=$proxy->getPersonById($_SESSION['fsID']);
		$text=htmlentities($xml);  // Some characters have special meaning within HTML.  This converts those characters so they how up properly on the page instead of being handled as the html characters
		$body=preg_replace('/&gt;&lt;/', '&gt;&#13;&lt;', $text); // regular expression to replace the >< combo by placing a return between them.  Makes it easier to read on the screen as each element is on a seperate line
	}
	return $body;
}

function saveXML() // This function is broken out so that it can be called from different areas as needed to get the latest information
{
	global $proxy; // Allows access to the $proxy variable we declared at the begining of the file
	global $results; // Allows access to the $results variable we declared at the beginning of the file

	if(!empty($_REQUEST['save']) && $_REQUEST['save']=='Save Changes') // Verifies that the save request isn't empty and that the requested action is to 'Save Changes' so that this only executes when we choose
	{
		$newXML=$_REQUEST['changedXML']; // This retrieves the information, including the changes, from the text area and assigns it to a variable
		$ret = $proxy->updatePerson($_SESSION['fsID'], $newXML); // This performs the update by passing in the record ID and the edited XML file and receives the results back from the updatePerson function
		$results= htmlentities($ret); // Replaces the HTML specific characters with text versions so it will display properly.
	}
}

if (!empty($_REQUEST['action']) && $_REQUEST['action']=='saveXML') // This verifies that the save XML button was clicked and if it was, it calls the save function
{
	saveXML();
}
?>
<html>
<head>
</head>
<body>
This sample application shows how the PHP FSAPI to update information
within the FamilySearch database. There are several things that you need
before using the full capabilities of this program:
<ol>
	<li>Account on FamilySearch development server
	(http://ref.dev.usys.org/). &nbsp; If you do not have one, leave the name and password blank.</li>
	<li>Number of a record that you have created on the FamilySearch
	development server. You can use KW3B-KS2, but you will not be able to
	save changes.  Use KW31-8YH, which allows saving the changes with default login information</li>
	<li>Persistent connection to the Internet</li>
</ol>
<p />


<table>
	<tr>
		<td colspan="2">
		<!-- This form is the entry point for retrieving the XML record -->
		<form action="editXMLRecords.php" method="post">
		Login ID: <input type="text" name="loginID" /> &nbsp; Password: <input type="password" name="password" /> &nbsp; Record ID: <input type="text" name="id" />
		</td>
	</tr>
	<tr>
		<td valign="top">
		<center><input type="submit" value="Get XML Record" /></center>
		</form>
		</td>
		<td valign="top">
		<center><?php 
		if(!empty($results)) // We don't want to see any indication of the results until we have any.  This is minor and I chose this route to help reduce server processing time.
		{
			print "Results of change:"; // The column header text for the change status.
		}
		?></center>
		</td>
	</tr>
	<tr>
		<td valign="top">
		<form action="editXMLRecords.php" method="post"><textarea name="changedXML" rows="38" cols="80"><?php
		if(!empty($_SESSION['fsID'])) // Makes sure that we have some information to work with beforerunning the code.
		{
			echo preg_replace('/statusMessage=&quot;OK&quot; statusCode=&quot;200&quot;/','', getXML()); // This removes the text that causes errors when you attempt to save the XML.  It also displays the results from the request within the editable text area of the web page.
		}
?></textarea><br />
		<center><input type="hidden" name="action" value="saveXML" /> <!-- A hidden field that is used to trigger the save XML function -->
		<input type="submit" name="save" value="Save Changes" /></center>
		
		</td>
		<td valign="top" border="3"><?php
		if(!empty($_REQUEST['save']) && $_REQUEST['save']=='Save Changes')
		{
			echo preg_replace('/&gt;&lt;/', '&gt;<br/>&lt;', $results); // This displays the results of the update after inserting a carriage return between the > and <.  It makes it easier to read on the screen when the elements are seperated out
			//echo $results; // this can be used if you want to see the full message as it is returned from the familysearch update function.  It will keep everything as a single line of text that will wrap as needed
		}
		?></td>
	</tr>
	<tr>
		<td>
		</form>
		</td>
	</tr>
</table>
</body>
</html>
