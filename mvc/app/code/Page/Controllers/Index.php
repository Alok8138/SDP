<?php

class Page_Controllers_Index
{

    public function indexAction()
    {

        // echo "Home Page";


        $root = Sdp::getBlock("page/root");
        $home = Sdp::getBlock("page/home");
        $root->getChild('content')->addChild('home', $home);


        $root->tooHtml();





        // echo "<pre>";
        // print_r($head);
        // echo "</pre>";

        // echo "<pre>";
        // print_r($header);
        // echo "</pre>";


        // echo "<pre>";
        // print_r($root);
        // echo "</pre>";
    }
}
