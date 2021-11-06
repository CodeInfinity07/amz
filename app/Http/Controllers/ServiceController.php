<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request){
      $asin = $request->asin;
      return Redirect('/api/scrape/'.$asin);
    }
}
