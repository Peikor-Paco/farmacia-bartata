<?php

/**
 *
 * http://gprs.nacex.com/nacex_ws/ws?method=getEstadoExpedicion
 * &user=PARAFARMACIUDADJARDIN
 * &pass=1D0BF1061E0A24EBE77D959E8661516C
 * &data=ref=01459|origen=1403|expe_codigo=301246343
 */


/**RETURN (| pipeline)
Identificador único de la expedición
Fecha del estado (dd/mm/yyyy)
Hora del estado (hh:mm)
Observaciones del estado (persona entrega, plataforma, código de recogida,etc.)
Estado (OK, RECOGIDO, TRANSITO, REPARTO, INCIDENCIA)
Código del estado o incidencia (devuelve la última EXPEDICIÓN vinculada a esta referencia)
Código de la agencia de origen
Número de albaran de la agencia
En una sola línea figurará, para cada expedición relacionada encontrada, el tipo de relación (R - Retorno, D - Devolución, X - Reexpedición) más los mismos campos de retorno anteriores todos separados por pipe "|". Cada expedición relacionada encontrada quedará delimitada por el carácter "~"
 */


namespace App\Libraries;

use GuzzleHttp\Client;

class Nacex
{
    private $cliente;
    private $agencia;
    private $userWS;
    private $passWS;

    public function __construct()
    {
        $this->cliente = env('nacex.cliente', '');
        $this->agencia = env('nacex.agencia', '');
        $this->userWS = env('nacex.userWS', '');
        $this->passWS = env('nacex.passWS', '');

        if (!$this->cliente) {
            die("ERROR: Nacex no configurado en .env");
        }
    }

    public function getStatus($idship = null)
    {

        $url = "http://gprs.nacex.com/nacex_ws/ws?method=getEstadoExpedicion";

        $url .=  "&user={$this->userWS}&pass={$this->passWS}&data=ref={$this->cliente}|origen={$this->agencia}|expe_codigo={$idship}";

        $state = null;
        $state_text = '';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); //timeout in seconds

        $data = curl_exec($ch);
        curl_close($ch);

        $data = explode('|', $data);
        $state = $data[4];
        $state_text = $data[3];

        switch ($state) {
            case 'PENDIENTE':
                $app_state = ENVIO_CREADO;
                break;
            case 'RECOGIDO':
                $app_state = ENVIO_RECIBIDO;
                break;
            case 'TRANSITO':
                $app_state = ENVIO_TRANSITO;
                break;
            case 'REPARTO':
                $app_state = ENVIO_REPARTO;
                break;
            case 'OK':
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
