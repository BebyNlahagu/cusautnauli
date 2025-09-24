@extends('layouts.master')
@section('title', 'Laporan Anggota')
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
                        <form action="{{ route('laporan.anggota') }}" method="GET">
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
                                <a href="{{ route('laporan.anggota') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <a class="btn btn-label-info btn-round btn-sm" href="{{ route('pdf.anggota') }}">
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
                                <th>Tanggal Bergabung</th>
                                <th>Kecamatan</th>
                                <th>Kelurahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $no = 1;
                            @endphp

                            @foreach ($user as $s)
                                @if ($s->role == 'User' && !empty($s->nm_koperasi))
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $s->nm_koperasi }}</td>
                                        <td>{{ $s->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($s->created_at)->translatedFormat('l, d F Y') }}</td>
                                        <td>{{ $s->kecamatan }}</td>
                                        <td>{{ $s->desa }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        $("#basic-datatables").DataTable({});
    });

</script>
@endsection
