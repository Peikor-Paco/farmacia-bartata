<?php

namespace App\Libraries;

use GuzzleHttp\Client;

class GLS
{
    private $guid;

    public function __construct()
    {
        $this->guid = env('gls.guid', '');

        if (!$this->guid) {
            die("ERROR: GLS no configurado en .env");
        }
    }

    public function getStatus($idship = null)
    {

        $URL = "https://wsclientes.asmred.com/b2b.asmx?wsdl";

        $XML = '<?xml version="1.0" encoding="utf-8"?>
               <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
                 <soap12:Body>
                   <GetExpCli xmlns="http://www.asmred.com/">
                     <codigo>' . $idship . '</codigo>
                     <uid>' . $this->guid . '</uid>
                   </GetExpCli>
                 </soap12:Body>
               </soap12:Envelope>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, TRUE);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $XML);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=UTF-8"));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); //timeout in seconds

        $postResult = curl_exec($ch);

        curl_close($ch);

        $state = null;
        $state_text = '';
        $app_state = null;

        $xml = simplexml_load_string($postResult, NULL, NULL, "http://http://www.w3.org/2003/05/soap-envelope");
        $xml->registerXPathNamespace('asm', 'http://www.asmred.com/');
        $arr = $xml->xpath("//asm:GetExpCliResponse/asm:GetExpCliResult");

        if (sizeof($arr) != 0) {
            $ret2 = $arr[0]->xpath("//expediciones/exp");

            if ($ret2 != null) {
                $Num = 0;
                foreach ($ret2 as $ret) {

                    $Num2 = $Num + 1;
                    $ret3 = $ret2[$Num]->xpath("tracking_list/tracking");
                    $Num3 = 0;

                    foreach ($ret3 as $ret) {
                        $TrkCodigo  = $ret[0]->xpath("//expediciones/exp/tracking_list/tracking/codigo");
                        $TrkDesc    = $ret[0]->xpath("//expediciones/exp/tracking_list/tracking/evento");
                        if ($TrkCodigo[$Num3]) {
                            $state = $TrkCodigo[$Num3];
                            $state_text = $TrkDesc[$Num3];
                        }

                        $Num3 = $Num3 + 1;
                    }
                }
            }
        }

        if($state !== NULL){
            switch ($state) {
                case -10:
                    $app_state = ENVIO_CREADO;
                    break;
                case 0:
                    $app_state = ENVIO_RECIBIDO;
                    break;
                case 19:
                case 3:
                    $app_state = ENVIO_TRANSITO;
                    break;
                case 6:
                case 22:
                case 25:
                    $app_state = ENVIO_REPARTO;
                    break;
                case 7:
                    $app_state = ENVIO_ENTREGADO;
                    break;

                default:
                    $app_state = ENVIO_INCIDENCIA;
                    break;
            }

        }

        return [
            'state' => $app_state,
            'text' => $state_text
        ];
    }
}
