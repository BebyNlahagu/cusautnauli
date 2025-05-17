@extends('layouts.master')
@section('title', 'Data Angsuran')
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
            <a href="">Angsuran</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('angsuran.index') }}">@yield('title')</a>
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah">
                    <span class="btn-label"><i class="fa fa-plus"></i></span> Add
                </button>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal Pinjaman</th>
                                <th>Jumlah Angsuran</th>
                                <th>Terima Total Pinjaman</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @php
                            $groupedAngsuran = $angsuran->groupBy('nasabah_id');
                            $no = 1;
                            @endphp

                            @foreach ($groupedAngsuran as $nasabahId => $angsurans)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $angsurans->first()->nasabah->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($angsurans->first()->created_at)->translatedFormat('l, d F
                                    Y') }}
                                </td>
                                <td>{{ $angsurans->first()->pinjaman->lama_pinjaman }}</td>
                                <td>Rp {{ number_format($angsurans->first()->pinjaman->terima_total, 0, ',', '.') }}
                                </td>
                                <td>
                                    <div class="form-button-action">
                                        <button type="button" class="btn btn-link btn-info btn-lg" title="Detail Angsuran" data-bs-toggle="modal" data-bs-target="#DetailModal{{ $nasabahId }}"><i class="fa fa-eye"></i></button>
                                    </div>
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

@foreach ($groupedAngsuran as $nasabahId => $angsurans)
<!-- Modal Detail -->
<div class="modal fade" id="DetailModal{{ $nasabahId }}" tabindex="-1" aria-labelledby="DetailModalLabel{{ $nasabahId }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DetailModalLabel{{ $nasabahId }}">
                    Detail Angsuran: {{ $angsurans->first()->nasabah->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <div class="modal-body">
                @if ($angsurans->count() > 0)
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Bulan Ke</th>
                                        <th>Pokok</th>
                                        <th>Bunga</th>
                                        <th>Denda</th>
                                        <th>Total Angsuran</th>
                                        <th>Tanggal Jatuh Tempo</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($angsurans as $detail)
                                    <tr>
                                        <td>{{ $detail->bulan_ke }}</td>
                                        <td>Rp {{ number_format($detail->angsuran_pokok, 0, ',', '.') }}
                                        </td>
                                        <td>Rp {{ number_format($detail->bunga, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($detail->denda, 0, ',', '.') }}</td>

                                        <td>Rp {{ number_format($detail->total_angsuran, 0, ',', '.') }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($detail->tanggal_jatuh_tempo)->translatedFormat('l,
                                            d F Y') }}
                                        </td>
                                        <td>
                                            @if ($detail->status == 'Lunas')
                                            <span class="badge text-bg-success">Lunas</span>
                                            @else
                                            <span class="badge text-bg-danger">Belum Lunas</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($detail->status != 'Lunas')
                                            @if (auth()->user()->role == 'Admin')
                                            <form id="ubahStatusForm{{ $detail->id }}" action="{{ route('angsuran.updateStatus', $detail->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="button" class="btn btn-warning btn-sm" title="Ubah Status" onclick="confirmUbahStatus({{ $detail->id }})">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @else
                                            <div class="alert alert-success p-3">
                                                <h5><strong>Angsuran Sudah Lunas!</strong></h5>
                                                <p><strong>Sisa Angsuran:</strong>
                                                    <span class="badge bg-info">{{ number_format($detail->sisa_angsuran,
                                                        0, ',', '.') }}</span>
                                                </p>

                                                @if ($detail->denda > 0)
                                                <p><strong>Denda:</strong>
                                                    <span class="badge bg-warning">{{ number_format($detail->denda, 0,
                                                        ',', '.') }}</span>
                                                </p>
                                                <p><strong>Total Angsuran Setelah Denda:</strong>
                                                    <span class="badge bg-success">{{
                                                        number_format($detail->total_angsuran, 0, ',', '.') }}</span>
                                                </p>
                                                @endif
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
                @else
                <p class="text-center">Belum ada data angsuran tersedia.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach


<div class="modal fade" id="tambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Data Angsuran</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('angsuran.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="pinjaman_id" id="pinjaman_id_hidden">
                    <input type="hidden" name="angsuran_perbulan" id="angsuran_bulan">
                    <input type="hidden" name="jumlah_total_pinjaman" id="jumlah_total_pinjaman_hidden">
                    <input type="hidden" name="tanggal_jatuh_tempo" id="jatuh_tempo_hidden">

                    <div class="form-floating form-floating-custom mb-3">
                        <select class="form-control" id="nasabah_id" name="nasabah_id" required>
                            <option value="">--Pilih Nama Nasabah--</option>
                            @foreach ($nasabah as $n)
                            <option value="{{ $n->id }}">{{ $n->name }}</option>
                            @endforeach
                        </select>
                        <label for="nasabah_id">Nama Nasabah</label>
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="text" id="jumlah_pinjaman" class="form-control" readonly>
                        <label for="jumlah_pinjaman">Jumlah Pinjaman</label>
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="text" id="lama_pinjaman" class="form-control" readonly>
                        <label for="lama_pinjaman">Lama Pinjaman (Tenor)</label>
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="text" id="bunga_pinjaman" class="form-control" readonly>
                        <label for="bunga_pinjaman">Bunga Pinjaman</label>
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="text" id="total_pinjaman" class="form-control" readonly>
                        <label for="total_pinjaman">Total Pinjaman</label>
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="text" id="proposi" class="form-control" readonly>
                        <label for="proposi">Biaya Adm 0.5%</label>
                    </div>
                    <div class="form-floating form-floating-custom mb-3">
                        <input type="text" id="terima_total" class="form-control" readonly>
                        <label for="terima_total">Bersih Terima</label>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@if (session('success'))
<script>
    Swal.fire({
        icon: 'success'
        , title: 'Berhasil!'
        , text: '{{ session('
        success ') }}'
        , showConfirmButton: false
        , timer: 2000
    });

</script>
@endif

@if (session('error'))
<script>
    Swal.fire({
        icon: 'error'
        , title: 'Gagal!'
        , text: '{{ session('
        error ') }}'
        , showConfirmButton: false
        , timer: 3000
    });

</script>
@endif


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmUbahStatus(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?'
            , text: "Angsuran Lunas!"
            , icon: 'warning'
            , showCancelButton: true
            , confirmButtonColor: '#28a745'
            , cancelButtonColor: '#d33'
            , confirmButtonText: 'Ya, Lunas!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('ubahStatusForm' + id).submit();
            }
        })
    }
    $(document).ready(function() {
        $(document).ready(function() {
            $("#basic-datatables").DataTable({});
        });

        function formatCurrency(number) {
            return "Rp. " + parseFloat(number).toLocaleString('id-ID');
        }

        // Format angka di tampilan awal (kalau perlu)
        $('.angsuran-perbulan').each(function() {
            var originalValue = $(this).text();
            var formattedValue = formatCurrency(originalValue);
            $(this).text(formattedValue);
        });

        // Saat nasabah dipilih
        $('#nasabah_id').on('change', function() {
            debugger;
            var nasabahId = $(this).val();
            if (nasabahId) {
                $.ajax({
                    url: '/get-pinjaman/' + nasabahId
                    , type: 'GET'
                    , success: function(data) {

                        $('#jumlah_pinjaman').val(formatCurrency(data.jumlah_pinjaman));
                        $('#lama_pinjaman').val(data.lama_pinjaman);
                        $('#bunga_pinjaman').val(data.bunga_pinjaman + '%');
                        $('#total_pinjaman').val(formatCurrency(data.total_pinjaman_flat));
                        $('#angsuran_bulan').val(formatCurrency(data.angsuran_per_bulan_flat));
                        $('#kapitalisasi').val(formatCurrency(data.kapitalisasi));
                        $('#proposi').val(formatCurrency(data.proposi));
                        $('#terima_total').val(formatCurrency(data.terima_total));

                        // Set hidden input untuk dikirim ke server
                        $('#pinjaman_id_hidden').val(data.pinjaman_id);
                        $('#jumlah_total_pinjaman_hidden').val(data.total_pinjaman_flat);
                        $('#angsuran_perbulan_hidden').val(data.angsuran_per_bulan_flat);

                        // Hitung jatuh tempo
                        let jatuhTempo = new Date();
                        jatuhTempo.setMonth(jatuhTempo.getMonth() + 1);
                        let jatuhTempoFormatted = jatuhTempo.toISOString().split('T')[0];
                        $('#jatuh_tempo_hidden').val(jatuhTempoFormatted);

                        // Kalau ada data bunga menurun, tampilkan di tabel
                        if (data.bunga_menurun && data.bunga_menurun.length > 0) {
                            window.bungaMenurunData = data.bunga_menurun;
                            renderTabelBungaMenurun(window.bungaMenurunData);
                        } else {
                            window.bungaMenurunData = [];
                            $('#tabel-bunga-menurun tbody').html(
                                '<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>'
                            );
                        }
                    }
                    , error: function(xhr) {
                        alert('Pinjaman tidak ditemukan atau terjadi kesalahan.');
                    }
                });
            } else {
                // Reset input
                $('#jumlah_pinjaman').val('');
                $('#lama_pinjaman').val('');
                $('#bunga_pinjaman').val('');
                $('#total_pinjaman').val('');
                $('#angsuran_per_bulan').val('');

                $('#pinjaman_id_hidden').val('');
                $('#jumlah_total_pinjaman_hidden').val('');
                $('#angsuran_perbulan_hidden').val('');
                $('#jatuh_tempo_hidden').val('');

                window.bungaMenurunData = [];
                $('#tabel-bunga-menurun tbody').html('');
            }
        });

        // Fungsi render tabel bunga menurun
        function renderTabelBungaMenurun(data) {
            var html = '';
            data.forEach(function(item) {
                html += '<tr>' +
                    '<td>' + item.bulan_ke + '</td>' +
                    '<td>' + formatCurrency(item.sisa_pokok) + '</td>' +
                    '<td>' + formatCurrency(item.angsuran_pokok) + '</td>' +
                    '<td>' + formatCurrency(item.bunga_bulan_ini) + '</td>' +
                    '<td>' + formatCurrency(item.total_angsuran_bulan_ini) + '</td>' +
                    '</tr>';
            });
            $('#tabel-bunga-menurun tbody').html(html);
        }

    });

</script>
@endsection
