@extends('layouts.master')
@section('title', 'Laporan Simpanan')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <a href="">Laporan</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('laporan.simpanan') }}">@yield('title')</a>
        </li>
    </ul>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center flex-wrap">
                <h4 class="card-title text-start">@yield('title')</h4>

                @if (auth()->user()->role == "Admin")
                <div class="btn-group ms-auto">
                    <button type="button" class="btn btn-label-info btn-round btn-sm me-2" data-bs-toggle="dropdown" aria-expanded="false" title="Filter">
                        <i class="fa fa-filter"></i>
                    </button>
                    <div class="dropdown-menu p-4" style="min-width: 300px;">
                        <form action="{{ route('laporan.simpanan') }}" method="GET">
                            <!-- Bulan Filter -->
                            <div class="mb-3">
                                <label for="bulan" class="form-label">Pilih Bulan</label>
                                <select name="bulan" id="bulan" class="form-control">
                                    <option value="">-- Semua Bulan --</option>
                                    @for ($i = 1; $i <= 12; $i++) <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                        {{ date("F", mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                        @endfor
                                </select>
                            </div>

                            <!-- Tahun Filter -->
                            <div class="mb-3">
                                <label for="tahun" class="form-label">Pilih Tahun</label>
                                <select name="tahun" id="tahun" class="form-control">
                                    <option value="">-- Semua Tahun --</option>
                                    @for ($i = now()->year; $i >= 2000; $i--)
                                    <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Hari Filter -->
                            <div class="mb-3">
                                <label for="hari" class="form-label">Pilih Hari</label>
                                <input type="date" name="hari" id="hari" value="{{ request('hari') }}" class="form-control">
                            </div>

                            <!-- Submit & Reset Button -->
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">Terapkan</button>
                                <a href="{{ route('laporan.simpanan') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>


                <a class="btn btn-label-info btn-round btn-sm" href="{{ route('pdf.simpanan') }}">
                    <i class="fa fa-download"></i>
                </a>
                @endif
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Nomor Anggota</th>
                                <th>Tanggal Simpanan</th>
                                <th>Jumlah Simpanan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $no = 1;
                            @endphp

                            @foreach ($groupedSimpanan as $item)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item['user']->nm_koperasi }}</td>
                                <td>{{ $item['user']->name }}</td>
                                <td>{{ $item['tanggal_terakhir'] }}</td>
                                <td class="text-end bold">Rp {{ number_format($item['total_simpanan'], 0, ',', '.') }}</td>
                                <td>
                                    <button type="button" class="btn btn-link btn-success view-simpanan-btn" title="Detail" data-user="{{ $item['user']->name }}" data-id="{{ $item['user']->id }}">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        @if (auth()->user()->role == "Admin")
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-center fw-bolder">Jumlah Total</th>
                                <th class="text-end fw-bold">Rp. {{ number_format($jumlah,0,',','.') }}</th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="simpananModal" tabindex="-1" aria-labelledby="simpananModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="simpananModalLabel">Daftar Simpanan <span id="userName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jenis Simpanan</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody id="simpananTableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('/assets/js/plugin/datatables/datatables.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const allSimpanan = @json($simpanan);
    $(document).ready(function() {
        $("#basic-datatables").DataTable({});
        $(document).on('click', '.view-simpanan-btn', function() {
            const userId = $(this).data('id');
            const userName = $(this).data('user');

            $('#userName').text(userName);
            $('#simpananTableBody').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');

            $.ajax({
                url: `/simpanan/user/${userId}`
                , method: 'GET'
                , success: function(data) {
                    let rows = '';
                    data.simpans.forEach((simpan, index) => {
                        rows += `<tr>
                            <td>${index + 1}</td>
                            <td>${simpan.tanggal}</td>
                            <td>${simpan.nama_simpanan}</td>
                            <td class="text-end">Rp ${simpan.besar_simpanan}</td>
                        </tr>`;
                    });

                    if (data.simpans.length === 0) {
                        rows = `<tr><td colspan="4" class="text-center">Tidak ada data simpanan</td></tr>`;
                    }

                    $('#simpananTableBody').html(rows);

                    const modal = new bootstrap.Modal(document.getElementById('simpananModal'));
                    modal.show();
                }
                , error: function() {
                    alert('Gagal mengambil data.');
                }
            });
        });
    });

</script>
@endsection
