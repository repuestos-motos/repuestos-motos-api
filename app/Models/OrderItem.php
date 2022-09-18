<?php

namespace App\Models;

use JsonSerializable;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model implements JsonSerializable
{
    protected $table = 'TDETALLESPEDIDOS';
    protected $primaryKey = 'IDDETALLES';
    public $timestamps = false; 

    /**
     * Returns JSON object representation
     */
    public function jsonSerialize()
    {
        $stdObject = (object)[
            "orderItemId" => $this->orderItemId(),
            "orderId" => $this->orderId(),
            "productId" => $this->productId(),
            "description" => $this->description(),
            "quantity" => $this->quantity(),
            "unitPrice" => $this->unitPrice(),
            "totalAmount" => $this->totalAmount(),
            "state" => $this->state(),
            "comments" => $this->comments()
        ];
        return $stdObject;
    }

    // Getters and setters

    public function orderItemId($value = null) {
        if (isset($value)) {
            $this->{$this->primaryKey} = $value;
        }
        return $this->{$this->primaryKey};
    }
    
    public function orderId($value = null) {
        if (isset($value)) {
            $this->IDPEDIDO = $value;
        }
        return $this->IDPEDIDO;
    }

    public function productId($value = null) {
        if (isset($value)) {
            $this->IDARTICULO = $value;
        }
        return $this->IDARTICULO;
    }
    
    public function description($value = null) {
        if (isset($value)) {
            $this->DESCRIPCION = $value;
        }
        return $this->DESCRIPCION;
    }
    
    public function quantity($value = null) {
        if (isset($value)) {
            $this->CANTIDAD = $value;
        }
        return $this->CANTIDAD;
    }

    public function unitPrice($value = null) {
        if (isset($value)) {
            $this->PRECIUNITARIO = $value;
        }
        return $this->PRECIUNITARIO;
    }

    public function totalAmount($value = null) {
        if (isset($value)) {
            $this->TOTAL = $value;
        }
        return $this->TOTAL;
    }
    
    public function state($value = null) {
        if (isset($value)) {
            $this->ESTADO = $value;
        }
        return $this->ESTADO;
    }
    
    public function comments($value = null) {
        if (isset($value)) {
            $this->COMENTARIO = $value;
        }
        return $this->COMENTARIO;
    }

}
