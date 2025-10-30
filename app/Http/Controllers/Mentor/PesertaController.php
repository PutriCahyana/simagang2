<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;

use App\Models\Peserta;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    //Read : ambil semua peserta
    public function index()
    {
        $peserta = \App\Models\Peserta::all();
        $judul = 'Daftar Peserta Magang';

        return view('mentor.peserta.index', compact('peserta', 'judul'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nim' => 'required',
            'kampus' => 'required',
            'jurusan' => 'required',
        ]);

        return Peserta::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Peserta::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $peserta = Peserta::findOrFail($id);
        $peserta->update($request->all());
        return $peserta;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return Peserta::destroy($id);
    }
}
