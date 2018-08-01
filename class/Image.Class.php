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

	/*
	PUBLIC FUNCTIONS
	*/

	public function getDataForm($e = 0) {
		if (!$e) {
			$str = "<input name='cImage-".$this->_jsonId."' class='imgFileInput' type='file'>";
		} else {
			$str = "<div class='file-".$this->_jsonId."'>
						<input type='text' name='name' value='".$this->_name."'>
						<input type='text' name='desc' value='".$this->_desc."'>
						<input type='hidden' name='id' value='".$this->_jsonId."'>
						<input type='hidden' name='class' value='cImage'>
					</div>";
		}
		return ($str);
	}

}

?>