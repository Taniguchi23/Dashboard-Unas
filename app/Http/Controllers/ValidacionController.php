<?php

namespace App\Http\Controllers;

use App\Helpers\Encriptar;
use App\Helpers\Util;
use App\Models\Password_reset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mail;
use App\Mail\ReporteEmail;

class ValidacionController extends Controller
{
    public function vista(){
        return view('auth.recuperar');
    }

    public function recuperar(Request $request){
        $usuario = User::where('email', $request->email)->where('state','A')->first();
        if (!empty($usuario)){
            $solicitud = Password_reset::where('email',$request->email)->where('state','A')->first();
            $token = Encriptar::generateString(6);
            if (empty($solicitud)){
                $solicitud = new Password_reset;
            }
            $solicitud->email = $request->email;
            $solicitud->token = $token;
            $solicitud->save();

            $datos = [
                'url' => env('URL').'recuperar-password/'.Encriptar::codificar($token),
                'nombre' => $usuario->name,
            ];

            Mail::to($request->email)->send(new ReporteEmail($datos, 'email.passwordEmail', 'Recuperar contraseña', 'TeamResegti'));
        }
        return redirect()->route('validacion.vista')->with('mensaje', 'Se ha enviado un mensaje al email proporcionado.');
    }

    public function vistaRecuperar($token){
        $token = Encriptar::decodificar($token);
        $solicitud = Password_reset::where('token', $token)
            ->where('state','A')->first();

        if ($solicitud == null){
            $datos = [
                'estado' => false,
            ];

        }else{
            $user = User::where('email',$solicitud->email)->first();
            $datos = [
                'estado' => true,
                'name' => $user->name,
                'id' => $user->id,
            ];

        }
        return view('auth.cambiar', $datos);
    }

    public function cambiar(Request $request, $id){
        $user = User::find($id);
        $user->password = Hash::make($request->password);
        $user->save();
        $solicitud = Password_reset::where('email', $user->email)->first();
        $solicitud->state = 'I';
        $solicitud->save();
        return redirect()->route('login')->with('cambio', 'Se ha cambiado satifactoriamente su contraseña.');
    }
}
