<?php

namespace App\Libraries;

use GuzzleHttp\Client;

class Prestashop
{

    private $url;
    private $token;

    public function __construct()
    {
        $this->url = env('prestashop.url', '');
        $this->token = env('prestashop.token', '');

        if (!$this->url || !$this->token) {
            die("ERROR: Prestashop no configurado en .env");
        }
    }

    /**
     * getTracking
     * Llama al WS de Prestashop para obtener el estado de un pedido
     *
     * @param  string $reference: número de pedido
     * @param  string $phone: número de teléfono
     * @return object $result: estado y tracking/carrier si corresponde
     */
    public function getTracking($reference = null, $phone = null)
    {
        if (!$reference && !$phone) {
            return null;
        }

        $client = new Client();

        $response = $client->request('GET', $this->url, [
            'verify' => false,
            'query' => [
                'token' => $this->token,
                'reference' => $reference,
                'phone' => $phone
            ]
        ]);

        $result = $response->getBody()->getContents();

        // log_message('info', 'Respuesta Prestashop --> ' . $result);

        return json_decode($result);
    }
}
