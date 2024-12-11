<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // You can customize this message or make it dynamic
        $message = 'You do not have access to this resource.';
        return view('home', compact('message'));
    }
}
