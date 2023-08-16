<?php

namespace App\Http\Controllers;

use App\Helpers\Util;
use App\Models\Cve;
use App\Models\Cvsdo;
use App\Models\Cvstre;
use App\Models\Description;
use App\Models\Email;
use App\Models\Filtro;
use App\Models\Metric;
use App\Models\Reference;
use App\Models\User;
use App\Models\Weakdescription;
use App\Models\Weakne;
use Illuminate\Support\Facades\DB;
use Mail;
use Response;
use App\Mail\ReporteEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashController extends Controller
{
    public function consulta()
    {
        try {
            $cantidad = Cve::count();

            $comienzoEstatico = env('VIERA_NVD_START');
            $comienzo = $cantidad + $comienzoEstatico;

            $url = 'https://services.nvd.nist.gov/rest/json/cves/2.0?startIndex='.$comienzo;
           // dd($url);
            $response = Http::timeout(120000) // Establece un tiempo de espera de 60 segundos
            ->get($url);
            $data = $response->json();
           // dd($data);

            $listaFiltros = Filtro::where('estado','A')->orderBy('orden')->get();

            if (!empty($data['vulnerabilities'])) {
                $listaVulnerabilidades = [];
                foreach ($data['vulnerabilities'] as $vulnerabilidad) {
                    $cve = $vulnerabilidad['cve'];
                    $cveObject = new Cve;
                    $cveObject->codigo = $cve['id'];
                    $cveObject->sourceIdentifier = $cve['sourceIdentifier'];
                    $cveObject->published = $cve['published'];
                    $cveObject->lastModified = $cve['lastModified'];
                    $cveObject->vulnStatus = $cve['vulnStatus'];
                    $cveObject->save();

                    $descripciones = $cve['descriptions'];
                    foreach ($descripciones as $descripcion){
                        $descriptionObject = new Description;
                        $descriptionObject->cve_id = $cveObject->id;
                        $descriptionObject->lang = $descripcion['lang'];
                        $descriptionObject->value = $descripcion['value'];
                        $descriptionObject->save();
                    }

                  /*  $metricas = $cve['metrics'];
                    if (!empty($metricas)){

                        $primerClave = key($metricas);
                        var_dump($primerClave);
                        dd($primerValor);
                    }else{
                        dd("sdsd");
                    }*/
                    $temp = $cve['metrics'];
                    $metricas = $cve['metrics'];
                    if (!empty($temp)){
                        $primerValor = reset($temp);

                        if (Util::esCritico($primerValor[0]['cvssData']['baseScore'])){
                            $encontrada = false;
                            foreach ($listaFiltros as $palabra) {
                                if (strpos($descripciones[0]['value'], $palabra->nombre) !== false) {
                                    $encontrada = true;
                                    break;
                                }
                            }
                            if ($encontrada) {
                                $listaVulnerabilidades[] = [
                                    'id' => $cveObject->codigo,
                                    'descripcion' => $descripciones[0]['value']
                                ];
                            }
                        }

                    }



                    foreach ($metricas as $index => $metrica){


                        $versionCadena =  $metrica[0]['cvssData']['version'];
                        $arregloVersion = explode('.',$versionCadena);
                        $version = $arregloVersion[0];
                        $subversion = $arregloVersion[1];
                        $metric = new Metric;
                        $metric->cve_id = $cveObject->id;
                        $metric->version = $version;
                        $metric->subversion = $subversion;
                        $metric->source = $metrica[0]['source'];
                        $metric->type = $metrica[0]['type'];
                        $metric->cvssData_version = $metrica[0]['cvssData']['version'] ?? null;
                        $metric->cvssData_vectorString = $metrica[0]['cvssData']['vectorString'] ?? null;
                        $metric->cvssData_attackVector = $metrica[0]['cvssData']['attackVector'] ?? null;
                        $metric->cvssData_accessVector = $metrica[0]['cvssData']['accessVector'] ?? null;
                        $metric->cvssData_accessComplexity = $metrica[0]['cvssData']['accessComplexity'] ?? null;
                        $metric->cvssData_attackComplexity = $metrica[0]['cvssData']['attackComplexity'] ?? null;
                        $metric->cvssData_authentication = $metrica[0]['cvssData']['authentication'] ?? null;
                        $metric->cvssData_privilegesRequired = $metrica[0]['cvssData']['privilegesRequired'] ?? null;
                        $metric->cvssData_userInteraction = $metrica[0]['cvssData']['userInteraction'] ?? null;
                        $metric->cvssData_scope = $metrica[0]['cvssData']['scope'] ?? null;
                        $metric->cvssData_confidentialityImpact = $metrica[0]['cvssData']['confidentialityImpact'] ?? null;
                        $metric->cvssData_integrityImpact = $metrica[0]['cvssData']['integrityImpact'] ?? null;
                        $metric->cvssData_availabilityImpact = $metrica[0]['cvssData']['availabilityImpact'] ?? null;
                        $metric->cvssData_baseScore = $metrica[0]['cvssData']['baseScore'] ?? null;
                        $metric->cvssData_baseSeverity = $metrica[0]['cvssData']['baseSeverity'] ?? null;
                        $metric->exploitabilityScore = $metrica[0]['exploitabilityScore'];
                        $metric->impactScore = $metrica[0]['impactScore'];
                        $metric->save();
                    }
                    if (isset($cve['weaknesses'])){
                        $weaknes = $cve['weaknesses'];
                        foreach ($weaknes as $weakne){
                            $weakneObject = new Weakne;
                            $weakneObject->cve_id = $cveObject->id;
                            $weakneObject->source = $weakne['source'];
                            $weakneObject->type = $weakne['type'];
                            $weakneObject->save();
                            $weaknedescriptions = $weakne['description'];
                            foreach ($weaknedescriptions as $weaknedescription){
                                $weaknedescriptionObject = new Weakdescription;
                                $weaknedescriptionObject->weakne_id = $weakneObject->id;
                                $weaknedescriptionObject->lang = $weaknedescription['lang'];
                                $weaknedescriptionObject->value = $weaknedescription['value'];
                                $weaknedescriptionObject->save();
                            }
                        }
                    }
                    $references = $cve['references'];
                    foreach ($references as $reference){
                        $referenceObject = new Reference;
                        $referenceObject->cve_id = $cveObject->id;
                        $referenceObject->url = $reference['url'];
                        $referenceObject->source = $reference['source'];
                        $referenceObject->save();
                    }
                }
                if (empty($listaVulnerabilidades)){
                    $vistaDatos = 'email.reporte';
                    $asunto = 'Reporte de vulnerabilidad';
                    $remitente = 'Giusseppe Viera';
                    $email = [
                        'listas' => $listaVulnerabilidades,
                    ];
                    $usuarios = User::where('state', 'A')->where('rol','!=','S')->get();
                    foreach ($usuarios as $usuario){
                        Mail::to($usuario->email)->send(new ReporteEmail($email, $vistaDatos, $asunto, $remitente));
                    }
                }
            }else{
                $correoAlternativo = 'giusseppeviera@hotmail.com';
                $vistaDatos = 'email.errorEmail';
                $asunto = 'Reporte de vulnerabilidad';
                $remitente = 'Giusseppe Viera';
                $email = [
                    "texto" => "Ha corrido bien pero no hay datos del api"
                ];
                Mail::to($correoAlternativo)->send(new ReporteEmail($email, $vistaDatos, $asunto, $remitente));
            }
        } catch (\Exception $e) {
            // Manejar el error, por ejemplo, registrar en el registro de errores
            // o enviar un correo electrónico al administrador informando del problema.
            $correoAlternativo = 'giusseppeviera@hotmail.com';
            $vistaDatos = 'email.errorEmail';
            $asunto = 'Reporte de vulnerabilidad';
            $remitente = 'Giusseppe Viera';
            $email = [
                "texto" => "Error del api se ha demorado mucho en ejecutar"
            ];
            Mail::to($correoAlternativo)->send(new ReporteEmail($email, $vistaDatos, $asunto, $remitente));
            dd($e->getMessage());
        }
    }

    public function domingo(){
        $today = date('Y-m-d');
        $lastWeek = date('Y-m-d', strtotime('-27 days'));

        $cves = CVE::whereBetween('published', [$lastWeek, $today])->get();
        $listaFiltros = Filtro::where('estado','A')->orderBy('orden')->pluck('nombre')->toArray();
        //dd($listaFiltros);
        $resultados = [];
        foreach ($cves as $cve){
             if ($cve->descriptions->isNotEmpty()){
                 foreach ($listaFiltros as $key => $filtro){
                     if (strpos($cve->descriptions[0]['value'], $filtro)){
                         $resultados[$key][] = [
                            'codigo' =>    $cve->codigo,
                            'descripcion' =>   $cve->descriptions[0]['value'],
                         ];
                     }
                 }
             }
        }

        $vistaDatos = 'email.reporteDominical';
        $asunto = 'Reporte de vulnerabilidad';
        $remitente = 'Giusseppe Viera';
        $email = [
            'listasFiltros' => $listaFiltros,
            'resultados' => $resultados
        ];

        $usuarios = User::where('state', 'A')->get();
        foreach ($usuarios as $usuario){
            Mail::to($usuario->email)->send(new ReporteEmail($email, $vistaDatos, $asunto, $remitente));
        }


    }

    public function suscribirse(Request $request){
        $email = $request->email;
        $buscarEmail = Email::where('email',$email)->first();
        if (empty($buscarEmail)){
            $subs =  new Email;
            $subs->email = $email;
            $subs->save();
            return "ok";
        }
        return "error";
    }

    public function vulnerabilidad($id){
        $cve = Cve::find($id);
        $datos = [];
        $codigo = 'unknown';
        $html = "";
        if (!empty($cve)){
            $codigo = $cve->codigo;
            $html1  = '<div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                    Datos
                                </button>
                            </h2>
                            <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">

                                 <table class="table table-bordered">

                                    <tbody>
                                      <tr>
                                        <td>Code</td>
                                        <td>'.$cve->codigo.'</td>
                                      </tr>
                                      <tr>
                                        <td>Source Identifier</td>
                                        <td>'.$cve->sourceIdentifier.'</td>
                                      </tr>
                                      <tr>
                                        <td>Published</td>
                                        <td>'.$cve->published.'</td>
                                      </tr>
                                      <tr>
                                        <td>Status</td>
                                        <td>'.$cve->vulnStatus.'</td>
                                      </tr>
                                    </tbody>
                                  </table>

                                </div>
                            </div>
                        </div>';

            $descripciones = $cve->descriptions;
            $htmlDescripcion = '';
            foreach ($descripciones as $descripcion){
                $htmlDescripcion .= ' <tr>
                                        <td> Language : '.$descripcion->lang.'</td>
                                        <td>'.$descripcion->value.'</td>
                                      </tr>';
            }


            $html2 = '<div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                    Descripción
                                </button>
                            </h2>
                            <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">

                                 <table class="table table-bordered">

                                    <tbody>
                                      '.$htmlDescripcion.'
                                    </tbody>
                                  </table>

                                </div>
                            </div>
                        </div>';

            $metricas =  $cve->metrics;
            $htmlMetrica = '0 Metricas';
            if (!empty($metricas)){
                $htmlMetrica = '';
                foreach ($metricas as $key => $metrica){
                    if ($metrica->version == 3){
                        $htmlMetrica .= '
                                     <tr class="text-success" >
                                    <tr>
                                        <td> Version : </td>
                                        <td>'.$metrica->cvssData_version.'</td>
                                    </tr>
                                    <tr>
                                        <td>Type</td>
                                        <td>'.$metrica->type.'</td>
                                    </tr>
                                    <tr>
                                        <td>Vector String</td>
                                        <td>'.$metrica->cvssData_vectorString.'</td>
                                    </tr>
                                    <tr>
                                        <td>Attack Vector</td>
                                        <td>'.$metrica->cvssData_attackVector.'</td>
                                    </tr>
                                    <tr>
                                        <td>Attack Complexity</td>
                                        <td>'.$metrica->cvssData_attackComplexity.'</td>
                                    </tr>
                                    <tr>
                                        <td>Privileges Required</td>
                                        <td>'.$metrica->cvssData_privilegesRequired.'</td>
                                    </tr>
                                    <tr>
                                        <td>User Interaction</td>
                                        <td>'.$metrica->cvssData_userInteraction.'</td>
                                    </tr>
                                    <tr>
                                        <td>Scope</td>
                                        <td>'.$metrica->cvssData_attackComplexity.'</td>
                                    </tr>
                                    <tr>
                                        <td>Confidentiality Impact</td>
                                        <td>'.$metrica->cvssData_confidentialityImpact.'</td>
                                    </tr>
                                    <tr>
                                        <td>Integrity Impact</td>
                                        <td>'.$metrica->cvssData_integrityImpact.'</td>
                                    </tr>
                                    <tr>
                                        <td>Availability Impact</td>
                                        <td>'.$metrica->cvssData_availabilityImpact.'</td>
                                    </tr>
                                    <tr>
                                        <td>Base Score</td>
                                        <td>'.$metrica->cvssData_baseScore.'</td>
                                    </tr>
                                    <tr>
                                        <td>Base Severity</td>
                                        <td>'.$metrica->cvssData_baseSeverity.'</td>
                                    </tr>
                                    <tr>
                                        <td>Exploitability Score</td>
                                        <td>'.$metrica->exploitabilityScore.'</td>
                                    </tr>
                                    <tr>
                                        <td>Impact Score</td>
                                        <td>'.$metrica->impactScore.'</td>
                                    </tr>

                                  </tr> <tr></tr>';
                    }else if($metrica->version == 2){

                        $htmlMetrica .= '
                                    <tr>
                                        <td> Version : </td>
                                        <td>'.$metrica->cvssData_version.'</td>
                                    </tr>
                                    <tr>
                                        <td>Type</td>
                                        <td>'.$metrica->type.'</td>
                                    </tr>
                                    <tr>
                                        <td>Vector String</td>
                                        <td>'.$metrica->cvssData_vectorString.'</td>
                                    </tr>
                                    <tr>
                                        <td>Access Vector</td>
                                        <td>'.$metrica->cvssData_accessVector.'</td>
                                    </tr>
                                    <tr>
                                        <td>Access Complexity</td>
                                        <td>'.$metrica->cvssData_accessComplexity.'</td>
                                    </tr>
                                    <tr>
                                        <td>Authentication</td>
                                        <td>'.$metrica->cvssData_authentication.'</td>
                                    </tr>
                                    <tr>
                                        <td>Confidentiality Impact</td>
                                        <td>'.$metrica->cvssData_confidentialityImpact.'</td>
                                    </tr>
                                    <tr>
                                        <td>Integrity Impact</td>
                                        <td>'.$metrica->cvssData_integrityImpact.'</td>
                                    </tr>
                                    <tr>
                                        <td>Availability Impact</td>
                                        <td>'.$metrica->cvssData_availabilityImpact.'</td>
                                    </tr>
                                    <tr>
                                        <td>Base Score</td>
                                        <td>'.$metrica->cvssData_baseScore.'</td>
                                    </tr>

                                    <tr>
                                        <td>Exploitability Score</td>
                                        <td>'.$metrica->exploitabilityScore.'</td>
                                    </tr>
                                    <tr>
                                        <td>Impact Score</td>
                                        <td>'.$metrica->impactScore.'</td>
                                    </tr>

                                   ';
                    }

                }

                $html3 = '<div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                    Métricas
                                </button>
                            </h2>
                            <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-collapseThree" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">

                                 <table class="table table-bordered">

                                    <tbody>
                                      '.$htmlMetrica.'
                                    </tbody>
                                  </table>

                                </div>
                            </div>
                        </div>';
            }

            $html = $html1 . $html2 . $html3;
            $response = 'ok';
        }else{
            $response = 'error';
        }

        $datos = [
            'estado' => $response,
            'html' => $html,
            'code' => $codigo
        ];

        return response()->json($datos);
    }

    public function verificarEmail($email){
         $user = User::where('email',$email)->first();
         if ($user){
             $response = 'ok';
             $id = $user->id;
         }else{
             $response = 'error';
             $id = 0;
         }
         $datos = [
             'response' => $response,
             'id' => $id,
         ];
         return response::json($datos);
    }


    public function graficos(){
        $arregloFiltros = Filtro::where('estado','A')->orderBy('orden')->pluck('nombre')->toArray();
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
        $fechaActual = date('Y-m-d');
        $mesAnterior = date('m', strtotime('-1 month', strtotime($fechaActual)));
        $mesAnterior = intval($mesAnterior);
        $resultados = [];
        $anioActual = date('Y');
        foreach ($arregloFiltros as  $filtro){
            $resultados[] = DB::table('cves')
                ->join('descriptions', 'cves.id', '=', 'descriptions.cve_id')
                ->where('descriptions.value', 'like', '%' . $filtro . '%')
                ->whereMonth('cves.published', $mesAnterior)
                ->whereYear('cves.published', $anioActual)
                ->distinct()
                ->count('cves.id');

        }

        $datos = [
            'datosPorCategoria3' => $datosPorCategoria3,
            'datosPorCategoria2' => $datosPorCategoria2,
            'cveDos' =>  $cveDos,
            'cveTres' => $cveTres,
            'arregloFiltros' => $arregloFiltros,
            'resultadoFiltros' => $resultados,
        ];
        return response::json($datos);
    }
}
