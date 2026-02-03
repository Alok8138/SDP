<?php
/**
 * HomeController.php
 */

require_once __DIR__ . '/../models/FeaturedProduct.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Brand.php';

class HomeController {
    public function index() {
        $products = require __DIR__ . '/../models/FeaturedProduct.php';
        $categories = require __DIR__ . '/../models/Category.php';
        $brands = require __DIR__ . '/../models/Brand.php';

        return [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands
        ];
    }
}
