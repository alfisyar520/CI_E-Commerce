<?php

namespace App\Controllers;

class Transaksi extends BaseController
{
    protected $requestS;

    public function __construct()
    {
        helper('form');
        $this->validation = \Config\Services::validation();
        $this->session = session();
        $this->requestS = service("request");
    }

    public function view()
    {
        $id = $this->requestS->uri->getSegment(3);

        $transaksiModel = new \App\Models\TransaksiModel();
        $transaksi = $transaksiModel
            ->join('barang', 'barang.id=transaksi.id_barang')
            ->join('user', 'user.id=transaksi.id_pembeli')
            ->where('transaksi.id', $id)
            ->first();
        return view('transaksi/view', [
            'transaksi' => $transaksi,
        ]);
    }
}
