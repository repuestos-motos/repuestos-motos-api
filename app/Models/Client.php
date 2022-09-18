<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'TCLIENTES';
    protected $primaryKey = 'IDCLIENTE';
    public $timestamps = false;

    /**
     * Returns JSON object representation
     */
    public function jsonSerialize()
    {
        $stdObject = (object)[
            "clientId" => $this->clientId(),
            "name" => $this->name(),
            "address" => $this->address(),
            "tel" => $this->tel(),
            "email" => $this->email()
        ];
        return $stdObject;
    }

    public function clientId($value = null) {
        if ($value) {
            $this->{$this->primaryKey} = $value;
        }
        return $this->{$this->primaryKey};
    }
    public function name($value = null) {
        if ($value) {
            $this->APENOM = $value;
        }
        return $this->APENOM;
    }
    public function address($value = null) {
        if ($value) {
            $this->DOMICILIO = $value;
        }
        return $this->DOMICILIO;
    }
    public function tel($value = null) {
        if ($value) {
            $this->TELEFONO = $value;
        }
        return $this->TELEFONO;
    }
    public function email($value = null) {
        if ($value) {
            $this->CORREO = $value;
        }
        return $this->CORREO;
    }

    /**
     * Checks if a client id exist
     * @return boolean True/False indicating if the client exists
     */
    public static function clientExist($clientId) {
        return !!!Client::find($clientId);
    }
}
