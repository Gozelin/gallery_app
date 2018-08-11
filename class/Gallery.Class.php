<?php

include ("JsonHelper.Class.php");
include ("Image.Class.php");

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
			if (is_numeric($details))
				$details = intval($details);
			if (cJsonHelper::isJson($details)) {
				$details = json_decode($details, true);
			}
			if (is_numeric($details)) {
				$this->importJson($details);
			}
			if (is_array($details)) {
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
							$this->importImage($detail);
							break;
						case "order":
							$this->_order = $detail;
							break;
						case "id":
							$this->_jsonId = intval($detail);
							break;
					}
				}
			}
		}
	}

	/*
	PUBLIC FUNCTION
	*/

	public function build($order = NULL, $edit = false) {
		$image = isset($this->_image[$this->_coverImage]) ? $this->_image[$this->_coverImage]->getPath() : "image/default.png";
		if ($edit) {
			$edit = "<div class='galleryEdit-btn'>
						<h3>EDIT</h3>
					</div>";
		} else {
			$edit = "";
		}
		$order = ($this->_order === NULL) ? $order : $this->_order; 
		$str = "<div id='g".$this->_jsonId."' class='galleryBox' style='order:".$order."'>
					<div class='galleryDisplay'>
						<img width='150px' height='150px' src=".$image.">
						<h3>".$this->_name."</h3>
						".$edit."
					</div>
				</div>";
		echo $str;
	}

	public function getForm() {
		$str = "<div id='galleryForm'>";
		$str .= "<form id='gDataForm' class='regForm'>
					<input name='name' type='text' placeholder='titre' value='".$this->_name."'>
					<input name='desc' type='text' placeholder='description' value='".$this->_desc."'>
					".$this->getImageForm(1)."
				</form>";
		$str .= "<form id='gFileForm' class='fileForm'>
					".$this->getImageForm(0)."
				</form>";
		$str .= "<div id='galleryForm-btn'><h3>SUBMIT</h3></div>
				<div id='galleryForm-del'><h1>X</h1></div></div>";
		echo $str;
	}

	private function getImageForm($e = 0) {
		$str = "<form class='imgForm'>";
		$appId = 0;
		foreach ($this->_image as $img) {
			$str .= $img->getForm($e, $appId++);
		}
		$i = new cImage();
		$str .= $i->getForm($e, 0); 
		$str .= "</form>";
		return $str;
	}

	private function importImage($data) {

		if (is_array($data)) {
			$img = new cImage($data);
			$this->addImage($img);
		}
	}

	public function delete() {
		if (is_array($this->_image)) {
			foreach ($this->_image as $img) {
				$img->delete();
			}
		}
		$this->deleteJson();
	}
}

function buildApp($edit = 0) {
	$order = 1;
	if ($edit)
		echo "<div id='galleryEdit-form'></div>";
	echo "<div id='galleryWrapper'>";
	if ($edit) {
		$g = new cGallery(["name"=>"add"]);
		$g->build(-1, $edit);
	}
	$gallery = new cGallery();
	$garr = $gallery->fetchAll();
	if (is_array($garr)) {
		foreach ($garr as $g) {
			echo $g->build($order, $edit);
			$order++;
		}
		echo "</div>";
	}
}

?>