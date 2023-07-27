<?php

namespace App\Http\Controllers;

use App\Models\Cve;
use App\Models\Cvsdo;
use App\Models\Cvstre;
use App\Models\Description;
use App\Models\Email;
use App\Models\Metric;
use App\Models\Reference;
use App\Models\Weakdescription;
use App\Models\Weakne;
use Mail;
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
                    $listaVulnerabilidades[] = [
                        'id' => $cveObject->codigo,
                        'descripcion' => $descripciones[0]['value']
                    ];

                    $metricas = $cve['metrics'];
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
                $vistaDatos = 'email.reporte';
                $asunto = 'Reporte de vulnerabilidad';
                $remitente = 'Giusseppe Viera';
                $email = [
                    'listas' => $listaVulnerabilidades,
                ];

                $usuarios = Email::where('estado', 'A')->get();
                foreach ($usuarios as $usuario){
                    Mail::to($usuario->email)->send(new ReporteEmail($email, $vistaDatos, $asunto, $remitente));
                }

            }
        } catch (\Exception $e) {
            // Manejar el error, por ejemplo, registrar en el registro de errores
            // o enviar un correo electrónico al administrador informando del problema.
            dd($e->getMessage());
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
}
