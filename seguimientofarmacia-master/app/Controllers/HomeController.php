<?php

namespace App\Controllers;
use GuzzleHttp\Client;
use App\Libraries\Prestashop;

class HomeController extends BaseController
{
	public function index()
	{
		// $test = new Prestashop();
		// $test->getTracking('asdas');

		// helper('array');

		// $client = new Client();
		// $response = $client->get('https://jsonplaceholder.typicode.com/todos/');
		// log_message('error', 'Some variable did not contain a value.');

		// $data = array(
		// 	'test' => (json_decode($response->getBody())));
		helper('url');
		helper('form');
		return view('home');
	}
}
