<?php

namespace App\Http\Controllers;

use App\Models\Filtro;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Response;

class AdminController extends Controller
{
    public function lista($tipo){
        $rol =substr($tipo,0,1);
        $users = User::where('rol',$rol)->orderByDesc('id')->orderBy('state')->get();
        $datos = [
            'usuarios' => $users,
            'rol' => $rol == 'u' ? 'Usuarios' : 'Administradores',
        ];
        return view('admin.lista', $datos);
    }

    public function usuarioStore(Request $request){
        $user = new User;
        $user->name = $request->name;
        $user->email =  $request->email;
        $user->password = Hash::make($request->password);
        $user->rol = substr($request->rol,0,1) == 'u'? 'U': 'A';
        $user->save();
        return redirect()->route('admin.lista',['tipo'=> $request->rol]);
    }

    public function usuarioEdit($id){
        $user = User::find($id);
        $datos = [
            'name' => $user->name,
            'email' => $user->email,
            'id' => $user->id,
            'state' => $user->state,
        ];

        return response::json($datos);
    }

    public function usuarioUpdate(Request  $request, $id){
        $user = User::find($id);
        $user->name = $request->name;
        $user->email =  $request->email;
        if ($request->filled('password')){
            $user->password = Hash::make($request->password);
        }
        $user->state = $request->estado;
        $rol = $user->rol== 'U'? 'usuarios': 'administradores';
        $user->save();
        return redirect()->route('admin.lista',['tipo'=> $rol]);
    }
    public function filtros(){
        $filtros = Filtro::where('estado','A')->orderBy('orden')->get();
        return view('admin.filtros',compact('filtros'));
    }

    public function filtroStore(Request $request){
        $filtro = new Filtro;
        $filtro->nombre = $request->name;
        $filtro->orden =  $request->email;
        $filtro->estado =  $request->estado;
        $filtro->save();
        return redirect()->route('admin.filtro');
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
        $filtro->nombre = $request->name;
        $filtro->orden =  $request->email;
        $filtro->estado =  $request->estado;
        $filtro->save();
        return redirect()->route('admin.filtro');
    }
}
