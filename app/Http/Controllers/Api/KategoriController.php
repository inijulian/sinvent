<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Kategori::all();
        $data = array('data'=>$categories);
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori'=>'required|in:A,M,BHP,BTHP',
            'deskripsi'=>'required|max:255',    
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $categories = Kategori::create([
            'kategori'=>$request->kategori,
            'deskripsi'=>$request->deskripsi,
        ]);

        return response()->json($categories, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
