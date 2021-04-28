<?php

namespace App\Controllers;

class Barang extends BaseController
{
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
        $barangModel = new \App\Models\BarangModel();
        $barangs = $barangModel->findAll();
        return view('barang/index', [
            'barangs' => $barangs,
        ]);
    }

    //detail
    public function view()
    {
        $id = $this->requestS->uri->getSegment(3); // 3 didapatkan dari - barang/view/id
        $barangModel = new \App\Models\BarangModel();

        $barang = $barangModel->find($id);

        return view('barang/view.php', [
            'barang' => $barang,
        ]);
    }

    public function create()
    {
        if ($this->requestS->getPost()) {
            $data = $this->requestS->getPost();
            $this->validation->run($data, 'barang');
            $errors = $this->validation->getErrors();

            if (!$errors) {
                $barangModel = new \App\Models\BarangModel();
                $barang = new \App\Entities\Barang();

                $barang->fill($data);
                // $barang->gambar = langsung memanggil di entities
                $barang->gambar = $this->requestS->getFile('gambar');
                $barang->created_by = $this->session->get('id');
                $barang->created_date = date("Y-m-d H:i:s");

                $barangModel->save($barang);

                $id = $barangModel->insertID(); //mengambil id dari barangModel

                $segments = ['barang', 'view', $id];
                // ke barang/view/id
                return redirect()->to(site_url($segments));

            }
        }

        return view('barang/create');
    }

    public function update()
    {
        $id = $this->requestS->uri->getSegment(3);
        $barangModel = new \App\Models\BarangModel();
        $barang = $barangModel->find($id);

        if ($this->requestS->getPost()) {
            $data = $this->requestS->getPost();
            $this->validation->run($data, 'barangUpdate');
            $errros = $this->validation->getErrors();

            if (!$errros) {
                $b = new \App\Entities\Barang();
                $b->id = $id;
                $b->fill($data);

                if ($this->requestS->getFile('gambar')->isValid()) {
                    $b->gambar = $this->requestS->getFile('gambar');
                }

                $b->updated_by = $this->session->get('id');
                $b->updated_date = date("Y-m-d H:i:s");

                $barangModel->save($b);
                $segments = ['barang', 'view', $id];

                return redirect()->to(site_url($segments));
            }
        }

        return view('barang/update', [
            'barang' => $barang,
        ]);
    }

    public function delete()
    {
        $id = $this->requestS->uri->getSegment(3);
        $barangModel = new \App\Models\BarangModel();

        $delete = $barangModel->delete($id);

        return redirect()->to(site_url('barang/index'));
    }
}
