<?php

namespace App\Controllers;

use TCPDF;

class Transaksi extends BaseController
{
    protected $requestS;

    public function __construct()
    {
        helper('form');
        $this->validation = \Config\Services::validation();
        $this->session = session();
        $this->requestS = service("request");
        $this->email = \Config\Services::email();
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

    public function index()
    {
        $transaksiModel = new \App\Models\TransaksiModel();
        $model = $transaksiModel->findAll();
        return view('transaksi/index', [
            'model' => $model,
        ]);
    }

    public function invoice()
    {
        $id = $this->requestS->uri->getSegment(3);

        $transaksiModel = new \App\Models\TransaksiModel();
        $transaksi = $transaksiModel->find($id);

        $userModel = new \App\Models\UserModel();
        $pembeli = $userModel->find($transaksi->id_pembeli);

        $barangModel = new \App\Models\BarangModel();
        $barang = $barangModel->find($transaksi->id_barang);

        $html = view('transaksi/invoice', [
            'transaksi' => $transaksi,
            'pembeli' => $pembeli,
            'barang' => $barang,
        ]);

        // create new PDF document
        $pdf = new TCPDF('L', PDF_UNIT, 'A5', true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Alfisyar Jefry Pranata');
        $pdf->SetTitle('Invoice');
        $pdf->SetSubject('Invoice');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->addPage();

        // output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        // print_r('ss');
        // exit();

        //line ini penting untuk ci4
        // $this->response->setContentType('application/pdf');

        //Close and output PDF document
        $pdf->Output(__DIR__ . '/../../public/uploads/Invoice.pdf', 'F');

        $attachment = base_url('uploads/Invoice.pdf');

        $message = "<h1>Invoice Pembelian</h1><p>Kepada " . $pembeli->username . ". Berikut invoice atas pembelian " . $barang->nama . ".";

        $this->sendEmail($attachment, 'alfispranata@gmail.com', 'Invoice', $message);

        return redirect()->to(site_url('transaksi/index'));
    }

    private function sendEmail($attachment, $to, $title, $message)
    {
        $this->email->setFrom('alfispranata@gmail.com', 'alfis pranata');
        $this->email->setTo($to);

        $this->email->attach($attachment);

        $this->email->setSubject($title);
        $this->email->setMessage($message);

        if (!$this->email->send()) {
            return false;
        } else {
            return true;
        }
    }

}
