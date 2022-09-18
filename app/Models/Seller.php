<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $table = 'TVENDEDORES';
    protected $primaryKey = 'IDVENDEDOR';
    public $timestamps = false;
    
    /**
     * Checks if a seller id exist
     * @return boolean True/False indicating if the seller exists
     */
    public static function sellerExist($id) {
        if (is_numeric($id) && $id >= 0) {
            $seller = Seller::where('IDVENDEDOR', '=', $id)->get();
            return count($seller) > 0;
        }
        return false;
    }
}
