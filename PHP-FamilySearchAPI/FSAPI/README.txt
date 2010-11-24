=======================================================
    PHP FamilySearch API Client

    Version 0.92a
    Copyright (C) 2007 Neumont University

=======================================================

CONTENTS
     1.  LICENSE
     2.  SYSTEM REQUIREMENTS
     3.  USERS GUIDE
     4.  CHANGELOG

-------------------------------------------------------
LICENSE

This code was created by students from Neumont University under the 
instruction of John Finlay. Questions and comments regarding this code
may be directed to him at john.finlay@neumont.edu.

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
Lesser General Public License for more details.

See LICENSE.txt for the full license.  If you did not receive a 
copy of the license with this code, you may find a copy online
at http://www.opensource.org/licenses/lgpl-license.php

-------------------------------------------------------
SYSTEM REQUIREMENTS

This library requires PHP 4.3 or higher.  It should work on all standard 
distributions of PHP.  SSL connections are possible, but PHP must be compiled
with OpenSSL support. For more information on how to enable OpenSSL in PHP,
refer to: http://www.php.net/manual/en/ref.openssl.php 

This library uses the PEAR HTTP and PEAR Net libraries.  For convenience
these libraries have been included with the project.  Documentation and
other information about these libraries can be found online at the PEAR
home page: http://pear.php.net

-------------------------------------------------------
USERS GUIDE

This library provides a PHP proxy client for the FamilySearch API
which anyone may use to interface with the FamilySearch API. This
proxy class will return the raw XML result from the FamilySearch 
API.

To use this proxy class you must include the FamilySearchProxy.php
file in your script and then make an instance of the class.  You 
may then call the methods of the class to interface with the
FamilySearch API.  API documentation for all of the proxy methods
can be found in the code for the FamilySearchProxy.php.

The following script shows an example of obtaining a person record

<?php 
include_once('FamilySearchProxy.php');

//-- setup authentication credentials
$url = 'http://ref.dev.usys.org';
$username = 'username';
$password = 'password';
$key = '1234567890';

//-- ID of the person record to obtain
$id = 'me';

//-- extra parameters to constrain your request
$query = 'view=full&citations=false&notes=false';

//-- If there are errors coming back from the request, when 
//--- $parseError set to false, the error will be in plain text, 
//--- otherwise it will be in XML format.
$parseError = true;

//--create a new object of FamilySearchProxy
$proxy = new FamilySearchProxy($url, $username, $password, $key);

//call the desire method
$response = $proxy->getPersonById($id, $query, $parseError);
print htmlentities($response);

?>

The FamilySearchTester.php file contains several tests of the 
FamilySearch API using this proxy class.  To run this test open 
FamilySearchTester.php and set the required login for 
FamilySearch. Browse to the FamilySearchTester.php in your 
browser to see the results of the tests.

** About Developer Keys **
FamilySearch requires a Developer Key to login to the system.  This
developer key must be obtained from FamilySearch.  You may provide
the key as a parameter of the FamilySearchProxy constructor or if you
do not provide a key, the code will look for your key in a file in the 
current directory called "familysearchdev.key".  It is recommended that
you protect your the .key file with a .htaccess restriction or similar
measures.

-------------------------------------------------------
CHANGELOG

Version 1.01 - June 26, 2007
- Updated PEAR:HTTP and PEAR:Net libraries to the latest versions
- Implemented cookie handling for multiple requests so that authentication 
  need not be performed with every request

=======================================================
