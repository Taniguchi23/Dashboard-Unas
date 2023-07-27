<?php

namespace App\Helpers;

class Encriptar{
    static $key = 'EnCRypT10nK#Y!RiSRNn';
    static $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';


    static function codificar($dato) {
        $resultado = $dato;
        $arrayLetras = self::$key;
        $limite = strlen($arrayLetras) - 1;
        $num = mt_rand(3,5);
        for ($i = 1; $i <= $num; $i++) {
            $resultado = base64_encode($resultado);
        }
        $resultado = $resultado . '+' . $arrayLetras[$num];
        $resultado = base64_encode($resultado);
        return $resultado;
    }

    static function decodificar($dato) {
        $resultado = base64_decode($dato);
        list($resultado, $letra) = explode('+', $resultado);
        $arrayLetras = self::$key;
        for ($i = 0; $i < strlen($arrayLetras); $i++) {
            if ($arrayLetras[$i] == $letra) {
                for ($j = 1; $j <= $i; $j++) {
                    $resultado = base64_decode($resultado);
                }
                break;
            }
        }
        return $resultado;
    }

    static function generateString($strength) {
        $input = self::$permitted_chars;

        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }
        return $random_string;
    }
}
?>
