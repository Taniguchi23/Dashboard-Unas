<?php

namespace App\Http\Controllers;

use App\Models\Cve;
use Illuminate\Http\Request;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function export(Request $request)
    {
        $filtro = $request->filtro;

        if ($request->fechaInicio != '' && $request->fechaFin != ''){

            $inicio = $request->input('fechaInicio').' 00:00:00';
            $fin = $request->input('fechaFin').' 23:59:59';

            $cves = Cve::whereHas('descriptions', function ($query) use ($filtro) {
                $query->where('value', 'like', '%' . $filtro . '%');
            })
                ->whereBetween('published', [$inicio, $fin]) // Agregar el rango de fechas aquí
                ->orderByDesc('published')
                ->get();
        }else{
            $cves = Cve::whereHas('descriptions', function ($query) use ($filtro) {
                $query->where('value','like', '%'.$filtro.'%');
            })->orderByDesc('published')->get();
        }

        return Excel::download(new ExcelExport($cves), 'data.xlsx');
    }

}
