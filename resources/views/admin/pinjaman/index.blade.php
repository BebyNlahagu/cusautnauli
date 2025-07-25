@extends('layouts.master')
@section('title', 'Data Pinjaman')
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
            <a href="">Pinjaman</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('pinjaman.index') }}">@yield('title')</a>
        </li>
    </ul>
</div>
@endsection
@section('content')

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">@yield('title')</h4>
                @php
                $user = auth()->user();
                $umur = \Carbon\Carbon::parse($user->tanggal_lahir)->age;
                @endphp

                @if (auth()->user()->role == "User")
                <a href="{{ route('pinjaman.create') }}" class="btn btn-success">Ajukan Pinjaman</a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                @if (auth()->user()->role === "Admin")
                                <th>No. NIK</th>
                                <th>Nama</th>
                                @endif
                                <th>Jumlah Pinjaman</th>
                                <th>Tenor</th>
                                <th>Bunga</th>
                                @if (auth()->user()->role === "Admin")
                                <th style="width: 10%">Action</th>
                                @else
                                <th>Status</th>
                                @endif
                                
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $no = 1;
                            @endphp

                            @foreach ($pinjaman as $n)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ \Carbon\Carbon::parse($n->created_at)->translatedFormat('l, d F Y') }}</td>
                                @if (auth()->user()->role === "Admin")
                                <td>{{ $n->user->Nik }}</td>
                                <td>{{ $n->user->name }}</td>
                                @endif
                                <td>Rp {{ number_format((float) $n->jumlah_pinjaman, 0, ',', '.') }}</td>
                                <td>{{ $n->lama_pinjaman }}</td>
                                <td>{{ $n->bunga_pinjaman }} %</td>
                                @if (auth()->user()->role === "User" )
                                <td><span class="badge
                                                    @if ($n->status === 'Disetujui') bg-success
                                                    @elseif ($n->status === 'Ditolak') bg-danger
                                                    @else bg-secondary
                                                    @endif">
                                        @if ($n->status === 'Disetujui')
                                        <i class="fa fa-check me-1"></i> Disetujui
                                        @elseif ($n->status === 'Ditolak')
                                        <i class="fa fa-times me-1"></i> Ditolak
                                        @else
                                        Belum Diproses
                                        @endif
                                    </span></td>
                                @endif
                                <td>
                                    @if (auth()->user()->role === "Admin")
                                    <div class="form-button-action">
                                        <!-- Tombol Edit -->
                                        <a href="{{ route('pinjaman.edit', $n->id) }}" data-bs-toggle="modal" data-bs-target="#Edit{{ $n->id }}" class="btn btn-link btn-primary btn-lg" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        @if ($n->status === 'Pending')
                                        <!-- Tombol Approve -->
                                        <form action="{{ route('pengajuan.status', $n->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="Disetujui">
                                            <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        </form>

                                        <!-- Tombol Reject -->
                                        <form action="{{ route('pengajuan.status', $n->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="Ditolak">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Tolak">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                        @else
                                        <!-- Tampilkan Badge Status -->
                                        <span class="badge
                                                    @if ($n->status === 'Disetujui') bg-success
                                                    @elseif ($n->status === 'Ditolak') bg-danger
                                                    @else bg-secondary
                                                    @endif">
                                            @if ($n->status === 'Disetujui')
                                            <i class="fa fa-check me-1"></i> Disetujui
                                            @elseif ($n->status === 'Ditolak')
                                            <i class="fa fa-times me-1"></i> Ditolak
                                            @else
                                            Belum Diproses
                                            @endif
                                        </span>

                                        @endif

                                        <!-- Tombol Delete -->
                                        <form id="delete-form-{{ $n->id }}" action="{{ route('pinjaman.destroy', $n->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-link btn-danger" title="Hapus" onclick="confirmDelete({{ $n->id }})">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Data Pinjaman</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pinjaman.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- NIK Nasabah -->
                    @php
                    $user = auth()->user();
                    $isAdmin = $user->role === 'Admin';
                    @endphp

                    <div class="form-floating form-floating-custom mb-3">
                        <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                            <option value="">Pilih Nomor Anggota</option>

                            @if (isset($nasabah) && $nasabah->isNotEmpty())
                            @foreach ($nasabah->where('status','Verify') as $n)
                            @if ($isAdmin)
                            <option value="{{ $n->id }}" data-nik="{{ $n->nm_koperasi ?? '' }}" data-nama="{{ $n->name ?? '' }}">{{ $n->nm_koperasi }}</option>
                            @else
                            @if ($n->id == $user->id)
                            <option value="{{ $n->id }}" data-nik="{{ $n->nm_koperasi ?? '' }}" data-nama="{{ $n->name ?? '' }}">{{ $n->nm_koperasi }}</option>
                            @endif
                            @endif
                            @endforeach
                            @else
                            <option disabled>Tidak ada Data</option>
                            @endif
                        </select>
                        <label for="user_id">Pilih Nomor Anggota</label>
                    </div>


                    <!-- Nama Nasabah -->
                    <div class="form-floating form-floating-custom mb-3">
                        <input type="text" class="form-control" id="nama_nasabah" name="nama_nasabah" placeholder="Nama Nasabah" readonly />
                        <label for="nama_nasabah">Nama Nasabah</label>
                    </div>

                    <!-- Lama Pinjaman -->
                    <div class="form-floating form-floating-custom mb-3">
                        <select name="lama_pinjaman" id="lama_pinjaman" class="form-control form-select @error('lama_pinjaman') is-invalid @enderror">
                            <option value="">--pilih--</option>
                            <option value="5 Bulan">5 Bulan</option>
                            <option value="10 Bulan">10 Bulan</option>
                            <option value="15 Bulan">15 Bulan</option>
                            <option value="20 Bulan">20 Bulan</option>
                            <option value="25 Bulan">25 Bulan</option>
                            <option value="30 Bulan">30 Bulan</option>
                        </select>
                        <label for="lama_pinjaman">Lama Pinjaman</label>
                    </div>

                    <!-- Jumlah Pinjaman -->
                    <div class="form-floating form-floating-custom mb-1">
                        <input type="text" id="jumlah_pinjaman_display" class="form-control" placeholder="Jumlah Pinjaman" />
                        <label for="jumlah_pinjaman_display">Jumlah Pinjaman</label>
                    </div>
                    <input type="hidden" id="jumlah_pinjaman" name="jumlah_pinjaman" />
                    <small class="text-danger" id="maxInfo" style="display: none;"></small>
                    <div class="form-floating form-floating-custom mb-3">
                        <input type="number" name="bunga_pinjaman" class="form-control" id="bunga_pinjaman" placeholder="Bunga Pinjaman" readonly />
                        <label for="floatingInput">Bunga Pinjaman</label>
                    </div>

                    <h2>Penjamin</h2>
                    <div class="form-floating form-floating-custom mb-3">
                        <input type="char" name="nama_penjamin" class="form-control" id="nama_penjamin" placeholder="Nama penjamin" readonly />
                        <label for="floatingInput">Nama Penjamin</label>
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="file" class="form-control" id="foto" name="foto" placeholder="Foto KTP" readonly />
                        <label for="foto">Foto KTP Penjamin</label>
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" name="kapitalisasi" id="jumlah_kapitalisasi">
                    <input type="hidden" name="proposi" id="jumlah_adm">
                    <input type="hidden" name="terima_total" id="jumlah_terima">
                    <div id="maxInfo" class="text-success mt-2" style="display: none;"></div>
                    <div id="infoTambahan" class="text-info" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Nasabah Belum Eligible -->
<div class="modal fade" id="nasabahBergabungModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Konfirmasi Nasabah</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h1> Nasabah belum bergabung lebih dari 6 bulan. Anda tidak bisa melanjutkan transaksi pinjaman.</h1>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@if (session('success'))
<script>
    Swal.fire({
        title: "Berhasil!"
        , text: "{{ session('success') }}"
        , icon: "success"
        , confirmButtonText: "OK"
    });

</script>
@endif

@if (session('delete'))
<script>
    Swal.fire({
        title: "Dihapus!"
        , text: "{{ session('delete') }}"
        , icon: "warning"
        , confirmButtonText: "OK"
    });

</script>
@endif

@if (session('error'))
<script>
    Swal.fire({
        icon: 'error'
        , title: 'Gagal'
        , text: "{{ session('error') }}"
        , timer: 3000
        , showConfirmButton: false
    });

</script>
@endif

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "Apakah Anda Yakin?"
            , text: "Data yang dihapus tidak bisa dikembalikan!"
            , icon: "warning"
            , showCancelButton: true
            , confirmButtonColor: "#d33"
            , cancelButtonColor: "#3085d6"
            , confirmButtonText: "Ya, Hapus!"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('/assets/js/plugin/datatables/datatables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $("#basic-datatables").DataTable({});

        let maxLoan = 0;

        // Format angka jadi Rupiah
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency'
                , currency: 'IDR'
                , minimumFractionDigits: 0
            , }).format(angka);
        }

        function parseRupiah(rupiahStr) {
            return parseInt(rupiahStr.replace(/[Rp. ]/g, '')) || 0;
        }

        $('#jumlah_pinjaman_display').on('input', function() {
            const inputStr = $(this).val();
            let value = parseRupiah(inputStr);

            if (maxLoan && value > maxLoan) {
                $('#maxInfo').text('Jumlah pinjaman tidak boleh melebihi ' + formatRupiah(maxLoan)).show();
                value = maxLoan;
            } else {
                $('#maxInfo').hide();
            }

            $('#jumlah_pinjaman').val(value);
            $(this).val(formatRupiah(value));

            const kapitalisasi = value * 0.02;
            const proposi = value * 0.005;
            const total_terima = value - proposi;

            $('#jumlah_kapitalisasi').val(kapitalisasi);
            $('#jumlah_adm').val(proposi);
            $('#jumlah_terima').val(total_terima);
        });

        // Saat nasabah dipilih
        $('#user_id').on('change', function() {
            var user_id = $(this).val();

            if (user_id) {
                $.ajax({
                    url: '/pinjaman/check-eligibility/' + user_id
                    , type: 'GET'
                    , success: function(response) {
                        if (response.status === 'not_eligible') {
                            // Tambahan: jika respons ada alasan
                            let alasan = response.message ? ? 'Nasabah tidak memenuhi syarat.';

                            // Tampilkan modal
                            $('#nasabahBergabungModal').modal('show');

                            // Optional: update isi modal jika perlu
                            $('#nasabahBergabungModal .modal-body').html(`<p>${alasan}</p>`);

                            // Reset semua inputan
                            $('#nama_nasabah').val('');
                            $('#jumlah_pinjaman_display').val('');
                            $('#jumlah_pinjaman').val('');
                            $('#bunga_pinjaman').val('');
                            $('#jumlah_kapitalisasi').val('');
                            $('#jumlah_adm').val('');
                            $('#jumlah_terima').val('');
                            $('#maxInfo').hide();
                            $('#infoTambahan').hide();
                        } else if (response.status === 'eligible') {
                            $('#nama_nasabah').val(response.nama_nasabah);
                            maxLoan = response.jumlah_pinjaman;

                            // Reset input nilai
                            $('#jumlah_pinjaman_display').val('');
                            $('#jumlah_pinjaman').val('');
                            $('#jumlah_kapitalisasi').val('');
                            $('#jumlah_adm').val('');
                            $('#jumlah_terima').val('');

                            // Set bunga default
                            $('#bunga_pinjaman').val(response.bunga_pinjaman);

                            // Tampilkan maksimal pinjaman
                            $('#maxInfo').text('Maksimal pinjaman: ' + formatRupiah(maxLoan)).show();
                            let info = '';
                            if (response.umur && response.lama_gabung_bulan && response.angsuran !== undefined) {
                                info = `Umur nasabah: ${response.umur} tahun<br>Lama bergabung: ${response.lama_gabung_bulan} bulan`;
                            }

                            $('#infoTambahan').html(info).show();
                        }
                    }
                    , error: function(xhr) {
                        alert('Terjadi kesalahan: ' + (xhr.responseJSON ? .error ? ? 'Unknown Error'));
                    }
                });
            }
        });
    });

</script>
@endsection
