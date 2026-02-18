<?php

class Catalog_Block_Product_View extends Core_Block_Template

{
    // protected $_js = [];
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate("Catalog/View/Product/view.phtml");
    }

    public function _construct() {}

    public function getProduct()
    {
        // return new 
        $product = SDp::getModel("catalog/product");
        $product->addData(
            [
                "product_id" => 1,
                "name" => "Dell Laptop 001",
                "url" => "Dell_Laptop001"
            ]


        );

        return $product;
        echo "<pre>";
        print_r($product);
        echo "</pre>";
    }
}
