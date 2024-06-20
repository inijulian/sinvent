<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\Barang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        if ($searchTerm) {
        $rsetBarangMasuk = BarangMasuk::where('tgl_masuk', 'like', '%' . $searchTerm . '%')
            ->orWhere('qty_masuk', 'like', '%' . $searchTerm . '%')
            ->orWhere('id', 'like', '%' . $searchTerm . '%')
            ->orWhereHas('barang', function ($query) use ($searchTerm) {
                $query->where('id', 'like', '%' . $searchTerm . '%')
                      ->orWhere('merk', 'like', '%' . $searchTerm . '%');
            })
            ->get();
        } else {
            $rsetBarangMasuk = BarangMasuk::all();
        }

    return view('v_barangmasuk.index', compact('rsetBarangMasuk'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $barangId = Barang::all();
        return view('v_barangmasuk.create',compact('barangId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tgl_masuk' => 'required',
            'qty_masuk' => 'required|integer|min:1', // Menambahkan validasi qty_masuk minimal 1
            'barang_id' => 'required',
        ], [
            'tgl_masuk.required' => 'Tanggal masuk harus diisi.',
            'qty_masuk.required' => 'Qty masuk harus diisi.',
            'qty_masuk.integer' => 'Qty masuk harus berupa angka.',
            'qty_masuk.min' => 'Qty masuk minimal adalah 1.',
            'barang_id.required' => 'Barang harus dipilih.',
        ]);
    
        // Memulai transaksi database
        DB::beginTransaction();
    
        try {
            // Simpan data barang masuk ke dalam database
            BarangMasuk::create([
                'tgl_masuk' => $request->tgl_masuk,
                'qty_masuk' => $request->qty_masuk,
                'barang_id' => $request->barang_id,
            ]);
    
            // Commit transaksi jika semua operasi berhasil
            DB::commit();
    
            // Redirect ke halaman indeks barang masuk dengan pesan sukses
            return redirect()->route('barangmasuk.index')->with(['success' => 'Data Barang Masuk Berhasil Disimpan!']);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();
    
            // Tangani kesalahan saat menyimpan data
            return back()->withErrors(['message' => 'Terjadi kesalahan saat menyimpan data.'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rsetBarangMasuk = BarangMasuk::find($id);

        return view('v_barangmasuk.show', compact('rsetBarangMasuk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $rsetBarangMasuk = BarangMasuk::find($id);
        $barangID = Barang::all();
        //return $rsetBarangMasuk;
        return view('v_barangmasuk.edit', compact('rsetBarangMasuk', 'barangID'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $request->validate([
        'tgl_masuk' => 'required',
        'qty_masuk' => 'required',
        'barang_id' => 'required',
    ]);

    $rsetBarangMasuk = BarangMasuk::find($id);
    $rsetBarangMasuk->update($request->all());

    return redirect()->route('barangmasuk.index')->with(['success' => 'Data Berhasil Diubah!']);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $rsetBarangMasuk = BarangMasuk::find($id);
    
            // Cek apakah stok barang cukup sebelum menghapus
            $stok_barang = $rsetBarangMasuk->barang->stok;
            $qty_masuk = $rsetBarangMasuk->qty_masuk;
            if ($stok_barang < $qty_masuk) {
                throw new \Exception('Stok barang tidak mencukupi untuk menghapus entri barang masuk ini');
            }
    
            // Hapus entri barang masuk jika stok mencukupi
            $rsetBarangMasuk->delete();
    
            return redirect()->route('barangmasuk.index')->with(['success' => 'Data Berhasil Dihapus!']);
        } catch (\Exception $e) {
            return redirect()->route('barangmasuk.index')->with(['error' => $e->getMessage()]);
        }
    }
}