<?php

namespace App\Controllers;

class Etalase extends BaseController
{
    private $url = "https://api.rajaongkir.com/starter/";
    private $apiKey = "988c391da110942cdfb1bb839327281b";
    protected $requestS;

    public function __construct()
    {
        helper('form');
        $this->validation = \Config\Services::validation();
        $this->session = session();
        $this->requestS = service("request");
    }

    public function index()
    {
        $barang = new \App\Models\BarangModel();
        $model = $barang->findAll();
        return view('etalase/index', [
            'model' => $model,
        ]);
    }

    public function beli()
    {
        $id = $this->requestS->uri->getSegment(3);
        $modelBarang = new \App\Models\BarangModel();
        $model = $modelBarang->find($id);

        $provinsi = $this->rajaOngkir('province');
        if ($this->requestS->getPost()) {
            $data = $this->requestS->getPost();
            $this->validation->run($data, 'transaksi');
            $errors = $this->validation->getErrors();

            if (!$errors) {
                $transaksiModel = new \App\Models\TransaksiModel();
                $transaksi = new \App\Entities\Transaksi();

                $barangModel = new \App\Models\BarangModel();
                $id_barang = $this->requestS->getPost('id_barang');
                $jumlah_pembelian = $this->requestS->getPost('jumlah');
                $barang = $barangModel->find($id_barang);
                $entityBarang = new \App\Entities\Barang();
                $entityBarang->stok = $barang->stok - $jumlah_pembelian;
                $barangModel->save($entityBarang);

                $transaksi->fill($data);
                $transaksi->status = 0;
                $transaksi->created_by = $this->session->get('id');
                $transaksi->created_date = date("Y-m-d H:i:s");

                $transaksiModel->save($transaksi);

                $id = $transaksiModel->insertID();
                $segment = ['transaksi', 'view', $id];

                return redirect()->to(site_url($segment));
            }
        }

        return view('etalase/beli.php', [
            'model' => $model,
            'provinsi' => json_decode($provinsi)->rajaongkir->results,
        ]);
    }

    public function getCity()
    {
        if ($this->requestS->isAjax()) {
            $id_province = $this->requestS->getGET('id_province');
            $data = $this->rajaOngkir('city', $id_province);
            return $this->response->setJSON($data); //biar javascript lebih mudah bacanya
        }
    }

    public function getCost()
    {

        if ($this->requestS->isAjax()) {
            $origin = $this->requestS->getGet('origin');
            $destination = $this->requestS->getGet('destination');
            $weight = $this->requestS->getGet('weight');
            $courier = $this->requestS->getGet('courier');
            $data = $this->rajaOngkirCost($origin, $destination, $weight, $courier);

            return $this->response->setJSON($data);

        }
    }

    private function rajaOngkirCost($origin, $destination, $weight, $courier)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.rajaongkir.com/starter/cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "origin=" . $origin . "&destination=" . $destination . "&weight=" . $weight . "&courier=" . $courier,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: " . $this->apiKey,
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return $response;
    }

    private function rajaOngkir($method, $id_provinsi = null)
    {
        $endPoint = $this->url . $method;

        if ($id_provinsi != null) {
            $endPoint = $endPoint . "?province=" . $id_provinsi;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $endPoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: " . $this->apiKey,
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return $response;
    }

}
