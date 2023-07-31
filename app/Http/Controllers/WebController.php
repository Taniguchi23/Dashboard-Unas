<?php

namespace App\Http\Controllers;

use App\Models\Cve;
use App\Models\Metric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebController extends Controller
{
    public function index(){

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

        $cves = Cve::orderByDesc('published')->take(20)->get();

        $datos = [
            'cveDos' =>  json_encode($cveDos),
            'cveTres' => json_encode($cveTres),
            'cves' => $cves,
        ];
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
