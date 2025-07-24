@extends('layouts.master')
@section('title', 'Ajukan Pinjaman')

@section('bread')
<div class="page-header">
    <h3 class="fw-bold mb-3">@yield('title')</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home">
            <a href="{{ route('home') }}">
                <i class="icon-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('pinjaman.index') }}">Pinjaman</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">@yield('title')</a>
        </li>
    </ul>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Ajukan Pinjaman</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('pinjaman.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    <div class="form-group mb-3">
                        <label>Nama Nasabah</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label>Nomor Anggota</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->nm_koperasi }}" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label>Total Simpanan</label>
                        <input type="text" class="form-control" value="Rp {{ number_format($total_simpanan, 0, ',', '.') }}" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label>Maksimal Pinjaman 5x Jumlah Simpanan</label>
                        <input type="text" class="form-control" id="maksimal_pinjaman_display" value="Rp {{ number_format($total_simpanan * 5, 0, ',', '.') }}" readonly>
                        <input type="hidden" id="maksimal_pinjaman" value="{{ $total_simpanan * 5 }}">
                    </div>

                    <div class="form-group mb-3">
                        <label>Lama Pinjaman</label>
                        <select name="lama_pinjaman" class="form-control" required>
                            <option value="">-- Pilih Lama Pinjaman --</option>
                            <option value="6 Bulan">6 Bulan</option>
                            <option value="12 Bulan">12 Bulan</option>
                            <option value="18 Bulan">18 Bulan</option>
                            <option value="24 Bulan">24 Bulan</option>
                            <option value="30 Bulan">30 Bulan</option>
                            <option value="36 Bulan">36 Bulan</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label>Minimal Pinjaman</label>
                        <input type="text" id="jumlah_pinjaman_display" class="form-control" required>
                        <input type="hidden" name="jumlah_pinjaman" id="jumlah_pinjaman" value="{{ $jumlahMinimal }}" min="{{ $jumlahMinimal }}">

                        <small id="error-message" class="text-danger" style="display: none;">
                            Jumlah pinjaman tidak boleh melebihi batas maksimal.
                        </small>
                    </div>


                    <div class="form-group mb-3">
                        <label>Nama Penjamin</label>
                        <input type="text" name="nama_penjamin" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Foto KTP Penjamin</label>
                        <input type="file" name="foto" class="form-control" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('pinjaman.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-success">Ajukan Pinjaman</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    @if(session('success'))
    Swal.fire({
        icon: 'success'
        , title: 'Berhasil'
        , text: '{{ session('
        success ') }}'
    , });

    @elseif(session('error'))
    Swal.fire({
        icon: 'error'
        , title: 'Gagal'
        , text: '{{ session('error') }}'
    , });
    @endif
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js "></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        const formatRupiah = function(angka) {
            return 'Rp. ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        };

        // Ambil nilai minimal dari hidden input, default ke 0 kalau kosong
        let rawInitial = $('#jumlah_pinjaman').val() || '0';

        // Hapus leading zero kalau ada
        rawInitial = rawInitial.replace(/^0+/, '');
        if (rawInitial === '') rawInitial = '0';

        // Set nilai display dengan format rupiah
        $('#jumlah_pinjaman_display').val(formatRupiah(rawInitial));

        // Saat form disubmit
        $('form').on('submit', function(e) {
            const maksimalPinjaman = parseFloat($('#maksimal_pinjaman').val());
            const jumlahPinjaman = parseFloat($('#jumlah_pinjaman').val());
            const errorEl = $('#error-message');

            if (isNaN(jumlahPinjaman)) {
                e.preventDefault();
                errorEl.text('Masukkan jumlah pinjaman yang valid.').show();
                return false;
            }

            if (jumlahPinjaman > maksimalPinjaman) {
                e.preventDefault();
                errorEl.text('Jumlah pinjaman melebihi batas maksimal.').show();
                return false;
            } else {
                errorEl.hide();
            }
        });

        $('#jumlah_pinjaman_display').on('input', function() {
            let raw = $(this).val().replace(/[^0-9]/g, '') || '0';

            // Hapus leading zero
            raw = raw.replace(/^0+/, '');
            if (raw === '') raw = '0';

            const formatted = formatRupiah(raw);
            const jumlahPinjaman = parseFloat(raw);
            const maksimalPinjaman = parseFloat($('#maksimal_pinjaman').val());
            const errorEl = $('#error-message');

            $(this).val(formatted);
            $('#jumlah_pinjaman').val(jumlahPinjaman);

            if (jumlahPinjaman > maksimalPinjaman) {
                errorEl.text('Jumlah pinjaman melebihi batas maksimal.').show();
                $(this).addClass('is-invalid');
            } else {
                errorEl.hide();
                $(this).removeClass('is-invalid');
            }
        });
    });
</script>
@endsection
