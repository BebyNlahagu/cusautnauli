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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">@yield('title')</h4>
                @if (auth()->user()->role == "Admin")
                    <a class="btn btn-success" href="{{ route('pdf.pinjaman')}}"><i class="fa fa-download"></i></a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>No. NIK</th>
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
                                <td>{{ $n->nasabah->Nik }}</td>
                                <td>{{ $n->nasabah->name }}</td>
                                <td>Rp {{ number_format((float)$n->jumlah_pinjaman, 0, ',', '.') }}</td>
                                <td>{{ $n->lama_pinjaman }}</td>
                                <td>{{ $n->bunga_pinjaman }} %</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('/assets/js/plugin/datatables/datatables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $("#basic-datatables").DataTable({});
    });
</script>
@endsection