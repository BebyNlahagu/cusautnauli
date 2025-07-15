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
                        <label>Maksimal Pinjaman</label>
                        <input type="text" class="form-control" id="maksimal_pinjaman_display" value="Rp {{ number_format($total_simpanan * 5, 0, ',', '.') }}" readonly>
                        <input type="hidden" id="maksimal_pinjaman" value="{{ $total_simpanan * 5 }}">
                    </div>

                    <div class="form-group mb-3">
                        <label>Lama Pinjaman</label>
                        <select name="lama_pinjaman" class="form-control" required>
                            <option value="">-- Pilih Lama Pinjaman --</option>
                            <option value="5 Bulan">5 Bulan</option>
                            <option value="10 Bulan">10 Bulan</option>
                            <option value="15 Bulan">15 Bulan</option>
                            <option value="20 Bulan">20 Bulan</option>
                            <option value="25 Bulan">25 Bulan</option>
                            <option value="30 Bulan">30 Bulan</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label>Jumlah Pinjaman</label>
                        <input type="number" name="jumlah_pinjaman" id="jumlah_pinjaman" class="form-control" min="10000" required>
                        <small id="error-message" class="text-danger" style="display: none;">Jumlah pinjaman tidak boleh melebihi batas maksimal.</small>
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
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
        });
    @elseif (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '{{ session('error') }}',
        });
    @endif
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js "></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
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
                errorEl.show();
            } else {
                errorEl.hide();
            }
        });

        // Tampilkan pesan error saat input diubah (opsional)
        $('#jumlah_pinjaman').on('input', function() {
            const maksimalPinjaman = parseFloat($('#maksimal_pinjaman').val());
            const jumlahPinjaman = parseFloat($(this).val());
            const errorEl = $('#error-message');

            if (jumlahPinjaman > maksimalPinjaman) {
                errorEl.show();
            } else {
                errorEl.hide();
            }
        });
    });

</script>
@endsection
