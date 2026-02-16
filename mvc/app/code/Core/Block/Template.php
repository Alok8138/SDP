<?php

class Core_Block_Template
{

    protected $_child = [];
    protected $_parent = null;
    protected $_template = null;


    public function __construct()
    {

        $this->_construct();
    }

    public function setTemplet($template)
    {
        $this->_template = $template;
    }



    public function tooHtml()
    {
        //full path till.phtml
        // echo getcwd() . "/app/code/" . $this->_template;;

        include getcwd() . "/app/code/" . $this->_template;
    }

    public function addChild($name, $block)
    {
        $this->_child[$name] = $block;
    }
    public function getChild($name)
    {
        // $this->_child[$name] = $block;
        if (isset($this->_child[$name])) {

            return $this->_child[$name];
        }
    }

    public function getChildHtml($name = "")
    {

        // print_r($this->_child);
        if (isset($this->_child[$name])) {

            $this->_child[$name]->tooHtml();
        } else {
            if (count($this->_child)) {
                foreach ($this->_child as $child) {
                    $child->tooHtml();
                }
            }
        }
    }
}
