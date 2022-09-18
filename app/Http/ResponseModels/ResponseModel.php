<?php

namespace App\Http\ResponseModels;

class ResponseModel
{
    public $message;
    public $data;
    private $statusCode = 200;

    public function __construct(
        $data = null,
        $message = '',
        $statusCode = 200
    ) {
        $this->message = $message;
        $this->data = $data;
        $this->statusCode = $statusCode;

    }

    public static function GetErrorResponse($data = null, $message = '', $statusCode = 500) {
        return response()->json(new ResponseModel($data, $message), $statusCode);
    }

    public static function GetSuccessfullResponse($data) {
        return response()->json(new ResponseModel($data), 200);
    }

}
