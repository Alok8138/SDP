<?php
class Page_Block_Head extends Core_Block_Template
{

    public function __construct()
    {
        // parent::__construct();
        $this->setTemplet("Page/View/head.phtml");
    }
}
