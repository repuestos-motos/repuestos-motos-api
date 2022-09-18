<?php

namespace App\Http\ResponseModels;

class Product
{
    public $porductId;
    public $title;
    public $description;
    public $category;
    public $brandName;
    public $currentStock;
    public $price;

    public function __construct(
        $id = '',
        $title = '',
        $description = '',
        $category = '',
        $brandName = '',
        $currentStock = '',
        $price = ''
    ) {
        $this->porductId = $id;
        $this->title = $title;
        $this->description = $description;
        $this->category = $category;
        $this->brandName = $brandName;
        $this->currentStock = $currentStock;
        $this->price = $price;
    }

}
