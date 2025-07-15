@extends('layouts.master')
@section('title', 'Data Simpanan')
@section('bread')
<div class="page-header">
    <h3 class="fw-bold mb-3"> @if(Auth::check() && Auth::user()->role === 'User')
        {{ Auth::user()->nm_koperasi }}
        @endif
    </h3>
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
            <a href="">Simpanan</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('simpanan.index') }}">@yield('title')</a>
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah"><span class="btn-label"><i class="fa fa-plus"></i></span>Add</button>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                @if (auth()->user()->role === "Admin")
                                <th>Nama</th>
                                @endif
                                <th>Tanggal Transaksi</th>
                                <th>Jumlah Simpanan</th>
                                @if (auth()->user()->role === "Admin")
                                <th style="width: 10%">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $no = 1;
                            @endphp

                            @foreach ($simpananGrouped as $index => $s)
                            <tr>
                                <td>{{ $no++ }}</td>
                                @if (auth()->user()->role === "Admin")
                                <td>{{ $s['user']->name }}</td>
                                @endif
                                <td>{{ $s['tanggal_transaksi'] }}</td>
                                <td>Rp {{ number_format($s['total_simpanan'], 0, ',', '.') }}</td>
                                @if (auth()->user()->role === "Admin")
                                <td>
                                    <div class="form-button-action">
                                        {{-- <a href="{{ route('simpanan.edit', $s['user']->id) }}" data-bs-toggle="modal" class="btn btn-link btn-primary btn-lg" data-bs-target="#Edit{{ $s['user']->id }}" data-original-title="Edit Task"><i class="fa fa-edit"></i></a> --}}
                                        <form id="delete-form-{{ $s['user']->id }}" action="{{ route('simpanan.destroyByUser', ['user_id' => $s['user']->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-link btn-danger delete-btn" data-form-id="{{ $s['user']->id }}">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        <tfoot>
                            <tr>
                                <td colspan="{{ auth()->check() && auth()->user()->role === 'Admin' ? 3 : 2 }}" class="text-center">
                                    Jumlah Kapitalisasi
                                </td>
                                <td class="text-end">Rp {{ number_format($kapitalisasi, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah data --}}

<div class="modal fade" id="tambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Data Simpanan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('simpanan.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-floating form-floating-custom mb-3">
                        <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                            <option value="">--Pilih Nomor Anggota--</option>
                            @foreach ($nasabah->whereNotNull("nm_koperasi") as $n)
                            <option value="{{ $n->id}}">{{ $n->nm_koperasi}}</option>
                            @endforeach
                        </select>
                        <label for="user_id">Pilih Nomor Anggota</label>
                    </div>
                    <div class="form-floating form-floating-custom mb-3">
                        <select class="form-control @error('jenis_simpanan') is-invalid @enderror" onchange="setJumlahSimpanan()" id="jenis_simpanan" name="jenis_simpanan">
                            <option value="">Pilih Jenis Simpanan</option>
                            <option value="Simpanan Wajib">Simpanan Wajib</option>
                            {{-- <option value="Simpanan Pokok">Simpanan Pokok</option> --}}
                            <option value="Simpanan Dakesma">Simpanan Dakesma</option>
                            {{-- <option value="Biaya Administrasi">Biaya Administrasi</option> --}}
                        </select>
                        <label for="jenis_simpanan">Jenis Simpanan</label>
                    </div>
                    <div class="form-floating form-floating-custom mb-3">
                        <input type="text" id="jumlah_simpanan_display" oninput="formatUang(this)" class="form-control @error('jumlah_simpanan') is-invalid @enderror" readonly placeholder="Jumlah Simpanan" value="{{ old('jumlah_simpanan') ? 'Rp ' . number_format(old('jumlah_simpanan'), 0, ',', '.') : '' }}" />
                        <input type="hidden" readonly name="jumlah_simpanan" class="form-control @error('jumlah_simpanan') is-invalid @enderror" id="jumlah_simpanan" placeholder="Jumlah Simpanan" value="{{ old('jumlah_simpanan') }}" />
                        <label for="floatingInput">Jumlah Simpanan</label>
                        @error('jumlah_simpanan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
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

@if(session("error"))
<script>
    swal.fire({
        title: "Gagal!"
        , text: "{{ session('error') }}"
        , icon: "error"
        , confirmButtonText: "Ok"
    });

</script>
@endif

@if(session('delete'))
<script>
    Swal.fire({
        title: "Dihapus!"
        , text: "{{ session('delete') }}"
        , icon: "warning"
        , confirmButtonText: "OK"
    });

</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('/assets/js/plugin/datatables/datatables.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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


    function formatUang(input) {
        let value = input.value.replace(/\D+/g, '');
        if (value.length > 14) value = value.slice(14);
        let formatted = 'Rp ' + value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        input.value = formatted;
        document.getElementById('jumlah_simpanan').value = value;
    }

    function setJumlahSimpanan() {
        const jenis = document.getElementById('jenis_simpanan').value;
        const jumlahInput = document.getElementById('jumlah_simpanan_display');

        const awal = 50000;
        const potongan = awal * 0.02;
        const jumlah = awal - potongan;

        if (jenis) {
            jumlahInput.value = jumlah;
            formatUang(jumlahInput);
        } else {
            jumlahInput.value = '';
            document.getElementById('jumlah_simpanan').value = '';
        }
    }


    $(document).ready(function() {
        $("#basic-datatables").DataTable({});
        $('.delete-btn').on('click', function() {
            var formId = $(this).data('form-id');

            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                $('#delete-form-' + formId).submit();
            }
        });
    });

</script>
@endsection
