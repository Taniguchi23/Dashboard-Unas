<?php

namespace App\Helpers;



class Util
{



    //FORMATO DE FECHA 06 DE NOVIEMBRE DEL 2016
    static function formatoFechaPersonalizado($fecha)
    {
        $contenedor = strtotime($fecha);
        $dia = date('d', $contenedor);
        $mes = date('m', $contenedor);
        $anio = date('Y', $contenedor);
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'setiembre', 'octubre', 'noviembre', 'diciembre'];
        $texto = $dia . ' de ' . $meses[($mes - 1)] . ' del ' . $anio;
        return $texto;
    }

    //FORMATO DE FECHA CON HORA PERSONALIZADO
    static function formatoFechaPersonalizadoHora($fecha)
    {
        $contenedor = strtotime($fecha);
        $dia = date('d', $contenedor);
        $mes = date('m', $contenedor);
        $anio = date('Y', $contenedor);
        $hora = date('H:i:s', $contenedor);
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'setiembre', 'octubre', 'noviembre', 'diciembre'];
        $texto = $dia . ' de ' . $meses[($mes - 1)] . ' del ' . $anio . ' ' . $hora;
        return $texto;
    }

    static function textoEstado($texto){
        $response = '';
       if ($texto == "HIGH"){
           $response = "danger";
       }else if ($texto == "PARTIAL"){
           $response = "warning";
       }else if ($texto == "LOW"){
           $response = "success";
       }else if ($texto== "NONE"){
           $response = "info";
       }else{
           $response = "secondary";
       }
        return 'text-'.$response;
    }


}
