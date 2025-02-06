<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogeController extends Controller
{
    public function produitIndex(){
        return view('bloge/produit');
    }
}