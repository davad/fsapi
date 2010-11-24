<?php
/**
 * Check for error in the result XML
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
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

class ErrorParser {

	var $insideitem = false;
	var $tag = "";
	var $code = "";
	var $message = "";
	var $errors = array();
	var $xml_parser;
	
	/**
	 * get the last error code that was found
	 *
	 * @return int
	 */
	function getLastCode() {
		return $this->code;
	}
	
	/**
	 * get the last error message that was found
	 *
	 * @return string
	 */
	function getLastMessage() {
		return $this->message;
	}
	
	/**
	 * get an array of all of the errors found
	 * Each element of the array is an array with elements 'code' and 'message'
	 *
	 * @return array
	 */
	function getErrorList() {
		return $this->errors;
	}

	//only interest if we recieve an error
	function startElement($parser, $tagName, $attr) {
		if ($this->insideitem) {
			$this->tag = $tagName;
		} else if ($tagName == "NS2:ERROR" or $tagName == "ERRORS"){
			$this->insideitem = true;
		}
	}

	//reset the current message and errornr
	function endElement($parser, $tagName) {
		if ($tagName == "NS2:ERROR" or $tagName == "ERRORS") {
			$this->insideitem = false;
		}
	}

	//parse the error code and the message
	function innerData($parser, $data) {
		if ($this->insideitem) {
			switch ($this->tag) {
				case "MESSAGE":
					//add the error and message to the array
					$this->message = $data;
					$this->errors[] = array($this->code, "code"=>$this->code, $data, "message"=>$data);
					break;
				case  "CODE":
					$this->code = trim($data);
					break;
			}
				
		}
	}

	/**
	 * read the input xml and parse for any errors, 
	 * return the errors if found
	 * 
	 * @param string $xml	a string of xml data
	 * @return array	an array of the error codes and messages that were found
	 */
	function parseXML($xml){
		$this->xml_parser = xml_parser_create();
		xml_set_object($this->xml_parser, $this);
		xml_set_element_handler($this->xml_parser, "startElement", "endElement");
		xml_set_character_data_handler($this->xml_parser, "innerData");

		xml_parse($this->xml_parser, $xml) or
			$this->errors[] = array(xml_get_error_code($this->xml_parser), "code"=>xml_get_error_code($this->xml_parser),
					"XML error: ".xml_error_string(xml_get_error_code($this->xml_parser))." at line ".xml_get_current_line_number($this->xml_parser)." ".__LINE__.__FILE__." ".htmlentities($xml),
					"message"=>"XML error: ".xml_error_string(xml_get_error_code($this->xml_parser))." at line ".xml_get_current_line_number($this->xml_parser)." ".__LINE__.__FILE__." ".htmlentities($xml));
		
		xml_parser_free($this->xml_parser);
		if(!empty($this->errors)){
			return $this->errors;
		}
	}
}
?>