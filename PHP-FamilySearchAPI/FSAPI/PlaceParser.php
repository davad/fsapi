<?php
/**
 * Check for normalized form of a place in result XML
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
 * @author Joseph Phalouka
 */
if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

class PlaceParser {
	var $official = array();
	var $normalized = array();
	var $place = array();
	var $insideitem = false;
	var $xml_parser;
	var $tag = "";
	
	//getters and setters
	function getOriginal() {
		return $this->official;
	}

	function getNormalized() {
		return $this->normalized;
	}

	function setOriginal($orig) {
		$this->official = $orig;
	}

	function setNormalized($normalized) {
		$this->normalized = $normalized;
	}

	/**
	 * recieve gedcom record
	 */
	function getGedcom() {
		$gedcom = "";
		if (!empty($this->original)){
			$gedcom="2 PLAC ".$this->normalized;
			$gedcom.="\r\n";
		}
		return $gedcom;
	}

	/**
	 * check for these inner tags
	 */
	function innerData($parser, $data) {
		if ($this->insideitem) {
			switch ($this->tag) {
				case "PLACE";
					$this->place[] = $data;
					break;
				case "OFFICIAL":
					//add the official place to the array
					$this->official[] = $data;
					break;
				case "NORMALIZED":
					//add the normalized form of place to the array
					$this->normalized[] = $data;
					break;
			}
		}
	}

	/**
	 * only interested if a place tag is found
	 */
	function startElement($parser, $tagName, $attrs) {
		if ($this->insideitem) {
			$this->tag = $tagName;
		} 
		else if ($tagName == "PLACES"){
			$this->insideitem = true;
		}
	}

	/**
	 * checks if this is the last tag
	 */
	function endElement($parser, $tagName) {
		if ($tagName == "PLACES") {
			$this->insideitem = false;
		}
	}

	/**
	 * read the input xml and parse for any places, return the places if found
	 */
	function parseXml($xml) {
		$this->xml_parser = xml_parser_create();
		xml_set_object($this->xml_parser, $this);
		xml_set_element_handler($this->xml_parser, "startElement", "endElement");
		xml_set_character_data_handler($this->xml_parser, "innerData");

		xml_parse($this->xml_parser, $xml) or die(sprintf("XML error: %s at line %d ".__LINE__.__FILE__." ".htmlentities($xml),
		xml_error_string(xml_get_error_code($this->xml_parser)),
		xml_get_current_line_number($this->xml_parser)));

		xml_parser_free($this->xml_parser);
		if(!empty($this->original)) {
			return $this->normalized;
		}
	}
}
?>