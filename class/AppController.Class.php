<?php

class cAppController {

    /*
    ATTRIBUTS
    */

    //string
    protected $_action = NULL;
    
    //string
    protected $_class = NULL;

    //array or json
    protected $_data = NULL;

    // object
    protected $_obj = NULL;

    public function __construct($detail = null) {
        if (!is_array($detail))
            return (0);
        $this->fetchAttr($detail);
    }

    private function fetchAttr($detail) {
        foreach ($detail as $key => $value) {
            $attr = "_".$key;
            if (property_exists($this, $attr))
                $this->$attr = $value;
        }
	}
	
	public function prepareQuery($arr) {
		if (is_array($arr))
            $this->fetchAttr($arr);
        else if (cJsonHelper::isJson($arr)) {
            $this->fetchAttr(json_decode($arr));
        }
    }
    
    public function prepareData($arr) {
        if (isset($arr["data"])) {
            $arr["data"] = json_decode($arr["data"], true);
            if (is_array($arr["data"])) {
                foreach ($arr["data"] as $key => $value) {
                    $str = explode("-", $key);
                    if (isset($str[1]) && $str[0] == "file") {
                        $name = $_FILES[$arr["data"][$key]["class"]."-".$str[1]]["name"];
                        $arr["data"][$arr["data"][$key]["attr"]] = $arr["data"][$key];
                        $arr["data"][$arr["data"][$key]["attr"]]["path"] = $arr["data"][$key]["dir"].$name;
                        move_uploaded_file($_FILES[$arr["data"][$key]["class"]."-".$str[1]]["tmp_name"], "../image/".$name);
                        unset($arr["data"][$key]);
                    }
                }
            }
        }
        return ($arr);
    }

    public function execQuery() {
        if (isset($this->_class) && class_exists($this->_class)) {
            $ret = NULL;
            $this->_obj = new $this->_class($this->_data);
            if (isset($this->_action)) {
                if (method_exists($this->_obj, $this->_action)) {
                    $function = $this->_action;
                    $ret = $this->_obj->$function($this->_data);
                }
            }
            return ($ret);
        } else if (isset($this->_action) && function_exists($this->_action)) {
			$function = $this->_action;
			$function($this->_data);
		}
    }
}

?>