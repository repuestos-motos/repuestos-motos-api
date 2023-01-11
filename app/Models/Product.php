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
    private $calculatedPrice = 0;

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
            "hasImage" => true
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
    public function salesPrice($value = null) {
        if ($value) {
            $this->PRECIOVENTA = $value;
        }
        return $this->PRECIOVENTA;
    }
    public function price($value = null) {
        if ($value) {
            $this->calculatedPrice = $value;
        }
        return $this->calculatedPrice;
    }
    public function cost($value = null) {
        if ($value) {
            $this->COSTO = $value;
        }
        return $this->COSTO;
    }
    public function discount() {
        return $this->DESCUENTO;
    }


    private static function productsSelect() {
        return Product::select(
            'TARTICULOS.IDARTICULO',
            'TARTICULOS.DESCRIPCION',
            'TARTICULOS.PRECIOVENTA',
            'TARTICULOS.STOCKINICIA',
            'TARTICULOS.STOCKVENTA',
            'TARTICULOS.STOCKCOMPRA',
            'TARTICULOS.STOCKBAJA',
            'TARTICULOS.DESCUENTO',
            'TARTICULOS.STOCK',
            'TARTICULOS.DETALLE',
            'TMARCAS.DESCRIPCION AS MARCA',
            'TRUBROS.DESCRIPCION AS CATEGORIA')
            ->leftJoin('TMARCAS', 'TMARCAS.IDMARCA', '=', 'TARTICULOS.IDMARCA')
            ->leftJoin('TRUBROS', 'TRUBROS.IDRUBRO', '=', 'TARTICULOS.IDRUBRO');
    }

    /**
     * Return all the products including it's brands and categories
     * @return Collection Collection of Products
     */
    public static function getProducts() {
        return self::productsSelect()
            ->get();
    }

    /**
     * Return a product by id
     * @param id Id of the product to find
     * @return Product Returns the product that matches the id or null
     */
    public static function getProduct($id) {
        return self::productsSelect()
            ->where('IDARTICULO', '=', $id)
            ->first();
    }

    public static function GetProductsListWithPrices($priceListId) {
        $products = self::getProducts();
        $priceList = PriceList::find($priceListId);
        $productList = [];
        foreach($products as $product) {
            $product->calculateSalesPrice($priceList);
            $productList[] = $product;
        }
        return $productList;
    }

    /**
     * Generates the price for the current product taking into accout the priceList
     * @param PriceList Price List Object
     */
    public function calculateSalesPrice($priceList) {
        $calculatedPrice = $this->salesPrice();
        if ($priceList) {
            if ($priceList->isDiscount()) {
                $calculatedPrice = round($calculatedPrice * (1 - $priceList->percentage() / 100), 2);
            } else {
                $calculatedPrice = round($calculatedPrice * (1 + $priceList->percentage() / 100), 2);
            }
        }
        if ($this->discount() > 0) {
            $calculatedPrice = round($calculatedPrice * (1 - $this->discount() / 100), 2);
        }
        $this->price($calculatedPrice);
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
