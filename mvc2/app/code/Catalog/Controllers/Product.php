<?php

class Catalog_Controllers_Product extends Core_Controllers_Front
{
    public function listAction()
    {
        $root = Sdp::getBlock("Page/Root");
        $list = Sdp::getBlock("Catalog/Product_List");
        $root->getChild('content')->addChild('list', $list);
        $root->toHtml();
    }
    public function viewAction()
    {
        // $request     = Sdp::getModel("core/request");
        $root        = Sdp::getBlock("page/root");
        $view        = Sdp::getBlock("catalog/product_View");

        // echo "<pre>";
        // print_r($root->getChild('content'));
        // echo "</pre>";


        $root->getChild("content")->addChild("view", $view);
        $root->getChild("head")->addJs("js/Catalog/product.js");
        $root->getChild("head")->addCss("css/Catalog/product.css");

        $root->toHtml();
        // echo "<pre>";
        // print_r($root->getChild('content'));
        // echo "</pre>";
    }
}
