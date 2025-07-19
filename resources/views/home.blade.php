@extends('layouts.master')
@section('title',"Dashboard")
@section('bread')
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">@yield('title')</h3>
        <h6 class="op-7 mb-2">Halo Admin</h6>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Nasabah</p>
                            <h4 class="card-title">{{ $nasabah}}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Simpanan</p>
                            <h4 class="card-title" id="jumlahSimpanan">{{ $simpanan}}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Pinjaman</p>
                            <h4 class="card-title" id="jumlahPinjaman">{{ $pinjaman }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Angsuran</p>
                            <h4 class="card-title" id="jumlahAngsuran">{{ $angsuran}}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Kapitalisasi</p>
                            <h4 class="card-title" id="kapitalisasi">{{ $kapitalisasi}}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-body">
                <h1 class="text-center">SELAMAT DATANG DI SISTEM INFORMASI SIMPAN PINJAM DI CU SAUT MAJU NAULI</h1>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function formatRupiah(angka) {
        var clean = angka.toString().replace(/[^0-9.]/g, '');
        var integerPart = clean.split('.')[0];
        var formatted = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        return 'Rp ' + formatted;
    }

    $(document).ready(function () {
        var elements = ['#jumlahSimpanan', '#jumlahPinjaman', '#jumlahAngsuran','#kapitalisasi'];

        elements.forEach(function (id) {
            var value = $(id).text();
            $(id).text(formatRupiah(value));
        });
    });
</script>
@endsection
