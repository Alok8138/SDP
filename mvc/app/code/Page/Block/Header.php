<?php
class Page_Block_Header extends Core_Block_Template
{

    function __construct()
    {
        // parent::__construct();
        $this->setTemplet("Page/View/header.phtml");
    }
}
