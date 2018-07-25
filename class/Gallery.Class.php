<?php

include ("JsonHelper.Class.php");

function buildGallery($edit = 0) {
	$gallery = new cGallery();
	$garr = $gallery->fetchAll();
	if (is_array($garr)) {
		if ($edit)
			echo "<div id='galleryEdit-form'></div>";
		echo "<div id='galleryWrapper'>";
		$order = 1;
		foreach ($garr as $g) {
			echo $g->build($order, $edit);
			$order++;
		}
		echo "</div>";
	}
}

class cGallery extends cJsonHelper {

	/*
	ATTRIBUTS
	*/

	//string
	protected $_name = NULL;

	//array
	protected $_image = array();

	//string
	protected $_desc = NULL;

	//int
	protected $_coverImage = 0;

	//int
	//display order in app
	protected $_order = NULL;

	/*
	ACCESSORS
	*/

	public function getName() { return $this->_name; }
	public function setName($value) { $this->_name = $value; }

	public function getDesc() { return ($this->_desc); }
	public function setDesc( $value) { $this->_desc = $value; }

	public function getImage($no) {
		if (isset($this->_image[$no]))
			return ($this->_image[$no]);
		return (NULL);
	}
	public function addImage($value) {
		if (is_object($value) && get_class($value) == "cImage") {
			array_push($this->_image, $value);
		}
	}
	public function delImage($no) {
		if (isset($this->_image) && is_array($this->_image) && isset($this->_image[$no])) {
			unset($this->_image[$no]);
		}
	}

	public function getGallery() {
		return ($this->_image);
	}

	/*
	CONSTRUCTOR
	*/

	public function __construct($details = NULL) {
		if($details != NULL)
		{
			if (cJsonHelper::isJson($details)) {
				echo ("param is json");
				$this->importJson($details);
			} else if (is_array($details)) {
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
						case "image":
							$this->_image = $detail;
							break;
						case "order":
							$this->_order = $detail;
							break;
					}
				}
			} else {
				echo("bad parameters: send array or json string");
			}
		}
	}

	/*
	PUBLIC FUNCTION
	*/

	public function build($order = NULL, $edit = false) {
		if ($edit)
			$edit = "<div class='galleryEdit-btn'>
						<h3>EDIT</h3>
					</div>";
		$order = ($this->_order === NULL) ? $order : $this->_order; 
		$str = "<div id='g".$this->_jsonId."' class='galleryBox' style='order:".$order."'>
					<div class='galleryDisplay'>
						<img width='150px' height='150px' src=".$this->_image[$this->_coverImage]->getPath().">
						<h3>".$this->_name."</h3>
						".$edit."
					</div>
				</div>";
		return ($str);
	}
}

?>