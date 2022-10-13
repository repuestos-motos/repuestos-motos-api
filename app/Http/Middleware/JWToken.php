<?php

namespace App\Http\Middleware;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;

class JWToken{
    private static $key = '5ekr3TKeY$2'; //Clave de encriptación
    private static $format = 'HS256'; //Tipo de encirptación 
    private static $time_token = (1000*60*24); //milisegundos*Minutos*Horas Para validez del token

    public static function CreateToken() {
        $time = time();
        $payload = array(
            'iat' => $time, // Tiempo que inició el token
            'aud' => self::Aud(),
            'exp' => $time + self::$time_token // Tiempo que expirará el token (S*M*X - X = horas)
        );
        return JWT::encode($payload, self::$key, self::$format);
    }
    
    public static function VerifyToken($token){
        if(empty($token)){
            return false;
        }
        try{
            $payload = JWT::decode($token, new Key(self::$key,self::$format));
            Log::error('Payload');
            Log::error(json_encode($payload));
            if($payload->aud !== self::Aud()){
                return false;
            }
            $time = time();
            $payload->exp = $time + self::$time_token;
            return JWT::encode((array)$payload, self::$key, self::$format);
        }catch(Exception $e){
            Log::error('Error al verificar Token');
            Log::error($e->getMessage());
            return false;
        }
    }
    
    public static function GetPayLoad($token)
    {
        return JWT::decode(
            $token,
            self::$key,
            self::$format
        );
    }

    public static function GetData($token)
    {
        return JWT::decode(
            $token,
            self::$key,
            self::$format
        )->data;
    }

    private static function Aud()
    {
        $aud = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }
        
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();
        
        return sha1($aud);
    }
}
