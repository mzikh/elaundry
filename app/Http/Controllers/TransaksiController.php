<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\Kategori;
use carbon\carbon;
use Session;
use PDF;
class TransaksiController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');

    }
    
    public function index()
    {
        $dataTransaksi = Transaksi::where('user_id',Auth::user()->id)
            ->where('status_order', 'Proses')
            ->orderBy('id','DESC')
            ->get();

        $kategori = Kategori::where('user_id',Auth::id())->get();

        $y = date('Y');
        $number = mt_rand(1000, 9999);

        // Nomor Form otomatis
        $newID = $number. Auth::user()->id .''.$y;

        return view('menu.data_transaksi', compact('newID','kategori','dataTransaksi'));
    }

    public function store(Request $request)
    {
        Transaksi::create([
            'no_transaksi'  => $request-> no_transaksi,
            'tgl_transaksi' => Carbon::now()->format('d-M-y'),
            'user_id'       => Auth::user()->id,
            'customer'      => $request-> customer,
            'berat'         => $request-> berat,
            'nama_kategori' => $request-> nama_kategori,
            'harga'         => preg_replace('/[^A-Za-z0-9\-]/', '',$request->harga),
            'harga_akhir'   => preg_replace('/[^A-Za-z0-9\-]/', '',$request->harga) * $request-> berat,
            'hari'          => $request-> hari,
            'tgl'           => Carbon::now()->day,
            'bulan'         => Carbon::now()->month,
            'tahun'         => Carbon::now()->year,
            'tgl_ambil'     => Carbon::now()->addDays($request->hari)->format('d-M-y'), 
        ]);

        Session::flash('success','Tambah Data Transaksi Berhasil');
        return redirect('data_transaksi');
    }

    public function edit(string $id)
    {
        $transaksi = Transaksi::find($id);
        return response()->json($transaksi);
    }

    public function update(Request $request, string $id)
    {
        Transaksi::find($id)->update([
            'customer'      => $request->customer,
            'berat'         => $request->berat,
            'nama_kategori' => $request->nama_kategori,
            'harga'         => preg_replace('/[^A-Za-z0-9\-]/', '',$request->harga),
            'harga_akhir'   => preg_replace('/[^A-Za-z0-9\-]/', '',$request->harga) * $request-> berat,
            'hari'          => $request-> hari,
        ]);
         
        Session::flash('success','Edit Data Transaksi Berhasil');
        return redirect('data_transaksi');
    }

    public function destroy(string $id)
    {
        Transaksi::find($id)->delete();

        Session::flash('success','Hapus Data Transaksi Berhasil');
        return redirect('data_transaksi');
    }
    
    public function selesai(string $id)
    {
        Transaksi::find($id)->update([
            'status_order' => "Selesai"
        ]);

        Session::flash('success','Selesaikan Data Transaksi Berhasil');
        return redirect('data_transaksi');
    }
    public function cetak_pdf(string $id)
    {
        $data = Transaksi::with('user')->find($id);
        $pdf = PDF::loadView('menu.cetak', ['data' => $data])->setPaper('a4', 'landscape');
        return $pdf->stream();
    }
}
