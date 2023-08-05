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
       }else if ($texto == "MEDIUM"){
           $response = "warning";
       }else if ($texto == "LOW"){
           $response = "success";
       }else if ($texto== "NONE"){
           $response = "info";
       }else if($texto == "CRITICAL"){
           $response = "dark";
       }else{
           $response = "secondary";
       }
        return 'text-'.$response;
    }


    static function valorTexto($valor){
        $response = '';
        if ($valor <= 5 && $valor >= 0){
            $response = 'LOW';
        }else if($valor <= 7 && $valor > 5){
            $response = 'MEDIUM';
        }else if($valor <= 9 && $valor > 7 ){
            $response = 'HIGH';
        }else{
            $response = 'CRITICAL';
        }
        return $response;
    }

    static function valorColor($valor){

        $response = '';
        if ($valor <= 5 && $valor >= 0){
            $response = 'success';
        }else if($valor <= 7 && $valor > 5){
            $response = 'warning';
        }else if($valor <= 9 && $valor > 7 ){
            $response = 'danger';
        }else{
            $response = 'dark';
        }

        return 'text-'.$response;
    }
    static function valorColorButton($valor){

        $response = '';
        if ($valor <= 5 && $valor >= 0){
            $response = 'success';
        }else if($valor <= 7 && $valor > 5){
            $response = 'warning';
        }else if($valor <= 9 && $valor > 7 ){
            $response = 'danger';
        }else{
            $response = 'dark';
        }

        return 'btn-'.$response;
    }


    static function estadoColor($estado){
        return $estado == 'A' ? 'success' : 'warning';
    }
    static function estadoTexto($estado){
        return $estado == 'A' ? 'Activo' : 'Inactivo';
    }
}
