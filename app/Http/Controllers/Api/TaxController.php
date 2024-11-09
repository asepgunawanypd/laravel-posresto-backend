<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    //index
    public function index()
    {
        //get data tax
        $discounts = \App\Models\Tax::all();

        return response()->json([
            'status' => 'success',
            'data' => $discounts
        ]);
    }

    //store
    public function store(Request $request)
    {
        //validate request
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'value' => 'required',

        ]);

        //create tax
        $tax = \App\Models\Tax::create($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $tax
        ], 201);
    }
}
