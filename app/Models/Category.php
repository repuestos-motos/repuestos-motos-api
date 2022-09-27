<?php

namespace App\Models;

use JsonSerializable;
use Illuminate\Database\Eloquent\Model;

class Category extends Model implements JsonSerializable
{
    protected $table = 'TRUBROS';
    protected $primaryKey = 'IDRUBRO';
    public $timestamps = false;

    /**
     * Returns JSON object representation
     */
    public function jsonSerialize()
    {
        $stdObject = (object)[
            "id" => $this->IDRUBRO,
            "name" => $this->DESCRIPCION,
        ];
        return $stdObject;
    }

}
