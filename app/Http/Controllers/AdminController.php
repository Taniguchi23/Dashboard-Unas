<?php

namespace App\Http\Controllers;

use App\Models\Filtro;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Response;

class AdminController extends Controller
{
    public function lista(){
        $users = User::where('rol','!=','S')->orderByDesc('id')->orderBy('state')->get();
        $datos = [
            'usuarios' => $users,
        ];
        return view('admin.lista', $datos);
    }

    public function usuarioStore(Request $request){
        $user = new User;
        $user->name = $request->name;
        $user->email =  $request->email;
        $user->rol =  $request->rol;
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()->route('admin.lista')->with('mensaje', '¡Se ha creado un usuario!');
    }

    public function usuarioEdit($id){
        $user = User::find($id);
        $datos = [
            'name' => $user->name,
            'email' => $user->email,
            'id' => $user->id,
            'state' => $user->state,
            'rol' => $user->rol,
        ];

        return response::json($datos);
    }

    public function usuarioUpdate(Request  $request, $id){
        $user = User::find($id);
        $user->name = $request->name;
        $user->email =  $request->email;
        $user->rol =  $request->rol;
        if ($request->filled('password')){
            $user->password = Hash::make($request->password);
        }
        $user->state = $request->estado;
        $user->save();
        return redirect()->route('admin.lista')->with('mensaje', '¡Se ha actualizado un usuario!');
    }
    public function filtros(){
        $filtros = Filtro::where('estado','A')->orderBy('orden')->get();
        return view('admin.filtro',compact('filtros'));
    }

    public function filtroStore(Request $request){
        $verificarOrden = Filtro::where('orden', $request->orden)->where('estado','A')->first();
        $filtro = new Filtro;
        $filtro->nombre = $request->nombre;
        if (!$verificarOrden){
            $filtro->orden =  $request->orden;
        }else{
            $orden = $verificarOrden->max('orden');
            $filtro->orden =  $orden + 1;
        }
        $filtro->save();

        return redirect()->route('admin.filtro')->with('mensaje', '¡Se ha creado un filtro!');
    }

    public function filtroEdit($id){

        $filtro = Filtro::find($id);
        $datos = [
            'nombre' => $filtro->nombre,
            'orden' => $filtro->orden,
            'id' => $filtro->id,
            'estado' => $filtro->estado,
        ];

        return response::json($datos);
    }

    public function filtroUpdate(Request  $request, $id){
        $filtro = Filtro::find($id);
        $verificarOrden = Filtro::where('orden', $request->orden)
            ->where('estado', 'A')
            ->where('id', '!=', $filtro->id)
            ->first();

        if ($verificarOrden){
            $verificarOrden->orden = $filtro->orden;
            $filtro->orden = $request->orden;
            $verificarOrden->save();
        }

        $filtro->nombre = $request->nombre;
        $filtro->estado = $request->estado;
        $filtro->save();
        return redirect()->route('admin.filtro')->with('mensaje', '¡Se ha actualizado un filtro!');
    }



}
