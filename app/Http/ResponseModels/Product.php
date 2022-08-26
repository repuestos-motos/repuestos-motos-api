<?php

namespace App\Http\ResponseModels;

class Product
{
    public $title;
    public $description;
    public $category;
    public $brandName;
    public $currentStock;
    public $price;

    public function __construct(
        $title = '',
        $description = '',
        $category = '',
        $brandName = '',
        $currentStock = '',
        $price = ''
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->category = $category;
        $this->brandName = $brandName;
        $this->currentStock = $currentStock;
        $this->price = $price;
    }

}
