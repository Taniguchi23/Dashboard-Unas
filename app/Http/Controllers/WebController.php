<?php

namespace App\Http\Controllers;

use App\Models\Cve;
use App\Models\Description;
use App\Models\Filtro;
use App\Models\Metric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebController extends Controller
{
    public function index(Request $request){

        $tiposContadosDos = Metric::where('version', 2)
            ->selectRaw('
        CASE
            WHEN cvssData_baseScore >= 0 AND cvssData_baseScore < 5 THEN "LOW"
            WHEN cvssData_baseScore >= 5 AND cvssData_baseScore < 7 THEN "MEDIUM"
            WHEN cvssData_baseScore >= 7 AND cvssData_baseScore < 9 THEN "HIGH"
            WHEN cvssData_baseScore >= 9 AND cvssData_baseScore <= 10 THEN "CRITICAL"
        END AS tipo,
        COUNT(*) as total
    ')
            ->groupBy('tipo')
            ->orderBy('total', 'desc')
            ->get();

        $tiposContadosTres = Metric::where('version', 3)
            ->selectRaw('
        CASE
            WHEN cvssData_baseScore >= 0 AND cvssData_baseScore < 5 THEN "LOW"
            WHEN cvssData_baseScore >= 5 AND cvssData_baseScore < 7 THEN "MEDIUM"
            WHEN cvssData_baseScore >= 7 AND cvssData_baseScore < 9 THEN "HIGH"
            WHEN cvssData_baseScore >= 9 AND cvssData_baseScore <= 10 THEN "CRITICAL"
        END AS tipo,
        COUNT(*) as total
    ')
            ->groupBy('tipo')
            ->orderBy('total', 'desc')
            ->get();

        $cveDos = [0,0,0,0];
        $cveTres = [0,0,0,0];

        foreach ($tiposContadosDos as  $tiposContadosDo){
            if ($tiposContadosDo->tipo == 'CRITICAL'){
                $cveDos[0] =  $tiposContadosDo->total;
            }else if($tiposContadosDo->tipo == 'HIGH'){
                $cveDos[1] =  $tiposContadosDo->total;
            }else if($tiposContadosDo->tipo == 'MEDIUM'){
                $cveDos[2] =  $tiposContadosDo->total;
            }else{
                $cveDos[3] =  $tiposContadosDo->total;
            }
        }

        foreach ($tiposContadosTres as  $tiposContadosTre){
            if ($tiposContadosTre->tipo == 'CRITICAL'){
                $cveTres[0] =  $tiposContadosTre->total;
            }else if($tiposContadosTre->tipo == 'HIGH'){
                $cveTres[1] =  $tiposContadosTre->total;
            }else if($tiposContadosTre->tipo == 'MEDIUM'){
                $cveTres[2] =  $tiposContadosTre->total;
            }else{
                $cveTres[3] =  $tiposContadosTre->total;
            }
        }

        $cvesUltimos = Cve::orderByDesc('published')->take(20)->get();

        if ($request->has('filtro')) {
            $filtroSelect = $request->input('filtro');

        } else {
            $filtroSelect = 'CISCO';
        }

        $cves = Cve::whereHas('descriptions', function ($query) use ($filtroSelect) {
            $query->where('value','like', '%'.$filtroSelect.'%');
        })->orderByDesc('published')->get();

        $listaFiltros = Filtro::where('estado','A')->orderBy('orden')->get();
        $arregloFiltros = Filtro::where('estado','A')->orderBy('orden')->pluck('nombre')->toArray();

        $fechaActual = date('Y-m-d');
        $mesAnterior = date('m', strtotime('-1 month', strtotime($fechaActual)));
        $mesAnterior = intval($mesAnterior);

        $resultados = [];
        foreach ($arregloFiltros as  $filtro){
            $resultados[] = DB::table('cves')
                ->join('descriptions', 'cves.id', '=', 'descriptions.cve_id')
                ->where('descriptions.value', 'like', '%' . $filtro . '%')
                ->whereMonth('cves.published', $mesAnterior)
                ->distinct()
                ->count('cves.id');

        }




        $datos = [
            'cveDos' =>  json_encode($cveDos),
            'cveTres' => json_encode($cveTres),
            'listaFiltros' => $listaFiltros,
            'cves' => $cves,
            'cvesUltimos' => $cvesUltimos,
            'filtro' => $filtroSelect,
            'ultimoMes' => $mesAnterior,
             'arregloFiltros' => $arregloFiltros,
            'resultadoFiltros' => $resultados,

        ];

      // dd($datos);

        return view('web.index', $datos);
    }

    public function vulnerabilidades(){
      $cves =  Cve::orderByDesc('published')->get();
      return view('web.resumen',compact('cves'));
    }

    public function notificacion(){
        return view('web.notificacion');
    }

    public function personalizacion(Request $request){

        $cves = Cve::whereHas('descriptions', function ($query) {
            $query->where('value','like', '%CISCO%');
        })->orderByDesc('published')->get();

        $datos = [
            'cves' => $cves,
            'filtro' => 'CISCO'
        ];

        return view('web.personalizacion',$datos);
    }
    public function personalizacionPost(Request $request){
        $filtro = $request->filtro;
        $cves = Cve::whereHas('descriptions', function ($query) use ($filtro) {
            $query->where('value','like', '%'.$filtro.'%');
        })->orderByDesc('published')->get();

        $datos = [
            'cves' => $cves,
            'filtro' => $filtro,
        ];

        return view('web.personalizacion',$datos);
    }
}
