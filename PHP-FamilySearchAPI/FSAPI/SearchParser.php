<?php
if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

class SearchParser {
	var $xml_parser;
	var $result_key = "*FAMILYTREE*SEARCHES*SEARCH";
	var $person_key = "*FAMILYTREE*SEARCHES*SEARCH*PERSON";
	var $name_key = "*FAMILYTREE*SEARCHES*SEARCH*PERSON*NAME*FORM*FULLTEXT";
	var $score_key = "*FAMILYTREE*SEARCHES*SEARCH*SCORE";
	var $event_key = "*FAMILYTREE*SEARCHES*SEARCH*PERSON*EVENTS*EVENT";
	var $date_key = "*FAMILYTREE*SEARCHES*SEARCH*PERSON*EVENTS*EVENT*DATE*ORIGINAL";
	var $place_key = "*FAMILYTREE*SEARCHES*SEARCH*PERSON*EVENTS*EVENT*PLACE*ORIGINAL";
	var $gender_key = "*FAMILYTREE*SEARCHES*SEARCH*PERSON*GENDER";
	
	var $Pperson_key = "*FAMILYTREE*SEARCHES*SEARCH*PARENTS*PARENT";
	var $Pname_key = "*FAMILYTREE*SEARCHES*SEARCH*PARENTS*PARENT*NAME*FORM*FULLTEXT";
	var $Pevent_key = "*FAMILYTREE*SEARCHES*SEARCH*PARENTS*PARENT*EVENTS*EVENT";
	var $Pdate_key = "*FAMILYTREE*SEARCHES*SEARCH*PARENTS*PARENT*EVENTS*EVENT*DATE*ORIGINAL";
	var $Pplace_key = "*FAMILYTREE*SEARCHES*SEARCH*PARENTS*PARENT*EVENTS*EVENT*PLACE*ORIGINAL";
	var $Pgender_key = "*FAMILYTREE*SEARCHES*SEARCH*PARENTS*PARENT*GENDER";
	
	var $Sperson_key = "*FAMILYTREE*SEARCHES*SEARCH*SPOUSES*SPOUSE";
	var $Sname_key = "*FAMILYTREE*SEARCHES*SEARCH*SPOUSES*SPOUSE*NAME*FORM*FULLTEXT";
	var $Sevent_key = "*FAMILYTREE*SEARCHES*SEARCH*SPOUSES*SPOUSE*EVENTS*EVENT";
	var $Sdate_key = "*FAMILYTREE*SEARCHES*SEARCH*SPOUSES*SPOUSE*EVENTS*EVENT*DATE*ORIGINAL";
	var $Splace_key = "*FAMILYTREE*SEARCHES*SEARCH*SPOUSES*SPOUSE*EVENTS*EVENT*PLACE*ORIGINAL";
	var $Sgender_key = "*FAMILYTREE*SEARCHES*SEARCH*SPOUSES*SPOUSE*GENDER";
	
	var $Cperson_key = "*FAMILYTREE*SEARCHES*SEARCH*CHILDREN*CHILD";
	var $Cname_key = "*FAMILYTREE*SEARCHES*SEARCH*CHILDREN*CHILD*NAME*FORM*FULLTEXT";
	var $Cevent_key = "*FAMILYTREE*SEARCHES*SEARCH*CHILDREN*CHILD*EVENTS*EVENT";
	var $Cdate_key = "*FAMILYTREE*SEARCHES*SEARCH*CHILDREN*CHILD*EVENTS*EVENT*DATE*ORIGINAL";
	var $Cplace_key = "*FAMILYTREE*SEARCHES*SEARCH*CHILDREN*CHILD*EVENTS*EVENT*PLACE*ORIGINAL";
	var $Cgender_key = "*FAMILYTREE*SEARCHES*SEARCH*CHILDREN*CHILD*GENDER";
	
	var $people_array = array();
	
	var $counter = 0;
	var $contents = "";
	var $current_person = null;
	var $current_parent = null;
	var $current_spouse = null;
	var $current_child = null;
	var $current_attrs = null;
	var $current_tag = "";
	
	function startElement($parser, $data, $attrs){
		$this->current_tag .= "*$data";
		//print "<b>".$this->current_tag."</b><br />\n";
		if ($this->current_tag==$this->event_key) $this->current_attrs = $attrs;
		if ($this->current_tag==$this->result_key) {
			$this->current_person = new SearchPerson();
			$this->current_person->id = $attrs['REF'];
		}
		if ($this->current_tag==$this->Pperson_key) {
			$this->current_parent = new SearchPerson();
			$this->current_person->id = $attrs['REF'];
		}
		if ($this->current_tag==$this->Sperson_key) {
			$this->current_spouse = new SearchPerson();
			$this->current_person->id = $attrs['REF'];
		}
		if ($this->current_tag==$this->Cperson_key) {
			$this->current_child = new SearchPerson();
			$this->current_person->id = $attrs['REF'];
		}
		$this->contents = "";
	}

	function endElement($parser, $data){

		$tag_key = strrpos($this->current_tag, '*');
		$this->current_tag = substr($this->current_tag, 0, $tag_key);
		//	print_r($current_attrs);
		$tempTag = $this->current_tag."*".$data;
		//print $tempTag.": ".$this->contents."<br />\n";

		if ($tempTag==$this->name_key) $this->current_person->personName = $this->contents;
		if ($tempTag==$this->score_key) $this->current_person->score = $this->contents;
		if ($tempTag==$this->date_key && $this->current_attrs['TYPE']=="Birth") $this->current_person->birthDate = $this->contents;
		if ($tempTag==$this->place_key && $this->current_attrs['TYPE']=="Birth") $this->current_person->birthPlace = $this->contents;
		if ($tempTag==$this->date_key && $this->current_attrs['TYPE']=="Death") $this->current_person->deathDate = $this->contents;
		if ($tempTag==$this->place_key && $this->current_attrs['TYPE']=="Death") $this->current_person->deathPlace = $this->contents;
		if ($tempTag==$this->date_key && $this->current_attrs['TYPE']=="Marriage") $this->current_person->marriageDate = $this->contents;
		if ($tempTag==$this->place_key && $this->current_attrs['TYPE']=="Marriage") $this->current_person->marriagePlace = $this->contents;
		if ($tempTag==$this->gender_key) $this->current_person->gender = $this->contents;
		
		if ($tempTag==$this->Pname_key) $this->current_parent->personName = $this->contents;
		if ($tempTag==$this->Pdate_key && $this->current_attrs['TYPE']=="Birth") $this->current_parent->birthDate = $this->contents;
		if ($tempTag==$this->Pplace_key && $this->current_attrs['TYPE']=="Birth") $this->current_parent->birthPlace = $this->contents;
		if ($tempTag==$this->Pdate_key && $this->current_attrs['TYPE']=="Death") $this->current_parent->deathDate = $this->contents;
		if ($tempTag==$this->Pplace_key && $this->current_attrs['TYPE']=="Death") $this->current_parent->deathPlace = $this->contents;
		if ($tempTag==$this->Pdate_key && $this->current_attrs['TYPE']=="Marriage") $this->current_parent->marriageDate = $this->contents;
		if ($tempTag==$this->Pplace_key && $this->current_attrs['TYPE']=="Marriage") $this->current_parent->marriagePlace = $this->contents;
		if ($tempTag==$this->Pgender_key) $this->current_parent->gender = $this->contents;
		
		if ($tempTag==$this->Sname_key) $this->current_spouse->personName = $this->contents;
		if ($tempTag==$this->Sdate_key && $this->current_attrs['TYPE']=="Birth") $this->current_spouse->birthDate = $this->contents;
		if ($tempTag==$this->Splace_key && $this->current_attrs['TYPE']=="Birth") $this->current_spouse->birthPlace = $this->contents;
		if ($tempTag==$this->Sdate_key && $this->current_attrs['TYPE']=="Death") $this->current_spouse->deathDate = $this->contents;
		if ($tempTag==$this->Splace_key && $this->current_attrs['TYPE']=="Death") $this->current_spouse->deathPlace = $this->contents;
		if ($tempTag==$this->Sdate_key && $this->current_attrs['TYPE']=="Marriage") $this->current_spouse->marriageDate = $this->contents;
		if ($tempTag==$this->Splace_key && $this->current_attrs['TYPE']=="Marriage") $this->current_spouse->marriagePlace = $this->contents;
		if ($tempTag==$this->Sgender_key) $this->current_spouse->gender = $this->contents;
		
		if ($tempTag==$this->Cname_key) $this->current_child->personName = $this->contents;
		if ($tempTag==$this->Cdate_key && $this->current_attrs['TYPE']=="Birth") $this->current_child->birthDate = $this->contents;
		if ($tempTag==$this->Cplace_key && $this->current_attrs['TYPE']=="Birth") $this->current_child->birthPlace = $this->contents;
		if ($tempTag==$this->Cdate_key && $this->current_attrs['TYPE']=="Death") $this->current_child->deathDate = $this->contents;
		if ($tempTag==$this->Cplace_key && $this->current_attrs['TYPE']=="Death") $this->current_child->deathPlace = $this->contents;
		if ($tempTag==$this->Cdate_key && $this->current_attrs['TYPE']=="Marriage") $this->current_child->marriageDate = $this->contents;
		if ($tempTag==$this->Cplace_key && $this->current_attrs['TYPE']=="Marriage") $this->current_child->marriagePlace = $this->contents;
		if ($tempTag==$this->Cgender_key) $this->current_child->gender = $this->contents;
		
		if ($tempTag==$this->Sperson_key) $this->current_person->spouses[] = $this->current_spouse; 
		if ($tempTag==$this->Pperson_key) $this->current_person->parents[] = $this->current_parent;
		if ($tempTag==$this->Cperson_key) $this->current_person->children[] = $this->current_child;
		
		if ($tempTag==$this->result_key) {
			$this->people_array[] = $this->current_person;
		}
		$this->contents = "";
	}

	function innerData($parser, $data){
		$this->contents .= $data;
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
		return $this->people_array;
	}
}

class SearchPerson {
	var $personName;
	var $score;
	var $birthDate;
	var $birthPlace;
	var $deathDate;
	var $deathPlace;
	var $marriageDate;
	var $marriagePlace;
	var $gender;
	var $version;
	var $id;
	var $spouses = array();
	var $parents = array();
	var $children = array();
	
	/**
	 * get the person's sex image
	 * @return string 	<img ... />
	 */
	function getSexImage($style='') {
		global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;
		if ($this->gender=="Male") $s = "sex";
		else if ($this->gender=="Female") $s = "sexf";
		else $s = "sexn";
		$temp = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES[$s]["small"]."\" alt=\"\" class=\"gender_image\"";
		if (!empty($style)) $temp .= " style=\"$style\"";
		$temp .= " />";
		return $temp;
	}
	
	/**
	 * Build the SearchPerson from an XG_Person
	 *
	 * @param XG_Person $xperson
	 * @param XmlGedcom $XMLGed
	 */
	function buildFromXG(&$xperson, &$XMLGed, $type="full") {
		//-- get the persons birth and death information
		$birthDate = "";
		$deathDate = "";
		$birthPlace = "";
		$deathPlace = "";
		$assert = $xperson->getBirthAssertion();
		if (!is_null($assert)) {
			$date = $assert->getDate();
			if (!is_null($date)) $birthDate = $date->getNormalized();
			$place = $assert->getPlace();
			if (!is_null($place)) $birthPlace = $place->getNormalized();
		}
		$assert = $xperson->getDeathAssertion();
		if (!is_null($assert)) {
			$date = $assert->getDate();
			if (!is_null($date)) $deathDate = $date->getNormalized();
			$place = $assert->getPlace();
			if (!is_null($place)) $deathPlace = $place->getNormalized();
		}
		
		$this->id = $xperson->getID();
		$this->birthDate = $birthDate;
		$this->version= $xperson->getVersion();
		$this->birthPlace = $birthPlace;
		$this->personName = $xperson->getPrimaryName()->getFullText();
		$this->deathDate = $deathDate;
		$this->deathPlace = $deathPlace;
		$this->gender = $xperson->getGender()->getGender();
		
		if ($type=="full") {
			$spouses = $xperson->getSpouses();
			/* @var $spouse XG_PersonRef */
			foreach($spouses as $spouse) {
				$id = $spouse->getRef();
				$sp = $XMLGed->getPerson($id);
				$temp = new SearchPerson();
				$temp->buildFromXG($sp, $XMLGed, 'spouse');
				$this->spouses[] = $temp;
			}
			
			$parents = $xperson->getParents();
			/* @var $spouse XG_PersonRef */
			foreach($parents as $parent) {
				$id = $parent->getRef();
				$sp = $XMLGed->getPerson($id);
				$temp = new SearchPerson();
				$temp->buildFromXG($sp, $XMLGed, 'parent');
				$this->parents[] = $temp;
			}
			
			// TODO get the children
		}
	}
}
?>