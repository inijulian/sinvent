<?php

namespace App\Http\Controllers\Api;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

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
        $categories = Kategori::find($id);
        if(is_null($categories)){
            return response()->json(['message'=>'id not found'], 404);
        } else {
        return response()->json($categories);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'kategori'=>'required|in:A,M,BHP,BTHP',
            'deskripsi'=>'required|max:255',    
        ]);

        $category = Kategori::find($id);
        if(is_null($category)){
            return response()->json(['message'=>'Record not found'], 404);
        } else {
            $category->update($request->all());
            return response()->json($category);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Kategori::findOrFail($id);
        $category->delete();
        
        return response()->json(['message'=>'Record Deleted']);
    }
}
