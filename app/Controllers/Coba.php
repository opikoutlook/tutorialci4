<?php

namespace App\Controllers;

class Coba extends BaseController
{
	public function index()
	{
		echo 'Ini adalah Controller Coba method index';
	}

	public function about($nama)
    {
        echo "Hello nama saya $nama";
    }




//-------------------------//
}
