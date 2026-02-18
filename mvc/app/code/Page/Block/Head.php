<?php

class Page_Block_Head extends Core_Block_Template
{


    protected $_js = [];
    protected $_css = [];


    
    public function __construct()
    {
        $this->setTemplate("Page/View/head.phtml");
        $this->addJs("js/default.js") -> addJs("js/default1.js");
        
    }


    public function addJs($file) {
        $this->_js[] = $file;

        return $this;
    } 
    public function getJs() {
        return $this->_js;
    }


    // public function getCss() {
    //     $this->_css[] = $file;
    // }
    // public function addJss() {
    //     return $this->_js;
    // }
}
