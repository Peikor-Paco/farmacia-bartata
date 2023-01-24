<?php

namespace App\Libraries;

use GuzzleHttp\Client;

class CorreosExpress
{
    private $solicitante;
    private $token;
    private $userWS;
    private $passWS;

    public function __construct()
    {
        $this->solicitante = env('correosexpress.solicitante', '');
        $this->userWS = env('correosexpress.userWS', '');
        $this->passWS = env('correosexpress.passWS', '');

        if (!$this->solicitante) {
            die("ERROR: Correos no configurado en .env");
        }
    }

    public function getStatus($idship = null)
    {
        //1. gen xml
        $soap_request = '
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mes="messages.seguimientoEnvio.ws.chx.es">
        <soapenv:Header/>
        <soapenv:Body>
            <mes:seguimientoEnvio>
            <mes:solicitante>' . $this->solicitante . '</mes:solicitante>
            <mes:dato>' . $idship . '</mes:dato>
            <!--Optional:-->
            <mes:password></mes:password>
            </mes:seguimientoEnvio>
        </soapenv:Body>
        </soapenv:Envelope>';

        //send xml to webservice
        $url = 'https://www.correosexpress.com/wpsc/services/SeguimientoEnvio?wsdl';
        $header = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: \"" . $url . "\"",
            "Content-length: " . strlen($soap_request),
        );

        $ch = curl_init();
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)',
            CURLOPT_URL => $url,
            CURLOPT_USERPWD => $this->userWS . ":" . $this->passWS,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $soap_request,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 5
        );

        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        if (!$output) {
            return ENVIO_ERROR;
        } else {
            // echo(htmlentities($output));
            $dom = new \DOMDocument();
            $dom->recover = TRUE;
            $dom->loadXML($output);

            /**
             * HISTORICO  : historicoEstados
            <xs:element minOccurs="0" name="codEstado" type="xs:string" nillable="true"/>
            <xs:element minOccurs="0" name="codIncEstado" type="xs:string" nillable="true"/>

            1 -- PENDIENTE RECEPCION
            2 -- EN ARRASTRE
            4 -- TRANSITO
            6 -- DELEGACION DESTINO
            8 -- EN REPARTO
            9 -- REPARTO FALLIDO ( INCIDENCIA ) Relleno descIncEstado
            11 -- NUEVO REPARTO ( INCIDENCIA ) Relleno descIncEstado
            12 -- ENTREGADO

            <xs:element minOccurs="0" name="descEstado" type="xs:string" nillable="true"/>
            <xs:element minOccurs="0" name="descIncEstado" type="xs:string" nillable="true"/>
            <xs:element minOccurs="0" name="fechaEstado" type="xs:string" nillable="true"/>
            <xs:element minOccurs="0" name="horaEstado" type="xs:string" nillable="true"/>
             **/
            $xml = $dom->getElementsByTagName("codEstado");
            $state = "";
            foreach ($xml as $element) {
                $state = $element->nodeValue;
                break;
            }

            $xml = $dom->getElementsByTagName("descIncEstado");
            $state_text = "";
            foreach ($xml as $element) {
                $state_text = $element->nodeValue;
                break;
            }

            curl_close($ch);


            switch ($state) {
                case 1:
                    $app_state = ENVIO_CREADO;
                    break;
                case 2:
                    $app_state = ENVIO_RECIBIDO;
                    break;
                case 4:
                case 6:
                    $app_state = ENVIO_TRANSITO;
                    break;
                case 8:
                    $app_state = ENVIO_REPARTO;
                    break;
                case 12:
                    $app_state = ENVIO_ENTREGADO;
                    break;

                default:
                    $app_state = ENVIO_INCIDENCIA;
                    break;
            }

            return [
                'state' => $app_state,
                'text' => $state_text
            ];
        }
    }
}
