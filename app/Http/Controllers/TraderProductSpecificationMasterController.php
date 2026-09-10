<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TraderProductSpecificationMasterController extends Controller
{
    public function index()
    {
        return view('trader-product-specification-masters.index');
    }

    public function data(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }
}