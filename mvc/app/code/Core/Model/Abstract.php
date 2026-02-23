<?php


class Core_Model_Abstract
{
    protected $_data = [];


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

        // echo "Method $method not found!";
    }


    public function __get($key)
    {
        return $this->_data[$key];
    }
    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }
    public function addData($data = [])
    {
        $this->_data = $data;

        return $this;
    }
    public function load($value,$field = null){
        $query = "SELECT* FROM catalog_product";
        // $this->fetchOne();

        $mySql = Sdp::getModel("core/connection_mysql");

        $data = $mySql->fetchOne();
        $this->_data = $data;
    }

   


}
