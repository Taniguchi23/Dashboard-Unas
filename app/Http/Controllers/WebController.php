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

      /*  $tiposContadosDos = Metric::where('version', 2)
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
        }*/

        $arregloFiltros = Filtro::where('estado','A')->orderBy('orden')->pluck('nombre')->toArray();

        $cvesUltimos = Cve::whereIn('id', function ($query) use ($arregloFiltros) {
            $query->select('cve_id')
                ->from('descriptions')
                ->where(function ($query) use ($arregloFiltros) {
                    foreach ($arregloFiltros as $filtro) {
                        $query->orWhere('value', 'LIKE', "%$filtro%");
                    }
                });
        })
            ->orderByDesc('published')
            ->take(20)
            ->get();



        $metricsCritical3  = Metric::whereBetween('cvssData_baseScore',[9,10])->where('version',3)->get();
        $metricsHigh3  = Metric::whereBetween('cvssData_baseScore',[7,8.9])->where('version',3)->get();
        $metricsMedium3  = Metric::whereBetween('cvssData_baseScore',[5,6.9])->where('version',3)->get();
        $metricsLow3  = Metric::whereBetween('cvssData_baseScore',[0,4.9])->where('version',3)->get();
        $cveTres = [$metricsCritical3->count(),$metricsHigh3->count(),$metricsMedium3->count(),$metricsLow3->count()];
        $contenedorCriticalFiltros3 = [];
        $contenedorHighFiltros3 = [];
        $contenedorMediumFiltros3 = [];
        $contenedorLowlFiltros3 = [];
        foreach ($arregloFiltros as $filtro) {
            $cantidadCriticalFiltros3 = Description::whereIn('cve_id', $metricsCritical3->pluck('cve_id'))
                ->where('value', 'LIKE', '%' . $filtro . '%')
                ->count();
            $cantidadHighFiltros3 = Description::whereIn('cve_id', $metricsHigh3->pluck('cve_id'))
                ->where('value', 'LIKE', '%' . $filtro . '%')
                ->count();
            $cantidadMediumFiltros3 = Description::whereIn('cve_id', $metricsMedium3->pluck('cve_id'))
                ->where('value', 'LIKE', '%' . $filtro . '%')
                ->count();
            $cantidadLowFiltros3 = Description::whereIn('cve_id', $metricsLow3->pluck('cve_id'))
                ->where('value', 'LIKE', '%' . $filtro . '%')
                ->count();

            if ($cantidadCriticalFiltros3 != 0){
                $contenedorCriticalFiltros3[$filtro] = $cantidadCriticalFiltros3;
            }
            if ($cantidadHighFiltros3 != 0){
                $contenedorHighFiltros3[$filtro] = $cantidadHighFiltros3;
            }
            if ($cantidadMediumFiltros3 != 0){
                $contenedorMediumFiltros3[$filtro] = $cantidadMediumFiltros3;
            }
            if ($cantidadLowFiltros3 != 0){
                $contenedorLowlFiltros3[$filtro] = $cantidadLowFiltros3;
            }
        }
        $contenedorCriticalFiltros3['Otros'] = $metricsCritical3->count() - array_sum($contenedorCriticalFiltros3);
        $contenedorHighFiltros3['Otros'] = $metricsHigh3->count() - array_sum($contenedorHighFiltros3);
        $contenedorMediumFiltros3['Otros'] = $metricsMedium3->count() - array_sum($contenedorMediumFiltros3);
        $contenedorLowlFiltros3['Otros'] = $metricsLow3->count() - array_sum($contenedorLowlFiltros3);

        $tempContenedorCriticalFiltros3 = [];
        foreach ($contenedorCriticalFiltros3 as $key => $itemCritical3){
            $tempContenedorCriticalFiltros3[] = [
                'nombre' => $key,
                'valor' => $itemCritical3
            ];
        }
        $tempContenedorHighFiltros3 = [];
            foreach ($contenedorHighFiltros3 as $key => $itemCritical3){
                $tempContenedorHighFiltros3[] = [
                    'nombre' => $key,
                    'valor' => $itemCritical3
                ];
            }
        $tempContenedorMediumFiltros3 = [];
            foreach ($contenedorMediumFiltros3 as $key => $itemCritical3){
                $tempContenedorMediumFiltros3[] = [
                    'nombre' => $key,
                    'valor' => $itemCritical3
                ];
            }
        $tempContenedorLowFiltros3 = [];
            foreach ($contenedorLowlFiltros3 as $key => $itemCritical3){
                $tempContenedorLowFiltros3[] = [
                    'nombre' => $key,
                    'valor' => $itemCritical3
                ];
            }



        $datosPorCategoria3 = [
            'CRITICAL' => $tempContenedorCriticalFiltros3,
            'HIGH' => $tempContenedorHighFiltros3,
            'MEDIUM' => $tempContenedorMediumFiltros3,
            'LOW' => $tempContenedorLowFiltros3,
        ];


        ///


        $metricsCritical2  = Metric::whereBetween('cvssData_baseScore',[9,10])->where('version',)->get();
        $metricsHigh2  = Metric::whereBetween('cvssData_baseScore',[7,8.9])->where('version',2)->get();
        $metricsMedium2  = Metric::whereBetween('cvssData_baseScore',[5,6.9])->where('version',2)->get();
        $metricsLow2  = Metric::whereBetween('cvssData_baseScore',[0,4.9])->where('version',2)->get();

        $cveDos = [$metricsCritical2->count(),$metricsHigh2->count(),$metricsMedium2->count(),$metricsLow2->count()];
        $contenedorCriticalFiltros2 = [];
        $contenedorHighFiltros2 = [];
        $contenedorMediumFiltros2 = [];
        $contenedorLowlFiltros2 = [];
        foreach ($arregloFiltros as $filtro) {
            $cantidadCriticalFiltros2 = Description::whereIn('cve_id', $metricsCritical2->pluck('cve_id'))
                ->where('value', 'LIKE', '%' . $filtro . '%')
                ->count();
            $cantidadHighFiltros2 = Description::whereIn('cve_id', $metricsHigh2->pluck('cve_id'))
                ->where('value', 'LIKE', '%' . $filtro . '%')
                ->count();
            $cantidadMediumFiltros2 = Description::whereIn('cve_id', $metricsMedium2->pluck('cve_id'))
                ->where('value', 'LIKE', '%' . $filtro . '%')
                ->count();
            $cantidadLowFiltros2 = Description::whereIn('cve_id', $metricsLow2->pluck('cve_id'))
                ->where('value', 'LIKE', '%' . $filtro . '%')
                ->count();

            if ($cantidadCriticalFiltros2 != 0){
                $contenedorCriticalFiltros2[$filtro] = $cantidadCriticalFiltros2;
            }
            if ($cantidadHighFiltros2 != 0){
                $contenedorHighFiltros2[$filtro] = $cantidadHighFiltros2;
            }
            if ($cantidadMediumFiltros2 != 0){
                $contenedorMediumFiltros2[$filtro] = $cantidadMediumFiltros2;
            }
            if ($cantidadLowFiltros2 != 0){
                $contenedorLowlFiltros2[$filtro] = $cantidadLowFiltros2;
            }
        }
        $contenedorCriticalFiltros2['Otros'] = $metricsCritical2->count() - array_sum($contenedorCriticalFiltros2);
        $contenedorHighFiltros2['Otros'] = $metricsHigh2->count() - array_sum($contenedorHighFiltros2);
        $contenedorMediumFiltros2['Otros'] = $metricsMedium2->count() - array_sum($contenedorMediumFiltros2);
        $contenedorLowlFiltros2['Otros'] = $metricsLow2->count() - array_sum($contenedorLowlFiltros2);

        $tempContenedorCriticalFiltros2 = [];
        foreach ($contenedorCriticalFiltros2 as $key => $itemCritical2){
            $tempContenedorCriticalFiltros2[] = [
                'nombre' => $key,
                'valor' => $itemCritical2
            ];
        }
        $tempContenedorHighFiltros2 = [];
        foreach ($contenedorHighFiltros2 as $key => $itemCritical2){
            $tempContenedorHighFiltros2[] = [
                'nombre' => $key,
                'valor' => $itemCritical2
            ];
        }
        $tempContenedorMediumFiltros2 = [];
        foreach ($contenedorMediumFiltros2 as $key => $itemCritical2){
            $tempContenedorMediumFiltros2[] = [
                'nombre' => $key,
                'valor' => $itemCritical2
            ];
        }
        $tempContenedorLowFiltros2 = [];
        foreach ($contenedorLowlFiltros2 as $key => $itemCritical2){
            $tempContenedorLowFiltros2[] = [
                'nombre' => $key,
                'valor' => $itemCritical2
            ];
        }



        $datosPorCategoria2 = [
            'CRITICAL' => $tempContenedorCriticalFiltros2,
            'HIGH' => $tempContenedorHighFiltros2,
            'MEDIUM' => $tempContenedorMediumFiltros2,
            'LOW' => $tempContenedorLowFiltros2,
        ];

        //dd($datosPorCategoria2);
        ///





        if ($request->has('filtro')) {
            $filtroSelect = $request->input('filtro');

        } else {
            $filtroSelect = 'CISCO';
        }

        $cves = Cve::whereHas('descriptions', function ($query) use ($filtroSelect) {
            $query->where('value','like', '%'.$filtroSelect.'%');
        })->orderByDesc('published')->get();

        $listaFiltros = Filtro::where('estado','A')->orderBy('orden')->get();


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
            'datosPorCategoria3' => json_encode($datosPorCategoria3),
            'datosPorCategoria2' => json_encode($datosPorCategoria2),

        ];

        //dd($datos);
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
