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

    //object
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