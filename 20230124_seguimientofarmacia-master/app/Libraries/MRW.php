<?php

namespace App\Libraries;

use GuzzleHttp\Client;

class MRW
{
    private $password;
    private $abonado;
    private $franquicia;
    private $wsdl_url;

    public function __construct()
    {
        $this->wsdl_url = 'http://seguimiento.mrw.es/swc/wssgmntnvs.asmx?WSDL';
        $this->franquicia = env('mrw.franquicia', '');
        $this->abonado = env('mrw.abonado', '');
        $this->password = env('mrw.password', '');

        if (!$this->franquicia || !$this->abonado || !$this->password) {
            die("ERROR: MRW no configurado en .env");
        }
    }

    public function getStatus($idship = null)
    {

        $app_state = null;
        $arrDatosWebservice = $this->sendTrackingMRW($idship);
        $state = null;
        $state_text = '';

        if ($arrDatosWebservice) {
            if ($arrDatosWebservice->SeguimientoNumeroEnvioMRWNacionalResult->Envio->Estado) {
                $state = $arrDatosWebservice->SeguimientoNumeroEnvioMRWNacionalResult->Envio->Estado;
                switch ($state) {
                    case 19:
                    case 39:
                    case 40:
                    case 46:
                    case 47:
                    case 48:
                    case 61:
                    case 70:
                    case 91:
                    case 93:
                    case 95:
                        $app_state = ENVIO_CREADO;
                        break;
                    case 17:
                    case 49:
                        $app_state = ENVIO_RECIBIDO;
                        break;
                    case 13:
                    case 11:
                    case 15:
                    case 57:
                    case 58:
                    case 59:
                    case 68:
                    case 69:
                    case 71:
                    case 72:
                    case 75:
                    case 77:
                    case 78:
                    case 79:
                    case 90:
                    case 96:
                        $app_state = ENVIO_TRANSITO;
                        break;
                    case 16:
                    case 73:
                        $app_state = ENVIO_REPARTO;
                        break;
                    case 0:
                        $app_state = ENVIO_ENTREGADO;
                        break;

                    default:
                        $app_state = ENVIO_INCIDENCIA;
                        $state_text = $arrDatosWebservice->SeguimientoNumeroEnvioMRWNacionalResult->Envio->EstadoDescripcion;
                        break;
                }
            }
        }

        return [
            'state' => $app_state,
            'text' => $state_text
        ];
    }

    private function sendTrackingMRW($idship = null)
    {
        try {
            $clientMRW = new \SoapClient(
                $this->wsdl_url,
                array(
                    'trace' => TRUE
                )
            );
        } catch (SoapFault $e) {
            $errr = "sendTrackingMRW:-> Error creando cliente SOAP:" . PHP_EOL . $e->__toString();
            if ($this->debug_activo == '1')
                $this->writeTolog($errr);
            return false;
        }

        $parametros = array(
            'Franquicia'    => $this->franquicia,
            'Cliente'       => $this->abonado,
            'Password'      => $this->password,
            'NumeroMRW'     => $idship,
            'Referencia'    => $idship,
            'Agrupado'      => 0,
        );

        try {
            $responseCode = $clientMRW->SeguimientoNumeroEnvioMRWNacional($parametros);
        } catch (SoapFault $exception) {
            return false;
        }
        if (0 == $responseCode->SeguimientoNumeroEnvioMRWNacionalResult->Estado) {
            $result = false;
        } else if (1 == $responseCode->SeguimientoNumeroEnvioMRWNacionalResult->Estado) {
            $result = $responseCode;
        } else {
            $result = false;
        }
        unset($clientMRW); // Destruimos el objeto cliente

        return $result;
    }
}
