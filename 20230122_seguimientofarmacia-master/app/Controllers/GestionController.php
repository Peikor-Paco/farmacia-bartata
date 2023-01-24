<?php

namespace App\Controllers;

use GuzzleHttp\Client;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class GestionController extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        //--------------------------------------------------------------------
        // Preload any models, libraries, etc, here.
        //--------------------------------------------------------------------
        $dominio = 'Area restringida';

        // usuario => contraseña
        $usuarios = array('farmaciabarata' => 'oRT0Q#h1V6f3');

        if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Digest realm="' . $dominio .
                '",qop="auth",nonce="' . uniqid() . '",opaque="' . md5($dominio) . '"');

            die('Necesaria autenticación');
        }

        // Analizar la variable PHP_AUTH_DIGEST
        if (
            !($datos = $this->analizar_http_digest($_SERVER['PHP_AUTH_DIGEST'])) ||
            !isset($usuarios[$datos['username']])
        ){
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Digest realm="' . $dominio .
                '",qop="auth",nonce="' . uniqid() . '",opaque="' . md5($dominio) . '"');
            die('Credenciales incorrectas');
        }


        // Generar una respuesta válida
        $A1 = md5($datos['username'] . ':' . $dominio . ':' . $usuarios[$datos['username']]);
        $A2 = md5($_SERVER['REQUEST_METHOD'] . ':' . $datos['uri']);
        $respuesta_válida = md5($A1 . ':' . $datos['nonce'] . ':' . $datos['nc'] . ':' . $datos['cnonce'] . ':' . $datos['qop'] . ':' . $A2);

        if ($datos['response'] != $respuesta_válida)
            die('Credenciales incorrectas');

    }

    private function analizar_http_digest($txt)
    {
        // Protección contra datos ausentes
        $partes_necesarias = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1);
        $datos = array();
        $claves = implode('|', array_keys($partes_necesarias));

        preg_match_all('@(' . $claves . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $coincidencias, PREG_SET_ORDER);

        foreach ($coincidencias as $c) {
            $datos[$c[1]] = $c[3] ? $c[3] : $c[4];
            unset($partes_necesarias[$c[1]]);
        }

        return $partes_necesarias ? false : $datos;
    }

    public function index()
    {
        helper('url');
        helper('form');
        $file = $this->isBannerUploaded();
        $data = [
            'bannerpath' => $file ? '/assets/img/banner.jpg' : ''
        ];
        return view('gestion', $data);
    }

    public function banner()
    {
        helper('url');
        helper('form');
        helper('filesystem');

        $validationRule = [
            'banner' => [
                'label' => 'Banner',
                'rules' => 'uploaded[banner]'
                    . '|is_image[banner]'
                    . '|mime_in[banner,image/jpg,image/jpeg]'
            ],
        ];
        if (!$this->validate($validationRule)) {
            $data = ['errors' => $this->validator->getErrors()];
            return view('gestion', $data);
        }

        $img = $this->request->getFile('banner');

        if (!$img->hasMoved()) {
            $img->move('assets/img/', 'banner.jpg', true);
            $data = [
                'bannerpath' => '/assets/img/banner.jpg'
            ];
            return view('gestion', $data);
        }

        $file = $this->isBannerUploaded();


        $data = [
            'bannerpath' => $file ? '/assets/img/banner.jpg' : '',
            'errors' => 'Ha habido un error'
        ];

        return view('gestion', $data);
    }

    public function delete()
    {
        helper('url');
        helper('form');

        try{
            unlink(FCPATH . 'assets/img/banner.jpg');
        }catch(Exception $e){

        }

        return redirect()->to('gestion');
    }

    private function isBannerUploaded()
    {
        if (file_exists(FCPATH . 'assets/img/banner.jpg')) {
            return true;
        }

        return false;
    }
}
