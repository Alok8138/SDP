<?php
class Catalog_Controllers_Product
{
    public function listAction()
    {
        echo "List Action";
    }
    public function viewAction()
    {
        // echo "View Action";
        $root = Sdp::getBlock("page/root");
        

        

        $view = Sdp::getBlock("catalog/product_view");
        $root->getChild('content')->addChild('view',$view);
        $root->tooHtml();

        // catalog_prd_block_view
    }

    
}
