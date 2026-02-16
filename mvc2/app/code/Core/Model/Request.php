<?php


class Core_Model_Request
{

    protected $_moduleName = "page";
    protected $_controllersName = "index";
    protected $_actionName = "index";


    function __construct()
    {

        $uri = $this->getRequestUri();

        $uri = str_replace($this->getbaseUrl(), "", $uri);
        //   /core/request

        $uri = array_filter(explode("/", $uri));

        $this->_moduleName      = isset($uri[0]) ? $uri[0] : "page";
        $this->_controllersName = isset($uri[1]) ? $uri[1] : "index";
        $this->_actionName      = isset($uri[2]) ? $uri[2] : "index";
    }

    public function getRequestUri()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : "http";

        $fulluri = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        return $fulluri;
    }
    public function getParams()
    {
        return $_REQUEST;
    }
    public function isPost()
    {
        return isset($_SERVER['POST']) ? true : false;
    }
    public function getPost()
    {
        return $_POST;
    }
    public function getbaseUrl()
    {
        return "http://localhost/internship/mvc2/";
    }
    public function getQuery()
    {
        return $_GET;
    }
    public function getModuleName()
    {
        return $this->_moduleName;
    }
    public function getControllersName()
    {
        return $this->_controllersName;
    }
    public function getActionName()
    {
        return $this->_actionName;
    }
}
