@extends('layouts.master')
@section('title', 'Laporan Angsuran')
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
            <a href="{{ route('laporan.angsuran') }}">@yield('title')</a>
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
                        <form action="{{ route('laporan.angsuran') }}" method="GET">
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
                                <a href="{{ route('laporan.angsuran') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>


                <a class="btn btn-label-info btn-round btn-sm" href="{{ route('pdf.angsuran') }}">
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
                                <th>Nomor Anggota</th>
                                <th>Nama</th>
                                <th>Tanggal Angguran</th>
                                <th>Jumlah Angsuran</th>
                                <th>Total Pinjaman</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @php
                            $groupedAngsuran = $angsuran->groupBy('user_id');
                            $no = 1;
                            @endphp

                            @foreach ($groupedAngsuran as $nasabahId => $ang)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $ang->first()->user->nm_koperasi }}</td>
                                <td>{{ $ang->first()->user->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($ang->first()->created_at)->translatedFormat('l, d F Y') }}
                                </td>
                                <td>{{ $ang->first()->pinjaman->lama_pinjaman }}</td>
                                <td>Rp {{ number_format($ang->first()->pinjaman->terima_total, 0, ',', '.') }}</td>
                               <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#lihatSemuaAngsuran{{ $nasabahId }}">
                                        <i class="fa fa-eye"></i> Lihat Semua
                                    </button>
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

@foreach ($groupedAngsuran as $nasabahId => $ang)
<div class="modal fade" id="lihatSemuaAngsuran{{ $nasabahId }}" tabindex="-1" aria-labelledby="lihatSemuaAngsuranLabel{{ $nasabahId }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Angsuran - {{ $ang->first()->user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nomor Anggota</th>
                            <th>Nama</th>
                            <th>Tanggal Angsuran</th>
                            <th>Lama Angsuran</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach ($groupedAngsuran as $nasabahId => $ang)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $ang->first()->user->nm_koperasi }}</td>
                                <td>{{ $ang->first()->user->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($ang->first()->created_at)->translatedFormat('l, d F Y') }}</td>
                                <td>{{ $ang->first()->pinjaman->lama_pinjaman }}</td>
                                <td>Rp {{ number_format($ang->first()->pinjaman->terima_total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script src="{{ asset('/assets/js/plugin/datatables/datatables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $("#basic-datatables").DataTable({});
    });

</script>
@endsection
