<?php
class Core_Model_Abstract
{


    protected $_data = [];
    protected $_resource = null;

    public function _init($resource)
    {
        $this->_resource = Sdp::getResourceModel($resource);
    }
    public function __call($method, $args)
    {

        if (substr($method, 0, 3) == "set") {
            $property = strtolower(substr($method, 3));
            $this->$property = $args[0];
            return;
        }

        if (substr($method, 0, 3) == "get") {
            $property = strtolower(substr($method, 3));
            return $this->$property ?? null;
        }
    }
    public function __construct() {}
    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }
    public function __get($name)
    {
        return $this->_data[$name];
    }
    public function addData($data = [])
    {
        $this->_data = $data;
        return $this;
    }
    public function load($value, $field = null)
    {

        $data = $this->getResource()->load($this, $value, $field);

        $this->_data = $data;
    }
    public function getResource()
    {
        return $this->_resource;
    }

    public function isEmpty()
    {
        return empty($this->_data);
    }
}
