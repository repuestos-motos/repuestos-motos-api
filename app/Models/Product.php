<?php

namespace App\Models;

use JsonSerializable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Product extends Model implements JsonSerializable
{
    protected $table = 'TARTICULOS';
    protected $primaryKey = 'IDARTICULO';
    public $timestamps = false;

    /**
     * Returns JSON object representation
     */
    public function jsonSerialize()
    {
        $stdObject = (object)[
            "productId" => $this->productId(),
            "title" => $this->title(),
            "description" => $this->description(),
            "category" => $this->category(),
            "brandName" => $this->brandName(),
            "currentStock" => $this->currentStock(),
            "price" => $this->price(),
            "hasImage" => $this->hasImage()
        ];
        return $stdObject;
    }

    public function productId($value = null) {
        if ($value) {
            $this->{$this->primaryKey} = $value;
        }
        return $this->{$this->primaryKey};
    }
    public function title($value = null) {
        if ($value) {
            $this->DESCRIPCION = $value;
        }
        return $this->DESCRIPCION;
    }
    public function description($value = null) {
        if ($value) {
            $this->DETALLE = $value;
        }
        return $this->DETALLE;
    }
    public function category($value = null) {
        if ($value) {
            $this->CATEGORIA = $value;
        }
        return $this->CATEGORIA;
    }
    public function brandName($value = null) {
        if ($value) {
            $this->MARCA = $value;
        }
        return $this->MARCA;
    }
    public function salesStock($value = null) {
        if ($value) {
            $this->STOCKVENTA = $value;
        }
        return $this->STOCKVENTA;
    }
    public function currentStock($value = null) {
        if ($value) {
            $this->STOCK = $value;
        }
        return $this->STOCK;
    }
    public function price($value = null) {
        if ($value) {
            $this->PRECIOVENTA = $value;
        }
        return $this->PRECIOVENTA;
    }
    public function cost($value = null) {
        if ($value) {
            $this->COSTO = $value;
        }
        return $this->COSTO;
    }
    public function hasImage() {
        return $this->FOTO && $this->FOTO != "NO";
    }

    /**
     * Return all the products including it's brands and categories
     * @return Collection Collection of Products
     */
    public static function getProducts() {
        return Product::select(
            'TARTICULOS.*',
            'TMARCAS.DESCRIPCION AS MARCA',
            'TRUBROS.DESCRIPCION AS CATEGORIA')
            ->leftJoin('TMARCAS', 'TMARCAS.IDMARCA', '=', 'TARTICULOS.IDMARCA')
            ->leftJoin('TRUBROS', 'TRUBROS.IDRUBRO', '=', 'TARTICULOS.IDRUBRO')
            ->get();
    }

    /**
     * Return a product by id
     * @param id Id of the product to find
     * @return Product Returns the product that matches the id or null
     */
    public static function getProduct($id) {
        return Product::select(
            'TARTICULOS.*',
            'TMARCAS.DESCRIPCION AS MARCA',
            'TRUBROS.DESCRIPCION AS CATEGORIA')
            ->where('IDARTICULO', '=', $id)
            ->leftJoin('TMARCAS', 'TMARCAS.IDMARCA', '=', 'TARTICULOS.IDMARCA')
            ->leftJoin('TRUBROS', 'TRUBROS.IDRUBRO', '=', 'TARTICULOS.IDRUBRO')
            ->first();
    }

    public static function GetProductsListWithPrices($priceListId) {
        $products = self::getProducts();
        $priceList = PriceList::find($priceListId);
        $productList = [];
        foreach($products as $product) {
            if ($priceList !== null) {
                $product->price($product->cost() * (1 + $priceList->percentage() / 100));
            }
            $productList[] = $product;
        }
        return $productList;
    }

    /**
     * Method to reduce the stock of a product when a sale is registered
     * @param quantity quantity to reduce
     * @return bool Returns true in case of success
     * @throws HttpException In case of validation error
     * @throws Exception In case of any other issue 
     */
    public function reduceStock($quantity) {
        if ($quantity > 0 && $this->currentStock() >= $quantity) { 
            $this->currentStock($this->currentStock() - $quantity);
            $this->salesStock($this->salesStock() + $quantity);
            $this->save();
        } else {
            throw new HttpException(400, 'Stock insuficiente para el producto ' . $this->title());
        }
    }
}
