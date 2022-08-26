<?php

namespace App\Http\ResponseModels;

class User
{
    public $id;
    public $name;
    public $idNumber;
    public $idType;
    public $address;
    public $tel;
    public $email;
    public $iibb;


    public function __construct(
        $id,
        $name,
        $idNumber,
        $idType,
        $address,
        $tel,
        $email,
        $iibb
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->idNumber = $idNumber;
        $this->idType = $idType;
        $this->address = $address;
        $this->tel = $tel;
        $this->email = $email;
        $this->iibb = $iibb;
    }

}
