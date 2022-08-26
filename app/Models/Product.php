<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\ResponseModels\Product as ProductListResponseModel;

class Product extends Model
{
    protected $table = 'TARTICULOS';
    protected $primaryKey = 'IDARTICULO';

    public static function GetProductsListWithPrices($priceListId) {
        $products = Product::select(
            'TARTICULOS.*',
            'TMARCAS.DESCRIPCION AS MARCA',
            'TRUBROS.DESCRIPCION AS CATEGORIA')
            ->leftJoin('TMARCAS', 'TMARCAS.IDMARCA', '=', 'TARTICULOS.IDMARCA')
            ->leftJoin('TRUBROS', 'TRUBROS.IDRUBRO', '=', 'TARTICULOS.IDRUBRO')
            ->get();
        $priceList = PriceList::find($priceListId);
        $productList = [];
        foreach($products as $product) {
            if ($product->CALCULAPRECIOPORLISTA === 'S' && $priceList !== null) {
                $product->PRECIOVENTA = $product->COSTO * (1 + $priceList->PORCE / 100);
            }
            $productList[] = new ProductListResponseModel(
                $product->DESCRIPCION,
                $product->DETALLE,
                $product->CATEGORIA,
                $product->MARCA,
                $product->STOCK,
                $product->PRECIOVENTA
            );
        }
        return $productList;
    }
}
