    <?php
    class Page_Block_Brand extends Core_Block_Template
    {
        public function __construct()
        {
            $this->setTemplate("Page/View/brand.phtml");       
        }

    public function getMenuArray()
    {
        return [
            'category1' => ['name' => 'Nike', 'image' => 'nike.jpg'],
            'category2' => ['name' => 'Apple', 'image' => 'apple.jpg'],
            'category3' => ['name' => 'Sony', 'image' => 'sony.jpg']
        ];
    }



    }





    ?>