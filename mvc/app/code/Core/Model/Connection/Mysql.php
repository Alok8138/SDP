<?php

class Core_Model_Connection_Mysql
{

    protected $_connection = null;
    public function __construct()
    {
        $this->connect();
    }

    public function connect()
    {
        if (is_null($this->_connection)) {



            $this->_connection = new mysqli("localhost", "root", "", "mvc");
        }

        if ($this->_connection->connect_error) {
            die("connection failed: " . $this->_connection->connect_error);
        }
    }


    public function fetchOne()
    {


        // if ($conn->connect_error) {
        //     die("Connection failed: " . $conn->connect_error);
        // }

        $sql = "SELECT * FROM catalog_product";
        $result = $this->_connection->query($sql);

        while ($row = $result->fetch_assoc()) {
            // print_r($row);
            return $row;
        }
    }


    public function __destruct()
    {
        $this->_connection->close();
    }
}
