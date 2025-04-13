<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data['title'] = ucfirst("Home");
        
        return view('templates/home/header', $data)
            . view('home')
            . view('templates/home/footer');
    }
}
