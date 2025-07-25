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
                        <input type="text" id="jumlah_pinjaman_display" class="form-control" required autocomplete="off">
                        <input type="hidden" name="jumlah_pinjaman" id="jumlah_pinjaman" value="{{ $jumlahMinimal }}" min="{{ $jumlahMinimal }}">
                        <small id="error-message" class="text-danger">
                            {{-- Jumlah pinjaman tidak kurang dari minimal. --}}
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
        , text: '{{ session('
        error ') }}'
    , });
    @endif

</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js "></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        const jumlahMinimal = parseFloat("{{ $jumlahMinimal }}");
        const maksimalPinjaman = parseFloat($('#maksimal_pinjaman').val());

        // Fungsi format angka jadi Rp. 10.000
        function formatRupiah(angka) {
            let number_string = angka.toString().replace(/[^,\d]/g, '')
                , split = number_string.split(',')
                , sisa = split[0].length % 3
                , rupiah = split[0].substr(0, sisa)
                , ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return 'Rp. ' + rupiah;
        }

        // Set default nilai awal ke minimal dengan format Rupiah
        $('#jumlah_pinjaman_display').val(formatRupiah(jumlahMinimal));
        $('#jumlah_pinjaman').val(jumlahMinimal);

        // Fungsi untuk dapatkan angka murni dari string input
        function getNumberOnly(string) {
            return parseInt(string.replace(/[^0-9]/g, '')) || 0;
        }

        // Event input, format dan update nilai hidden tanpa mengganggu posisi cursor
        $('#jumlah_pinjaman_display').on('input', function(e) {
            let input = $(this);
            let cursorPos = this.selectionStart;

            let originalLength = input.val().length;

            let angka = getNumberOnly(input.val());

            const errorEl = $("#error-message")

            if (angka < jumlahMinimal) {
                errorEl.text('Jumlah pinjaman tidak kurang dari minimal.').show();
            }else{
                errorEl.hide();
            }

            $('#jumlah_pinjaman').val(angka);

           
            let formatted = formatRupiah(angka);
            input.val(formatted);

            let newLength = formatted.length;
            cursorPos = cursorPos + (newLength - originalLength);
            this.setSelectionRange(cursorPos, cursorPos);
        });

        // Saat blur (keluar input), koreksi ke minimal kalau kurang dan format ulang
        $('#jumlah_pinjaman_display').on('blur', function() {
            let angka = getNumberOnly($(this).val());

            if (angka < jumlahMinimal) {
                angka = jumlahMinimal;
                $('#jumlah_pinjaman').val(angka);
                $(this).val(formatRupiah(angka));
            }
        });

        // Validasi form saat submit
        $('form').on('submit', function(e) {
            const jumlahPinjaman = parseFloat($('#jumlah_pinjaman').val());

            if (isNaN(jumlahPinjaman)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error'
                    , title: 'Gagal'
                    , text: 'Masukkan jumlah pinjaman yang valid.'
                });
                return false;
            }

            if (jumlahPinjaman < jumlahMinimal) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error'
                    , title: 'Gagal'
                    , text: 'Jumlah pinjaman tidak boleh kurang dari minimal (Rp ' + jumlahMinimal.toLocaleString('id-ID') + ').'
                });
                $('#jumlah_pinjaman_display').val(formatRupiah(jumlahMinimal));
                $('#jumlah_pinjaman').val(jumlahMinimal);
                return false;
            }

            if (jumlahPinjaman > maksimalPinjaman) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error'
                    , title: 'Gagal'
                    , text: 'Jumlah pinjaman melebihi batas maksimal.'
                });
                return false;
            }
        });
    });

</script>
@endsection
