<?php
class Page_Block_Home extends Core_Block_Template
{

    public function __construct()
    {
        // parent::__construct();
        $this->setTemplet("Page/View/home.phtml");
    }
}
