<?php

namespace App\Controllers;

class Pages extends BaseController
{
	public function index()
	{
		$data['title'] = "Halaman Home";
		
		echo view("pages/home", $data);
		
	}

	public function about()
	{
		$data['title'] = "Halaman About Me";
		
		echo view("pages/about", $data);
		
	}

	public function contact()
	{
		$data['title'] = "Halaman Contact";
		
		echo view("pages/contact", $data);
	}


//---------------------------------
}
