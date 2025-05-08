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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">@yield('title')</h4>
               @if (auth()->user()->role == "Admin")
                    <a class="btn btn-success" href="{{ route('pdf.angsuran')}}"><i class="fa fa-download"></i></a>
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
                                <th>Total Pinjaman</th>
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
                                    <td>{{ \Carbon\Carbon::parse($angsurans->first()->created_at)->translatedFormat('l, d F Y') }}
                                    </td>
                                    <td>{{ $angsurans->first()->pinjaman->lama_pinjaman }}</td>
                                    <td>Rp {{ number_format($angsurans->first()->pinjaman->terima_total, 0, ',', '.') }}
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
<script src="{{ asset('/assets/js/plugin/datatables/datatables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $("#basic-datatables").DataTable({});
    });
</script>
@endsection