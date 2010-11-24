<?php
/**
 * Check for name in the result XML
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

class NameParser {
	var $name = array();
	var $piece = array();
	var $groups = array();
	var $group = array();
	var $item = array();
	var $xml_parser;
	var $insideitem = false;
	var $tag = "";
	var $type = "";
	
	//getters and setters
	function getName() {
		return $this->name;
	}
	
	function getPiece() {
		return $this->piece;
	}
	
	function getType() {
		return $this->type;
	}
	
	function getGroups() {
		return $this->groups;
	}
	
	function getGroup() {
		return $this->group;
	}
	
	function getItem() {
		return $this->item;
	}
	
	function setName($name) {
		$this->name = $name;
	}
	
	function setPiece($piece) {
		$this->piece = $piece;
	}
	
	function setType($type) {
		$this->type = $type;
	}
	
	function setGroups($groups) {
		$this->groups = $groups;
	}
	
	function setGroup($group) {
		$this->group = $group;
	}
	
	function setItem($item) {
		$this->item = $item;
	}

	/**
	 * recieve gedcom record
	 */
	function getGedcom() {
		$gedcom = "";
		if(!empty($this->name)) {
			$gedcom = "".$this->item;
			$gedcom.="\r\n";
		}
		return $gedcom;
	}
	
	/**
	 * check for these inner tags
	 */
	function innerData($parser, $data) {
		if($this->insideitem) {
			switch($this->tag) {
				case "NAME":
					$this->name[] = $data;
					break;
				case "PIECE":
					$this->piece[] = $data;
					break;
				case "GROUPS":
					$this->groups[] = $data;
					break;
				case "GROUP":
					$this->group[] = $data;
					break;
				case "ITEM":
					$this->item[] = $data;
					break;
			}
		}
	}
	
	/**
	 * only interested if a names tag is found
	 */
	function startElement($parser, $tagName, $attrs) {
		if ($this->insideitem) {
			$this->tag = $tagName;
		} 
		else if ($tagName == "NAMES"){
			$this->insideitem = true;
		}
	}
	
	/**
	 * checks if this is the last tag
	 */
	function endElement($parser, $tagName) {
		if ($tagName == "NAMES") {
			$this->insideitem = false;
		}
	}
	
	/**
	 * read the input xml and parse for any names, return the names if found
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
		if(!empty($this->name)) {
			return $this->item;
		}
	}
}
?>