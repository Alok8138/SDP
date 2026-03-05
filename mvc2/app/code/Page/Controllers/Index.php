<?php
class Page_Controllers_Index extends Core_Controllers_Front
{
    public function indexAction()
    {
        $root = Sdp::getBlock("page/root");
        $home = Sdp::getBlock('page/home');

        // echo "<pre>";
        // print_r($root->getChild('content'));
        // echo "</pre>";


        $root->getChild('content')->addChild('home', $home);
        $root->toHtml();

        // echo "<pre>";
        // print_r($root);
        // echo "</pre>";
    }
}
