@extends('layouts.master')
@section('title', 'Data Nasabah')
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
            <a href="">Anggota</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('nasabah.index') }}">@yield('title')</a>
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
                <button class="btn btn-link btn-primary" id="LihatdataBaru">Anggota Baru</button>
            </div>
            <div class="card-body" id="data-terverifikasi">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No.Anggota</th>
                                <th>Tanggal Bergabung</th>
                                <th>No. NIK</th>
                                <th>Nama</th>
                                <th>Jenis Kelamin</th>
                                <th>Tanggal Lahir</th>
                                <th>No. HP/Wa</th>
                                <th>Pekerjaan</th>
                                <th>Kecamatan</th>
                                <th>Kelurahan</th>
                                <th>Alamat</th>
                                <th>Foto Diri</th>
                                <th>Foto KTP</th>
                                <th>Foto KK</th>
                                <th>Simpanan Pokok</th>
                                <th>Biaya Administrasi</th>
                                <th>Status</th>
                                <th style="width: 10%">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $no = 1;
                            @endphp

                            @foreach ($nasabahTerverifikasi as $n)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $n->nm_koperasi }}</td>
                                <td>{{ \Carbon\Carbon::parse($n->created_at)->translatedFormat('l, d F Y') }}</td>
                                <td>{{ $n->Nik }}</td>
                                <td>{{ $n->name }}</td>
                                <td>{{ $n->jenis_kelamin }}</td>
                                <td>{{ \Carbon\Carbon::parse($n->tanggal_lahir)->translatedFormat('l, d F Y') }}</td>
                                <td>{{ $n->no_telp }}</td>
                                <td>{{ $n->pekerjaan }}</td>
                                <td>{{ $n->kecamatan }}</td>
                                <td>{{ $n->desa }}</td>
                                <td>{{ $n->kelurahan }}</td>
                                <td>
                                    @if($n->foto)
                                    <a href="{{ Storage::url('images/' . $n->foto) }}" target="_blank">
                                        Lihat Foto
                                    </a>
                                    @else
                                    <span>Data Foto Tidak Ada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($n->ktp)
                                    <a href="{{ Storage::url('images/' . $n->ktp) }}" target="_blank">
                                        lihat ktp
                                    </a>
                                    @else
                                    <span>Data Foto Tidak Ada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($n->kk)
                                    <a href="{{ Storage::url('images/' . $n->kk) }}" target="_blank">
                                        lihat KK
                                    </a>
                                    @else
                                    <span>Data Foto Tidak Ada</span>
                                    @endif
                                </td>

                                <td><input type="checkbox" disabled {{ $n->simpanan_wajib ? 'checked' : '' }}></td>
                                <td><input type="checkbox" disabled {{ $n->administrasi ? 'checked' : '' }}></td>

                                <td>@if($n->status == "Verify")
                                        <span class="badge text-bg-success">Terverifikasi</span>
                                    @else
                                        <span class="badge text-bg-danger">Tidak Terverifikasi</span>
                                    @endif</td>
                                <td>
                                    <div class="form-button-action">
                                        <form id="delete-form-{{ $n->id }}" action="{{ route('nasabah.destroy', $n->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" data-bs-toggle="tooltip" class="btn btn-link btn-danger" data-original-title="Remove" onclick="confirmDelete({{ $n->id }})" style="display: inline;"><i class="fa fa-times"></i></button>
                                        </form>

                                        @if ($n->status != "Verify")
                                        <form id="ubahStatusForm{{ $n->id }}" action="{{ route('nasabah.verify', $n->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="button" class="btn btn-warning btn-link" title="Ubah Status" onclick="confirmUbahStatus({{ $n->id }})">
                                                <i class="fa fa-angle-left"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

            <div class="card-body" id="tidak-terverifikasi" style="display:none;">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No.Reg</th>
                                <th>No. NIK</th>
                                <th>Nama</th>
                                <th>Jenis Kelamin</th>
                                <th>Tanggal Lahir</th>
                                <th>No. HP/Wa</th>
                                <th>Pekerjaan</th>
                                <th>Kecamatan</th>
                                <th>Kelurahan</th>
                                <th>Alamat</th>
                                <th>Foto Diri</th>
                                <th>Foto KTP</th>
                                <th>Foto KK</th>
                                <th>Status</th>
                                <th style="width: 10%">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $no = 1;
                            @endphp

                            @foreach ($nasabahTidakTerverifikasi as $n)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $n->nmr_anggota }}</td>
                                <td>{{ $n->Nik }}</td>
                                <td>{{ $n->name }}</td>
                                <td>{{ $n->jenis_kelamin }}</td>
                                <td>{{ \Carbon\Carbon::parse($n->tanggal_lahir)->translatedFormat('l, d F Y') }}
                                </td>
                                <td>{{ $n->no_telp }}</td>
                                <td>{{ $n->pekerjaan }}</td>
                                <td>{{ $n->kecamatan }}</td>
                                <td>{{ $n->desa }}</td>
                                <td>{{ $n->kelurahan }}</td>
                                <td>
                                    @if($n->foto)
                                    <a href="{{ Storage::url('images/' . $n->foto) }}" target="_blank">
                                        Lihat Foto
                                    </a>
                                    @else
                                    <span>Data Foto Tidak Ada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($n->ktp)
                                    <a href="{{ Storage::url('images/' . $n->ktp) }}" target="_blank">
                                        lihat ktp
                                    </a>
                                    @else
                                    <span>Data Foto Tidak Ada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($n->kk)
                                    <a href="{{ Storage::url('images/' . $n->kk) }}" target="_blank">
                                        lihat KK
                                    </a>
                                    @else
                                    <span>Data Foto Tidak Ada</span>
                                    @endif
                                </td>
                        
                                <td>@if($n->status == "Verify")
                                    <span class="badge text-bg-success">Terverifikasi</span>
                                    @else
                                    <span class="badge text-bg-danger">Tidak Terverifikasi</span>
                                    @endif</td>
                                <td>
                                    <div class="form-button-action">
                                        <form action="{{ route('nasabah.updateCheckbox', $n->id) }}" method="POST" style="display:inline-flex; align-items: center;">
                                            @csrf
                                            @method('PUT')

                                            <label style="margin-right: 5px;">
                                                <input type="checkbox" class="checkbox-update" data-id="{{ $n->id }}" data-field="simpanan_wajib" {{ $n->simpanan_wajib ? 'checked' : '' }}>
                                                Simpanan Pokok
                                            </label>

                                            <label style="margin-right: 5px;">
                                                <input type="checkbox" class="checkbox-update" data-id="{{ $n->id }}" data-field="administrasi" {{ $n->administrasi ? 'checked' : '' }}>
                                                Biaya Administrasi
                                            </label>
                                        </form>
                                        <form id="delete-form-{{ $n->id }}" action="{{ route('nasabah.destroy', $n->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" data-bs-toggle="tooltip" class="btn btn-link btn-danger" data-original-title="Remove" onclick="confirmDelete({{ $n->id }})" style="display: inline;"><i class="fa fa-times"></i></button>
                                        </form>

                                        @if ($n->status != "Verify")
                                        <form id="ubahStatusForm{{ $n->id }}" action="{{ route('nasabah.verify', $n->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="button" class="btn btn-warning btn-link" title="Ubah Status" onclick="confirmUbahStatus({{ $n->id }})">
                                                <i class="fa fa-angle-left"></i>
                                            </button>
                                        </form>
                                        @endif
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

@if (session('error'))
<script>
    Swal.fire({
        title: "Gagal!"
        , text: "{{ session('error') }}"
        , icon: "error"
        , confirmButtonText: "OK"
    });

</script>
@endif

@if (session('delete'))
<script>
    Swal.fire({
        title: "Dihapus!"
        , text: "{{ session('delete') }}"
        , icon: "warning"
        , confirmButtonText: "OK"
    });

</script>
@endif

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function confirmUbahStatus(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?'
            , text: "Verifikasi Data!"
            , icon: 'warning'
            , showCancelButton: true
            , confirmButtonColor: '#28a745'
            , cancelButtonColor: '#d33'
            , confirmButtonText: 'Ya, Terverifikasi!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('ubahStatusForm' + id).submit();
            }
        })
    }

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

    function validateImage(input, previewId, errorId) {
        const file = files.input[0];
        const tipe = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        const maxSize = 2 * 1024 * 1024;

        $(errorId).text('');
        $(previewId).attr('src', '');

        if (file) {
            if (!tipe.includes(file.type)) {
                $(errorId).text('File harus berupa gambar (jpeg, png, jpg, webp).');
                input.value = '';
                return;
            }

            if (file.size > maxSize) {
                $(errorId).text('Ukuran file maksimal 2MB.');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                $(previewId).attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    }

    $(document).ready(function() {
        // Preview for Foto Diri
        $('#foto').change(function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    $('#fotoPreview').attr('src', event.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        });

        // Preview for KTP
        $('#ktp').change(function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    $('#ktpPreview').attr('src', event.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        });

        // Preview for Kartu Keluarga
        $('#kk').change(function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    $('#kkPreview').attr('src', event.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        });

        $('#LihatdataBaru').on('click', function() {
            $('#data-terverifikasi').toggle();
            $('#tidak-terverifikasi').toggle();

            if ($('#tidak-terverifikasi').is(':visible')) {
                $(this).text('Anggota Terverifikasi');
            } else {
                $(this).text('Anggota Baru');
            }
        });

    });

    $('.checkbox-update').on('change', function () {
        const checkbox = $(this);
        const id = checkbox.data('id');
        const field = checkbox.data('field');
        const isChecked = checkbox.is(':checked') ? 1 : 0;

        $.ajax({
            url: `/nasabah/update-checkbox/${id}`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                [field]: isChecked
            },
            success: function (res) {
                console.log('Berhasil update:', res);
            },
            error: function (err) {
                alert('Gagal update checkbox!');
                checkbox.prop('checked', !isChecked);
            }
        });
    });

</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('/assets/js/plugin/datatables/datatables.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $("#basic-datatables").DataTable({});

        // Add Row
        $("#add-row").DataTable({
            pageLength: 5
        , });

        var action =
            '<td> <div class="form-button-action"> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';

        $("#addRowButton").click(function() {
            $("#add-row")
                .dataTable()
                .fnAddData([
                    $("#addName").val(), $("#addPosition").val(), $("#addOffice").val(), action
                , ]);
            $("#addRowModal").modal("hide");
        });
    });

</script>
@endsection
