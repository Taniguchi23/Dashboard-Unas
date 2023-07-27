<?php

namespace App\Http\Controllers;

use App\Models\Cve;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function index(){
        return view('web.index');
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
