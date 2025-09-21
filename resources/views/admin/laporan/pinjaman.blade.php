@extends('layouts.master')
@section('title', 'Laporan Pinjaman')
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
            <a href="{{ route('laporan.pinjaman') }}">@yield('title')</a>
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
                            <form action="{{ route('laporan.pinjaman') }}" method="GET">
                                <!-- Bulan Filter -->
                                <div class="mb-3">
                                    <label for="bulan" class="form-label">Pilih Bulan</label>
                                    <select name="bulan" id="bulan" class="form-control">
                                        <option value="">-- Semua Bulan --</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
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
                                    <a href="{{ route('laporan.pinjaman') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>


                    <a class="btn btn-label-info btn-round btn-sm"" href="{{ route('pdf.pinjaman') }}">
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
                                <th>Tanggal</th>
                                <th>Nomor Anggota</th>
                                <th>Nama</th>
                                <th>Jumlah Pinjaman</th>
                                <th>Tenor</th>
                                <th>Bunga</th>
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
                                <td>{{ $n->user->nm_koperasi }}</td>
                                <td>{{ $n->user->name }}</td>
                                <td>Rp {{ number_format((float)$n->jumlah_pinjaman, 0, ',', '.') }}</td>
                                <td>{{ $n->lama_pinjaman }}</td>
                                <td>{{ $n->bunga_pinjaman }} %</td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#lihatSemuaModal{{ $n->user_id }}">
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

@foreach ($semua_pinjaman->groupBy('user_id') as $user_id => $pinjaman_user)
<div class="modal fade" id="lihatSemuaModal{{ $user_id }}" tabindex="-1" aria-labelledby="lihatSemuaModalLabel{{ $user_id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Pinjaman - {{ $pinjaman_user->first()->user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Lama</th>
                            <th>Bunga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pinjaman_user as $i => $p)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('F Y') }}</td>
                                <td>Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}</td>
                                <td>{{ $p->lama_pinjaman }}</td>
                                <td>{{ $p->bunga_pinjaman }}%</td>
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


<script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $("#basic-datatables").DataTable({});
    });
</script>
@endsection
