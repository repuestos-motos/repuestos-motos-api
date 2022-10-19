<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use JsonSerializable;

class OrderStatus extends Model implements JsonSerializable
{
    protected $table = 'TESTADO';
    protected $primaryKey = 'IDESTADO';
    public $timestamps = false;

    /**
     * Returns JSON object representation
     */
    public function jsonSerialize()
    {
        $stdObject = (object)[
            "stateId" => $this->IDESTADO,
            "description" => $this->DESCRIPCION
        ];
        return $stdObject;
    }
}
