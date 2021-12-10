<?php

namespace App\Controllers;
use App\Models\KomikModel;
use CodeIgniter\HTTP\Request;

class Komik extends BaseController
{
    protected $komikModel; 
    /* agar bisa dipakai di fungsi turunan class Komik yang lain
     construct digunakan agar selalu di load saat class Komik dipanggil*/
    public function __construct()
    {
       $this->komikModel = new KomikModel();
    }

    public function index()
    {
        // ini untuk memanggil public construct yang sudah dideklarasi sebelumnya
        // $komik = $this->komikModel->findAll(); diganti dgn baris dibawahnya karena perubahan di Models>KomikModel penambahan method getKomik()
        $data['komik'] = $this->komikModel->getKomik();
        $data['title']= "Data Komik";
        //koneksi ke model yang dipakai
        // $komikModel = new \App\Models\KomikModel(); ---> cara 1
        // $komikModel = new KomikModel(); ---> cara 2 ini harus pakai use di atas, dan harus dipanggil berkali-kali pada method yang lain jika ingin dipakai
      
       


        echo view('komik/index', $data);
    }

    public function detail($slug)
    {
        // $komik=$this->komikModel->where(['slug' => $slug])->first();
        $data['komik'] = $this->komikModel->getKomik($slug);
        $data['title'] = "Detail Komik";

        //memeriksa apakah data yang dikirimkan kosong atau tidak
        // jika tidak ada, maka akan menampilkan error 404 
        if(empty($data['komik'])){
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul komik '.$slug. ' tidak ditemukan');
            
        }
        return view('komik/detail', $data);
    }

    public function create()
    {
      
        /* hanya dipakai untuk membuat halaman create form saja */
        /* untuk data form yg sudah diisi disimpan dari session withInput yg dikiri ke sini */
        $data['judul']= old('judul');
        $data['penulis'] = old('penulis');
        $data['penerbit'] = old('penerbit');
        $data['sampul'] = old('sampul');
        /* akhir dari data form lama yang blm berhasil input */
        $data['title'] = "Form Tambah Data Komik";

        $data['validation'] = \Config\Services::validation();
        return view('komik/create', $data);
    }

    public function save()
    {
        if(!$this->validate([
            'judul' => [
                'rules'=> 'required|is_unique[komik.judul]',
                'errors'=> [
                            'required' =>'{field} komik harus diisi',
                            'is_unique' => '{field} komik sudah terdaftar'
                           ]
                ],
            'sampul' =>[
                'rules'=> 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/png,image/jpg,image/jpeg]',
                'errors'=>[
                            'max_size' =>'Ukuran gambar terlalu besar',
                            'is_image' =>'Yang Anda masukkan bukan gambar',
                            'mime_in' =>'Yang Anda masukkan bukan gambar'
                ]
            ]
            
        ])){
            //ketika tidak tervalidasi
            // return redirect()->to('/komik/create')->withInput()->with('validation', $validation);
             return redirect()->to('/komik/create')->withInput();
            /* redirect sambil mengirimkan input yang ditulis dalam session dan hail validasi dalam $validation ke /komik/create.. lihat Komik::create */
        }
        //1. ambil gambarnya
        $fileSampul = $this->request->getFile('sampul');
        //2. ambil nama sampul untuk bagian save di baris .....
        // $namaSampul = $fileSampul->getName();
        //untuk user tidak memasukkan gambar, kita cek dulu
        if($fileSampul->getError()== 4){
            $namaSampul = 'default.png';
        }else{

            $namaSampul = $fileSampul->getRandomName();
            //3. pindahkan ke folder images
            // $fileSampul->move('public\images'); //ini ga bisa
            $fileSampul->move("images",$namaSampul); 
            // $fileSampul->move(ROOTPATH."public/images"); // ini bisa dipakai
        }
  
        $slug = url_title($this->request->getVar('judul'),'-', true);
        // $this->request->getVar(); **> untuk menerima kiriman dari form
        /* -------
        menyimpan nilai2 $this->komikModel->save(['key'=>'value']) jgn lupa daftarkan field2 yg akan diubah di dalam Models>KomikModel.php 
        --------------------------*/
        $this->komikModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,        
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        //membuat flashdata
        /* session()->setFlashdata('judulflashdata','Pesan yang dimunculkan') */
        session()->setFlashdata('pesan','Data berhasil ditambahkan');

        //melakukan redirect atau pengalihan ke halaman /komik
        return redirect()->to('/komik');

    }

    public function delete($id)
    {
        $komik = $this->komikModel->find($id);
        //periksa apakah gambarnya == default.png
        if($komik['sampul'] != 'default.png'){
            //hapus data file gambar
            unlink("images/".$komik['sampul']);
        }

        $this->komikModel->delete($id);//hapus data dalam Model sedangkan file gambar masih ada
        session()->setFlashdata('pesan','Data berhasil dihapus');

        return redirect()->to('/komik');

    }

    public function edit($slug)
    {
        $data['title'] = "Form Ubah Data Komik";
        $data['komik']= $this->komikModel->getKomik($slug);
        $data['validation'] = \Config\Services::validation();

        return view('komik/edit', $data);

    }

    public function update($id)
    {
        //cek judulnya 
        $komiklama = $this->komikModel->getKomik($this->request->getVar('slug'));
        if($komiklama['judul'] == $this->request->getVar('judul')){
            $rules_judul = 'required';
        }else{
            $rules_judul = 'required|is_unique[komik.judul]';
        }
        if(!$this->validate([
            'judul' => [
                'rules'=> $rules_judul,
                'errors'=> [
                            'required' =>'{field} komik harus diisi',
                            'is_unique' => '{field} komik sudah terdaftar'
                           ]
                ],
                'sampul' =>[
                    'rules'=> 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/png,image/jpg,image/jpeg]',
                    'errors'=>[
                                'max_size' =>'Ukuran gambar terlalu besar',
                                'is_image' =>'Yang Anda masukkan bukan gambar',
                                'mime_in' =>'Yang Anda masukkan bukan gambar'
                    ]
                ]
        ])){
            // $validation = \Config\Services::validation();
            return redirect()->to('/komik/edit/'.$this->request->getVar('slug'))->withInput();
        }
        $fileSampul = $this->request->getFile('sampul');
        //cek gambar apakah ttap gambar lama
        if($fileSampul->getError() == 4){
            $namaSampul = $this->request->getVar('sampulLama');
        }else{
            //generate nama random
            $namaSampul = $fileSampul->getRandomName();
            //pindahkan gambar ke public/images
            $fileSampul->move("images", $namaSampul);
            //hapus file lama
            if ($komiklama['sampul'] != 'default.png') {
                unlink("images/".$this->request->getVar('sampulLama'));
            }
        }

        // dd($this->request->getVar()); untuk melihat variabel yg dikirimkan
        $this->komikModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => $this->request->getVar('slug'),
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        //membuat flashdata
        /* session()->setFlashdata('judulflashdata','Pesan yang dimunculkan') */
        session()->setFlashdata('pesan','Data berhasil diubah');

        //melakukan redirect atau pengalihan ke halaman /komik
        return redirect()->to('/komik');
    }
    //------------
}