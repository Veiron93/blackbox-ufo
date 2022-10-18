<?php

class Infinity_Products_List
{

    private static $idsProducts = [];
    public static $products = [];

    public function __construct()
    {
        self::$products = self::getProducts();
    }

    public static function getProducts()
    {
        $products = App_Catalog::getProducts("", 30);

        if ($products && count($products)) {
            foreach ($products as $product) {
                array_push(self::$idsProducts, $product->id);
            }
        }

        traceLog(self::$idsProducts);

        return $products;
    }
}
