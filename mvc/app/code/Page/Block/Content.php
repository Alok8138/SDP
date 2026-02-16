<?php

class Page_Block_Content extends Core_Block_Template
{

    function __construct()
    {
        // parent::__construct();
        $this->setTemplet("Page/View/content.phtml");
    }
}
