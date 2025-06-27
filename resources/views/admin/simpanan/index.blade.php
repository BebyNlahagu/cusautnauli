@extends('layouts.master')
@section('title', 'Data Simpanan')
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
                                <th>Nama</th>
                                <th>Tanggal Simpanan</th>
                                <th>Jenis Simpanan</th>
                                <th>Jumlah Simpanan</th>
                                <th style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $no = 1;
                            @endphp

                            @foreach ($simpanan as $s)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $s->nasabah->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($s->created_at)->translatedFormat('l, d F Y') }}</td>
                                <td>{{ $s->jenis_simpanan}}</td>
                                <td class="text-end bold">Rp {{ number_format($s->jumlah_simpanan, 0, ',', '.') }}</td>
                                <td>
                                    <div class="form-button-action">
                                        <a href="{{ route('simpanan.edit', $s->id) }}" data-bs-toggle="modal" class="btn btn-link btn-primary btn-lg" data-bs-target="#Edit{{ $s->id }}" data-original-title="Edit Task"><i class="fa fa-edit"></i></a>
                                        <form id="delete-form-{{ $s->id }}" action="{{ route('simpanan.destroy', $s->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" data-bs-toggle="tooltip" class="btn btn-link btn-danger" data-original-title="Remove" onclick="confirmDelete({{ $s->id }})"><i class="fa fa-times"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-center">Jumlah Kapitalisasi</td>
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
                            <option value="">--Pilih Nomor Registrasi--</option>
                            @foreach ($nasabah as $n)
                            <option value="{{ $n->id}}">{{ $n->nmr_anggota}}</option>
                            @endforeach
                        </select>
                        <label for="user_id">Pilih Nomor Registrasi</label>
                    </div>
                    <div class="form-floating form-floating-custom mb-3">
                        <select class="form-control @error('jenis_simpanan') is-invalid @enderror" onchange="setJumlahSimpanan()" id="jenis_simpanan" name="jenis_simpanan">
                            <option value="">Pilih Jenis Simpanan</option>
                            <option value="Simpanan Wajib">Simpanan Wajib</option>
                            <option value="Simpanan Pokok">Simpanan Pokok</option>
                            <option value="Simpanan Dakesma">Simpanan Dakesma</option>
                            <option value="Biaya Administrasi">Biaya Administrasi</option>
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

{{-- update --}}
@foreach ($simpanan as $s)
<div class="modal fade" id="Edit{{ $s->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Data Simpanan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('simpanan.update', $s->id) }}" method="post" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="modal-body">
                    <div class="form-floating form-floating-custom mb-3">
                        <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                            <option value="">--Pilih Nomor Registrasi--</option>
                            @foreach ($nasabah->where('status', 'Verify') as $n)
                                <option value="{{ $n->id }}" {{ $n->id == $s->user_id ? 'selected' : '' }}>{{ $n->nmr_anggota }}</option>
                            @endforeach
                        </select>
                        <label for="user_id">Pilih Nomor Registrasi</label>
                    </div>
                    <div class="form-floating form-floating-custom mb-3">
                        <select class="form-control @error('jenis_simpanan') is-invalid @enderror" id="jenis_simpanan" name="jenis_simpanan">
                            <option value="">Pilih Jenis Simpanan</option>
                            <option value="Simpanan Wajib" {{ $s->jenis_simpanan == 'Simpanan Wajib' ? 'selected' : ''}}>Simpanan Wajib</option>
                            <option value="Simpanan Pokok" {{ $s->jenis_simpanan == 'Simpanan Pokok' ? 'selected' : ''}}>Simpanan Pokok</option>
                            <option value="Simpanan Dakesma" {{ $s->jenis_simpanan == 'Simpanan Dakesma' ? 'selected' : ''}}>Simpanan Dakesma</option>
                            <option value="Biaya Administrasi" {{ $s->jenis_simpanan == 'Biaya Administrasi' ? 'selected' : ''}}>Biaya Administrasi</option>
                        </select>
                        <label for="jenis_simpanan">Jenis Simpanan</label>
                    </div>
                    <div class="form-floating form-floating-custom mb-3">
                        <input type="text" id="jumlah_simpanan_display" oninput="formatUang(this)" class="form-control @error('jumlah_simpanan') is-invalid @enderror" placeholder="Jumlah Simpanan" value="{{ old('jumlah_simpanan') ? 'Rp ' . number_format(old('jumlah_simpanan'), 0, ',', '.') : (isset($simpanan) ? 'Rp ' . number_format($s->jumlah_simpanan, 0, ',', '.') : '') }}" />
                        <input type="hidden" name="jumlah_simpanan" class="form-control @error('jumlah_simpanan') is-invalid @enderror" id="jumlah_simpanan" placeholder="Jumlah Simpanan" value="{{ old('jumlah_simpanan', $s->jumlah_simpanan ?? '') }}" />
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
@endforeach

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

    // function formatUang(input) {
    //     let value = input.value.replace(/\D+/g, '');
    //     if (value.length > 14) value = value.slice(0, 14);
    //     let formatted = 'Rp ' + value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    //     input.value = formatted;
    //     document.getElementById('jumlah_simpanan').value = value;
    // }

    // document.addEventListener('DOMContentLoaded', function() {
    //     const displayInput = document.getElementById('jumlah_simpanan_display');
    //     const hiddenInput = document.getElementById('jumlah_simpanan');

    //     if (hiddenInput.value) {
    //         displayInput.value = 'Rp ' + Number(hiddenInput.value).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    //     }
    // });

    // function formatUangEdit(input, id) {
    //     let value = input.value.replace(/\D+/g, '');
    //     if (value.length > 14) value = value.slice(0, 14);
    //     let formatted = 'Rp ' + value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    //     input.value = formatted;
    //     document.getElementById('jumlah_simpanan_edit_' + id).value = value;
    // }

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
    });

</script>
@endsection
