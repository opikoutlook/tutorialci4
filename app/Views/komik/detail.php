<?= $this->extend('layout/template'); ?>
<?= $this->section('content');?>
<div class="container">
<h1 class="mt-2 mb-2">Detail Komik</h1>
<h2><?= $komik['judul']?></h2> 
    <div class="row">
        
        <div class="col-2">
           <img src="/images/<?= $komik['sampul']?>" alt="" width="150px"><br>
        </div>
        <div class="col-8">
               <h3>Keterangan</h3> <br>
               <b>Penulis : </b><?= $komik['penulis'];?> <br>
               <b>Penerbit :</b><?= $komik['penerbit']?> <br>
               <!-- edit dengan mengirimkan slug sebagai identifier -->
                <a href="/komik/edit/<?= $komik['slug'];?>" class="btn btn-warning mt-3">Edit</a>

                <!-- http method spoofing -->
              
                <form action="/komik/<?= $komik['id'];?>" method="POST" class="d-inline">
                <?= csrf_field();?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger mt-3" onclick="return confirm('Apakah Anda yakin ingin menghapus ini?');">Delete</button>
                </form><br> 
                <!-- akhir dari http method spoofing -->
                <a href="/komik" class="btn btn-primary mt-3">Kembali ke Daftar Komik</a>
           </div>
    </div>
</div>


<?= $this->endSection();?>