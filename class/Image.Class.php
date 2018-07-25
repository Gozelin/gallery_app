<?php

include_once("JsonHelper.Class.php");

class cImage extends cJsonHelper {

	/*
	ATTRIBUT
	*/

	//string
	protected $_name = NULL;

	//string
	protected $_desc = NULL;

	//string
	protected $_path = NULL;

	/*
	ACCESSORS
	*/

	public function getName() { return $this->_name; }
	public function setName($value) { $this->_name = $value; }

	public function getDesc() { return ($this->_desc); }
	public function setDesc( $value) { $this->_desc = $value; }

	public function getPath() { return $this->_path; }
	public function setPath( $value) { $this->_path = $value; }

	/*
	CONSTRUCTOR
	*/

	public function __construct($details = NULL) {
		if($details != NULL)
		{
			foreach($details as $key => $detail)
			{
				switch(strtolower($key))
				{
					case "name":
						$this->_name = $detail;
						break;
					case "desc":
						$this->_desc = $detail;
						break;
					case "path":
						$this->_path = $detail;
						break;
				}
			}
		}
	}
}

?>