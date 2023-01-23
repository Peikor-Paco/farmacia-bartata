<?php

namespace App\Controllers;

use GuzzleHttp\Client;
use App\Libraries\Prestashop;
use App\Libraries\CorreosExpress;
use App\Libraries\GLS;
use App\Libraries\MRW;
use App\Libraries\Nacex;
use CodeIgniter\HTTP\RequestInterface;

class TrackingController extends BaseController
{

	public function info($reference = null, $phone = null)
	{
		helper('url');
		helper('form');

		if (!$reference) {
			$reference = $this->request->getVar('reference');
		}

		if (!$phone) {
			$phone = $this->request->getVar('phone');
		}

		$reference = trim($reference);
		$phone = trim($phone);

		if (!$reference && !$phone) {
			$data = [
				'error' => 'Debes introducir la referencia de pedido o tu teléfono'
			];

			return view('home', $data);
		}

		if ($reference) {
			//Prestashop reference validation
			$validation =  \Config\Services::validation();
			if (!$validation->run(['reference' => $reference], 'prestashop_reference')) {
				$data = [
					'error' => 'Referencia de pedido no válida'
				];

				return view('home', $data);
			}
		}

		if ($phone) {
			//Prestashop reference validation
			$validation =  \Config\Services::validation();
			if (!$validation->run(['phone' => $phone], 'prestashop_phone')) {
				$data = [
					'error' => 'Teléfono no válido'
				];

				return view('home', $data);
			}
		}

		$prestashop = new Prestashop();
		$prestashop_info = $prestashop->getTracking($reference, $phone);

		if (!$prestashop_info) {
			$data = [
				'error' => 'Se ha producido un error inesperado, por favor inténtelo de nuevo más tarde'
			];
			return view('home', $data);
		}

		if (isset($prestashop_info->status) && !$prestashop_info->status) {
			$data = [
				'error' => 'No se ha encontrado ningún envío con esa información'
			];
			return view('home', $data);
		}

		if (isset($prestashop_info->status) && $prestashop_info->status && $prestashop_info->data->order_status !== 'Enviado') {
			$data = [
				'warning' => 'Su pedido aún no ha sido enviado'
			];
			return view('home', $data);
		}

		$result = false;

		if (isset($prestashop_info->status) && $prestashop_info->status && $prestashop_info->data->order_status == 'Enviado') {
			$carrier = $prestashop_info->data->carrier;
			$shipping_number = $prestashop_info->data->shipping_number;
			$reference = $reference ? $reference : $prestashop_info->data->reference;

			switch ($carrier) {
				case 'Nacex':
					$nacex = new Nacex();
					$result = $nacex->getStatus($shipping_number);
					$result['carrier'] = 'Nacex';
					break;
				case 'Correos Express':
				case 'Correos Express Baleares':
					$correos = new CorreosExpress();
					$result = $correos->getStatus($shipping_number);
					$result['carrier'] = 'Correos Express';
					break;
				case 'Agencia recomendada por Farmacia Barata':
					$mrw = new MRW();
					$result = $mrw->getStatus($shipping_number);
					if ($result['state'] !== NULL)
						$result['carrier'] = 'MRW';
					else {
						$gls = new GLS();
						$result = $gls->getStatus($shipping_number);
						if ($result['state'] !== NULL)
							$result['carrier'] = 'GLS';
						else{
							$correos = new CorreosExpress();
							$result = $correos->getStatus($shipping_number);
							$result['carrier'] = 'Correos Express';
						}
					}
					break;
				case 'GLS':
					$gls = new GLS();
					$result = $gls->getStatus($shipping_number);
					$result['carrier'] = 'GLS';
					break;
				case 'MRW':
				case 'MRW medicamentos':
					$mrw = new MRW();
					$result = $mrw->getStatus($shipping_number);
					$result['carrier'] = 'MRW';
					break;
				default:
					$result = false;
					break;
			}
		}

		if (!$result) {
			$data = [
				'error' => 'El transportista no tiene información aún, inténtelo más tarde'
			];
			return view('home', $data);
		}

		$data = $this->_getViewData($result);

		if (!$data || $data['state'] == 'Error') {
			$data = [
				'error' => 'Se ha producido un error inesperado, por favor inténtelo de nuevo más tarde'
			];
			return view('home', $data);
		}

		$data['carrier'] = $result['carrier'];
		$data['shipping_number'] = $prestashop_info->data->shipping_number;
		$data['reference'] = $reference;

		return view('tracking', $data);
	}

	private function _getViewData($result)
	{
		$data = false;

		if (!isset($result['state'])) {
			$data = [
				'state' => 'Error'
			];
		}

		if ($result['state'] == ENVIO_ERROR) {
			$data = [
				'state' => 'Error',
			];
		}

		if ($result['state'] == ENVIO_CREADO) {
			$data = [
				'state' => 'Creado',
				'imgcabecera' => 'Seguimiento_1120x119px_FB01.png',
				'imgcuerpo' => '1trackingFB_notificado-agencia.jpg',
				'text' => '¡Ya hemos notificado tu envío a la agencia de transporte por lo que muy pronto pasarán por nuestras instalaciones para recoger tu pedido!</br><br/>
				En 24-48 horas hábiles deberías tenerlo contigo, si no es así por favor ponte en contacto con nosotros.<br/><br/>
				Gracias por tu confianza.'
			];
		}

		if ($result['state'] == ENVIO_RECIBIDO) {
			$data = [
				'state' => 'Recibido',
				'imgcabecera' => 'Seguimiento_1120x119px_FB02.png',
				'imgcuerpo' => '2trackingFB_pendiente-deenvio.jpg',
				'text' => '¡La agencia de transporte ha recogido ya tu paquete de nuestras instalaciones!<br/><br/>
				Muy pronto lo tendrás en tu casa, si por alguna razón no te hubiera llegado y la agencia de transporte no se hubiera puesto en contacto contigo en 24-48 horas, por favor háznoslo saber para solucionarlo.'
			];
		}

		if ($result['state'] == ENVIO_TRANSITO) {
			$data = [
				'state' => 'Transito',
				'imgcabecera' => 'Seguimiento_1120x119px_FB03.png',
				'imgcuerpo' => '3trackingFB_en-camino.jpg',
				'text' => 'Tu pedido ya está llegando a tu ciudad por lo que en 24-48 horas te será entregado, si no fuera así, por favor llámanos para solucionarlo!'
			];
		}

		if ($result['state'] == ENVIO_REPARTO) {
			$data = [
				'state' => 'Reparto',
				'imgcabecera' => 'Seguimiento_1120x119px_FB04.png',
				'imgcuerpo' => '4trackingFB_en-reparto.jpg',
				'text' => 'Durante el día de hoy te debería ser entregado tu pedido.<br/><br/>
				Gracias por tu confianza!'
			];
		}

		if ($result['state'] == ENVIO_ENTREGADO) {
			$data = [
				'state' => 'Entregado',
				'imgcabecera' => 'Seguimiento_1120x119px_FB05.png',
				'imgcuerpo' => '5trackingFB_entregado.jpg',
				'text' => 'Muchas gracias por confiar en nosotros, esperamos verte de nuevo muy pronto!'
			];
		}

		if ($result['state'] == ENVIO_INCIDENCIA) {
			$data = [
				'state' => 'Incidencia',
				'imgcabecera' => 'Seguimiento_1120x119px_FB01.png',
				'imgcuerpo' => '6trackingFB_incidencia.jpg',
				'text' => 'Algo ha sucedido 😟 <br/><br/>
				El transportista ha marcado tu pedido con alguna incidencia por lo que se pondrá en contacto contigo para acordar una nueva entrega. ',
				'state_text' => $result['text']
			];
		}

        $file = $this->isBannerUploaded();
		$data['bannerpath'] = $file ? '/assets/img/banner.jpg' : '';

		return $data;
	}

	private function isBannerUploaded()
    {
        if (file_exists(FCPATH . 'assets/img/banner.jpg')) {
            return true;
        }

        return false;
    }
}
