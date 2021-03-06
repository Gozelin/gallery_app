<?php

abstract class cJsonHelper {

	//string
	//path to the db file
	private $_path_db = NULL;

	//int
	//id of the object in db file
	protected $_jsonId = 0;

	//string
	//a buffer for fc (file content)
	private $_jsonFc = NULL;

	//boolean
	//if true, error/warnings log will be DISPLAYED
	static $verbose = false;

	/*
	GET/SET
	*/

	public function getJsonId() { return ($this->_jsonId); }
	public function setJsonId($value) { $this->_jsonId = $value; }

	/*
	MISC FUNCTIONS
	*/

	private function log($str, $code = 0) {
		switch ($code) {
			case 0:
				$header = "error: ";
				break;
			case 1:
				$header = "warning: ";
				break;
			default:
				$header = "undefined: ";
		}
		if (Self::$verbose) {
			echo $header.$str."<br>";
		}
	}

	private function prepareDb() {
		$path = $_SERVER["DOCUMENT_ROOT"]."/gallery_app/db/";
		$fn = get_class($this).".db.json";
		$this->_path_db = $path.$fn;
		if (fopen($this->_path_db, 'c+')) {
			return(true);
		}
		$this->log("db not found", 0);
		return (false);
	}

	private function getDispId() {
		$id = 0;
		foreach ($this->_jsonFc as $filec ) {
			if ($filec["id"] > $id)
				$id = $filec["id"];
		}
		if ($id == NULL)
			return (1);
		return ($id + 1);
	}

	private function getFileContent() {
		$this->_jsonFc = file_get_contents($this->_path_db);
		if (!$this->_jsonFc)
			return (false);
		$this->_jsonFc = json_decode($this->_jsonFc, true);
		return (true);
	}

	private function clearDump() {
		unset($this->_jsonFc);
		$this->_jsonFc = NULL;
	}

	/*
	STATIC
	*/

	static function isJson($string) {
		if (!is_string($string))
			return (0);
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	/*
	INSERT FUNCTION
	*/

	//convert and insert the data from object into db JSON file
	public function insertJson() {
		$json = $this->expJson();
		if (!$this->prepareDb() || !$this->getFileContent()) {
			file_put_contents($this->_path_db, json_encode([["id"=>1, "json"=>$json, "class"=>get_class($this)]]));
			$this->_jsonId = 1;
		} else {
			if (is_array($this->_jsonFc)) {
				$this->_jsonId = $this->getDispId();
				array_push($this->_jsonFc, ["id"=>$this->_jsonId, "json"=>$json, "class"=>get_class($this)]);
				$this->_jsonFc = json_encode($this->_jsonFc, true);
				file_put_contents($this->_path_db, $this->_jsonFc);
			}
		}
		$this->clearDump();
	}

	//convert the object's data to JSON format
	public function expJson() {
		$reflect = new ReflectionClass(get_class($this));
		$prop = $reflect->getProperties();
		if (is_array($prop)) {
			$arr = [];
			foreach ($prop as $key => $attr) {
				if ($attr->class == get_class($this) || $attr->name == "_jsonClass") {
					$attr_data = $attr->name;
					$val = $this->expJson_parse($this->$attr_data);
					$arr[$attr->name] = $val;
				}
			}
		}
		return (json_encode($arr));
	}

	private function expJson_parse($ad) {
		$val = NULL;
		switch(gettype($ad)) {
			default:
				$val = $ad;
				break;
			case "array":
				$val = [];
				foreach ($ad as $key => $data) {
					array_push($val, $this->expJson_parse($data));
				}
				break;
			case "object":
				$val = [];
				if (is_subclass_of($ad, "cJsonHelper")) {
					$ad->insertJson();
					$val["id"] = $ad->getJsonId();
					$val["class"] = get_class($ad);
				}
		}
		return ($val);
	}

	/*
	IMPORT FUNCTION
	*/

	//import object's data from JSON file database
	public function importJson($id) {
		if (!$this->prepareDb() || !$this->getFileContent()) {
			$this->log("empty db", 0);
			return (false);
		}
		if (is_array($this->_jsonFc)) {
			$this->_jsonId = -1;
			foreach($this->_jsonFc as $filec) {
				if ($filec["id"] == $id) {
					$this->_jsonId = $id;
					$this->impJson($filec["json"], $filec["class"]);
				}
			}
			if ($this->_jsonId == -1 && Self::$verbose) {
				$this->log("can't find id", 0);
				return (false);
			}
		}
		$this->clearDump();
	}

	//import the object's data from a JSON string
	public function impJson($json, $class) {
		$arr = json_decode($json, true);
		if (is_array($arr)) {
			foreach ($arr as $key => $attr) {
				$var = "";
				if (property_exists($this, $key))
					$var = $key;
				else if (property_exists($this, "_".$key))
					$var = "_".$key;
				if ($var)
					$this->$var = $this->impJson_parse($attr, $class);
			}
		}
	}

	private function impJson_parse($ad, $class) {
		$val = NULL;
		switch (gettype($ad)) {
			default:
				$val = $ad;
				break;
			case "string":
				if (Self::isJson($ad)) {
					$val = new $class();
					$val->impJson($ad, $class);
				}
				else
					$val = $ad;
				break;
			case "array":
				if (isset($ad["class"])) {
					$val = new $ad["class"]();
					$val->importJson($ad["id"]);
				} else {
					$val = [];
					foreach ($ad as $key => $data) {
						$val[] = $this->impJson_parse($data, $class);
					}
				}
				break;
		}
		return ($val);
	}

	/*
	UPDATE FUNCTION
	*/

	//update the database with new data from an object
	public function updateJson() {
		if (!$this->prepareDb() || !$this->getFileContent()) {
			$this->log("empty db", 0);
			return (false);
		}
		if (is_array($this->_jsonFc)) {
			$found = false;
			foreach ($this->_jsonFc as $key => $fc) {
				if ((isset($this->_jsonFc[$key]["id"]) && $this->_jsonFc[$key]["id"] === $this->_jsonId)) {
					$this->_jsonFc[$key]["json"] = $this->expJson();
					$found = true;
				}
			}
			if ($found) {
				file_put_contents($this->_path_db, json_encode($this->_jsonFc));
			} else {
				$this->log("id not found in db", 0);
			}
		}
		$this->clearDump();
	}

	/*
	DELETE FUNCTION
	*/

	public function deleteJson() {
		if (!$this->prepareDb() || !$this->getFileContent()) {
			return (false);
		}
		if (is_array($this->_jsonFc)) {
			foreach ($this->_jsonFc as $key => $value) {
				if (isset($this->_jsonFc[$key]["id"]) && $this->_jsonFc[$key]["id"] == $this->_jsonId) {
					unset($this->_jsonFc[$key]);
					file_put_contents($this->_path_db, json_encode($this->_jsonFc));
				}
			}
		}
		$this->clearDump();
	}

	public function fetchAll() { 
		if (!$this->prepareDb() || !$this->getFileContent()) {
			$this->log("empty db", 0);
			return (false);
		}
		$arr =[];
		if (is_array($this->_jsonFc)) { 
			foreach ($this->_jsonFc as $elem) {
				$obj = new $this();
				$obj->_jsonId = $elem["id"];
				$obj->impJson($elem["json"], $elem["class"]);
				$arr[] = $obj;
			}
		}
		$this->clearDump();
		return ($arr);
	}
}

?>