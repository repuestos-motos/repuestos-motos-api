<?php

namespace App\Http\ResponseModels;

class SellerUser
{
    public $id;
    public $name;
    public $commission;
    public $isSeller = true;


    public function __construct(
        $id,
        $name,
        $commission
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->commission = $commission;
    }

}
