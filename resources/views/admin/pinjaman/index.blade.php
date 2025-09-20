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
                    @if (auth()->user()->role == 'Admin')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah"><span
                                class="btn-label"><i class="fa fa-plus"></i></span>Add</button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="basic-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    @if (auth()->user()->role === 'Admin')
                                        <th>No. NIK</th>
                                        <th>Nama</th>
                                    @endif
                                    <th>Jumlah Pinjaman</th>
                                    <th>Tenor</th>
                                    <th>Bunga</th>
                                    @if (auth()->user()->role === 'Admin')
                                        <th style="width: 10%">Action</th>
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
                                        @if (auth()->user()->role === 'Admin')
                                            <td>{{ $n->nasabah->Nik }}</td>
                                            <td>{{ $n->nasabah->name }}</td>
                                        @endif
                                        <td>Rp {{ number_format((float) $n->jumlah_pinjaman, 0, ',', '.') }}</td>
                                        <td>{{ $n->lama_pinjaman }}</td>
                                        <td>{{ $n->bunga_pinjaman }} %</td>
                                        <td>
                                            @if (auth()->user()->role === 'Admin')
                                                <div class="form-button-action">
                                                    <form id="delete-form-{{ $n->id }}"
                                                        action="{{ route('pinjaman.destroy', $n->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" data-bs-toggle="tooltip"
                                                            class="btn btn-link btn-danger" data-original-title="Remove"
                                                            onclick="confirmDelete({{ $n->id }})"><i
                                                                class="fa fa-times"></i></button>
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

    <div class="modal fade" id="tambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                            $isAdmin = $user->role === 'Admin'; // sesuaikan sesuai sistem role kamu
                        @endphp

                        <div class="form-floating form-floating-custom mb-3">
                            <select class="form-control select2 @error('user_id') is-invalid @enderror" style="width: 100%;" id="user_id"
                                name="user_id">
                                <option value="">Pilih Nomor Anggota</option>

                                @if (isset($nasabah) && $nasabah->isNotEmpty())
                                    @foreach ($nasabah->whereNotNull('nm_koperasi') as $n)
                                        @if ($isAdmin)
                                            <option value="{{ $n->id }}" data-nik="{{ $n->nm_koperasi ?? '' }}"
                                                data-nama="{{ $n->name ?? '' }}">{{ $n->nm_koperasi }} -
                                                {{ $n->name }}</option>
                                        @else
                                            @if ($n->id == $user->id)
                                                <option value="{{ $n->id }}" data-nik="{{ $n->nm_koperasi ?? '' }}"
                                                    data-nama="{{ $n->name ?? '' }}">{{ $n->nm_koperasi }} -
                                                    {{ $n->name }}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                @else
                                    <option disabled>Tidak ada Data</option>
                                @endif
                            </select>
                        </div>


                        <!-- Nama Nasabah -->
                        <div class="form-floating form-floating-custom mb-3">
                            <input type="text" class="form-control" id="nama_nasabah" name="nama_nasabah"
                                placeholder="Nama Nasabah" readonly />
                            <label for="nama_nasabah">Nama Nasabah</label>
                        </div>

                        <!-- Lama Pinjaman -->
                        <div class="form-floating form-floating-custom mb-3">

                            <select name="lama_pinjaman" class="form-control form-select" required>
                                <option value="">-- Pilih Lama Pinjaman --</option>
                                <option value="6 Bulan">6 Bulan</option>
                                <option value="12 Bulan">12 Bulan</option>
                                <option value="18 Bulan">18 Bulan</option>
                                <option value="24 Bulan">24 Bulan</option>
                                <option value="30 Bulan">30 Bulan</option>
                                <option value="36 Bulan">36 Bulan</option>
                            </select>
                             <label>Lama Pinjaman</label>
                        </div>

                        <!-- Jumlah Pinjaman -->
                        <div class="form-floating form-floating-custom mb-1">
                            <input type="text" id="jumlah_pinjaman_display" class="form-control"
                                placeholder="Jumlah Pinjaman" />
                            <label for="jumlah_pinjaman_display">Jumlah Pinjaman</label>
                        </div>

                        <input type="hidden" id="jumlah_pinjaman" name="jumlah_pinjaman" />

                        <small class="text-danger" id="maxInfo" style="display: none;"></small>

                        <div class="form-floating form-floating-custom mb-3">
                            <input type="number" name="bunga_pinjaman" class="form-control" id="bunga_pinjaman"
                                placeholder="Bunga Pinjaman" readonly />
                            <label for="floatingInput">Bunga Pinjaman</label>
                        </div>

                        <!-- Penjamin -->
                        <h1>Penjamin Pinjaman</h1>
                        <div class="form-floating form-floating-custom mb-3">
                            <input type="text" class="form-control" id="nama_penjamin" name="nama_penjamin"
                                placeholder="Penjamin" />
                            <label for="nama_penjamin">Penjamin</label>
                        </div>

                        <div class="form-floating form-floating-custom mb-3">
                            <input type="file" class="form-control" id="foto" name="foto"
                                placeholder="Foto Penjamin" />
                            <label for="foto">KTP Penjamin</label>
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
    <div class="modal fade" id="nasabahBergabungModal" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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

    <!-- {{-- Modal Edit --}}
    @foreach ($pinjaman as $n)
    <div class="modal fade" id="Edit{{ $n->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Data Pinjaman</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('pinjaman.update', $n->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-floating form-floating-custom mb-3">
                            <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                                <option value="">Pilih</option>
                                @if (isset($nasabah) && $nasabah->isNotEmpty())
    @foreach ($nasabah->where('status', 'Verify') as $item)
    <option value="{{ $item->id }}" {{ $item->id == $n->user_id ? 'selected' : '' }} data-nik="{{ $item->Nik }}" data-nama="{{ $item->name }}">
                                    {{ $item->Nik }}
                                </option>
    @endforeach
@else
    <p>Tidak Ada Data</p>
    @endif
                            </select>
                            <label for="user_id">Pilih Nasabah</label>
                        </div>

                        <div class="form-floating form-floating-custom mb-3">
                            <input type="text" class="form-control" id="nama_nasabah" name="nama_nasabah" placeholder="Nama Nasabah" value="{{ $n->nasabah->name ?? '' }}" readonly />
                            <label for="nama_nasabah">Nama Nasabah</label>
                        </div>

                        <div class="form-floating form-floating-custom mb-3">
                            <select name="lama_pinjaman" id="lama_pinjaman" class="form-control form-select">
                                <option value="">--pilih--</option>
                                <option value="5 Bulan" {{ $n->lama_pinjaman == '6 Bulan' ? 'selected' : '' }}>6 Bulan
                                </option>
                                <option value="10 Bulan" {{ $n->lama_pinjaman == '12 Bulan' ? 'selected' : '' }}>12
                                    Bulan</option>
                                <option value="15 Bulan" {{ $n->lama_pinjaman == '18 Bulan' ? 'selected' : '' }}>18
                                    Bulan</option>
                                <option value="20 Bulan" {{ $n->lama_pinjaman == '24 Bulan' ? 'selected' : '' }}>24
                                    Bulan</option>
                                <option value="30 Bulan" {{ $n->lama_pinjaman == '30 Bulan' ? 'selected' : '' }}>30
                                    Bulan</option>
                            </select>
                            <label for="lama_pinjaman">Lama Pinjaman</label>
                        </div>


                        <div class="form-floating form-floating-custom mb-3">
                            <input type="text" id="jumlah_pinjaman_display_edit_{{ $n->id }}" oninput="formatUangEdit(this, {{ $n->id }})" class="form-control @error('jumlah_pinjaman') is-invalid @enderror" placeholder="Jumlah Simpanan" value="{{ 'Rp ' . number_format($n->jumlah_pinjaman, 0, ',', '.') }}" />

                            <input type="hidden" name="jumlah_pinjaman" class="form-control @error('jumlah_pinjaman') is-invalid @enderror" id="jumlah_pinjaman_edit_{{ $n->id }}" value="{{ $n->jumlah_pinjaman }}" />

                            <label for="jumlah_pinjaman">Jumlah Pinjaman</label>
                            @error('jumlah_pinjaman')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
                        </div>

                        <div class="form-floating form-floating-custom mb-3">
                            <input type="number" name="bunga_pinjaman" class="form-control" id="bunga_pinjaman" placeholder="Bunga Pinjaman" value="{{ old('bunga_pinjaman', $n->bunga_pinjaman) }}"  readonly />
                            <label for="bunga_pinjaman">Bunga Pinjaman</label>
                        </div>

                        <div class="form-floating form-floating-custom mb-3">
                            <input type="number" name="simpanan" class="form-control @error('simpanan') is-invalid @enderror" id="simpanan" placeholder="Simpanan" value="{{ old('simpanan', $n->simpanan) }}" />
                            <label for="simpanan">Simpanan</label>
                            @error('simpanan')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach -->

    @if (session('success'))
        <script>
            Swal.fire({
                title: "Berhasil!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "OK"
            });
        </script>
    @endif

    @if (session('delete'))
        <script>
            Swal.fire({
                title: "Dihapus!",
                text: "{{ session('delete') }}",
                icon: "warning",
                confirmButtonText: "OK"
            });
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: "Apakah Anda Yakin?",
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
        $(document).ready(function() {
            $("#basic-datatables").DataTable({});

            $('#user_id').select2({
                dropdownParent: $('#tambah')
            });

            let maxLoan = 0;

            // Format angka jadi Rupiah
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                }).format(angka);
            }

            function parseRupiah(rupiahStr) {
                return parseInt(rupiahStr.replace(/[Rp. ]/g, '')) || 0;
            }

            $('#jumlah_pinjaman_display').on('input', function() {
                const inputStr = $(this).val();
                let value = parseRupiah(inputStr);

                if (maxLoan && value > maxLoan) {
                    $('#maxInfo').text('Jumlah pinjaman tidak boleh melebihi ' + formatRupiah(maxLoan))
                        .show();
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
                var selectedOption = $(this).find('option:selected');
                var nama = selectedOption.data('nama') || '';

                // Isi langsung nama nasabah dari option
                $('#nama_nasabah').val(nama);

                var user_id = $(this).val();

                if (user_id) {
                    $.ajax({
                        url: '/pinjaman/check-eligibility/' + user_id,
                        type: 'GET',
                        success: function(response) {
                            if (response.status === 'not_eligible') {
                                let alasan = response.message ??
                                    'Nasabah tidak memenuhi syarat.';
                                $('#nasabahBergabungModal').modal('show');
                                $('#nasabahBergabungModal .modal-body').html(
                                `<p>${alasan}</p>`);

                                // Reset semua inputan kecuali nama
                                $('#jumlah_pinjaman_display').val('');
                                $('#jumlah_pinjaman').val('');
                                $('#bunga_pinjaman').val('');
                                $('#jumlah_kapitalisasi').val('');
                                $('#jumlah_adm').val('');
                                $('#jumlah_terima').val('');
                                $('#maxInfo').hide();
                                $('#infoTambahan').hide();
                            } else if (response.status === 'eligible') {
                                maxLoan = response.jumlah_pinjaman;

                                // Reset input nilai
                                $('#jumlah_pinjaman_display').val('');
                                $('#jumlah_pinjaman').val('');
                                $('#jumlah_kapitalisasi').val('');
                                $('#jumlah_adm').val('');
                                $('#jumlah_terima').val('');
                                $('#bunga_pinjaman').val(response.bunga_pinjaman);

                                // Tampilkan maksimal pinjaman
                                $('#maxInfo').text('Maksimal pinjaman: ' + formatRupiah(
                                    maxLoan)).show();

                                let info = '';
                                if (response.umur && response.lama_gabung_bulan && response
                                    .angsuran !== undefined) {
                                    info =
                                        `Umur nasabah: ${response.umur} tahun<br>Lama bergabung: ${response.lama_gabung_bulan} bulan`;
                                }
                                $('#infoTambahan').html(info).show();
                            }
                        },
                        error: function(xhr) {
                            alert('Terjadi kesalahan: ' + (xhr.responseJSON?.error ??
                                'Unknown Error'));
                        }
                    });
                } else {
                    $('#nama_nasabah').val('');
                }
            });

        });
    </script>
@endsection
