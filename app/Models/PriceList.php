<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $table = 'TLISTASPRECIOS';
    protected $primaryKey = 'IDLISTA';

    public function getId() {
        return $this->IDLISTA;
    }

    public function percentage() {
        return $this->PORCE;
    }

    public function isDiscount() {
        return $this->APLICA === '-';
    }
}
