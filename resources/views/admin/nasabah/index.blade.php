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
                {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah"><span class="btn-label"><i class="fa fa-plus"></i></span>Add</button> --}}
            </div>
            <div class="card-body">
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
                                <th>Kelurahan</th>
                                <th>Pekerjaan</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $no = 1;
                            @endphp

                            @foreach ($nasabah as $n)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $n->nmr_anggota }}</td>
                                <td>{{ $n->Nik }}</td>
                                <td>{{ $n->name }}</td>
                                <td>{{ $n->jenis_kelamin }}</td>
                                <td>{{ \Carbon\Carbon::parse($n->tanggal_lahir)->translatedFormat('l, d F Y') }}
                                </td>
                                <td>{{ $n->no_telp }}</td>
                                <td>{{ $n->kelurahan }}</td>
                                <td>{{ $n->pekerjaan }}</td>
                                <td>{{ $n->alamat }}</td>
                                <td>@if($n->status == "Verify")
                                    <span class="badge text-bg-success">Terverifikasi</span>
                                    @else
                                    <span class="badge text-bg-danger">Tidak Terverifikasi</span>
                                    @endif</td>
                                <td>
                                    <div class="form-button-action">
                                        {{-- <button type="button" class="btn btn-link btn-info btn-lg" title="Email dan Password" data-bs-toggle="modal" data-bs-target="#DetailModal{{ $n->id }}"><i class="fa fa-eye"></i></button> --}}

                                        <a href="{{ route('nasabah.edit', $n->id) }}" data-bs-toggle="modal" class="btn btn-link btn-primary btn-lg" data-bs-target="#edit{{ $n->id }}" data-original-title="Edit Task"><i class="fa fa-edit"></i></a>
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
                                        @else
                                        <button type="button" class="btn btn-link btn-info btn-lg" title="Email dan Password" data-bs-toggle="modal" data-bs-target="#DetailModal{{ $n->id }}"><i class="fa fa-eye"></i></button>
                                        <div class="modal fade" id="DetailModal{{ $n->id }}" tabindex="-1" aria-labelledby="DetailModalLabel{{ $n->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="DetailModalLabel{{ $n->id }}">Detail Akun Nasabah</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Email:</strong> {{ $n->user->email ?? 'Tidak tersedia' }}</p>
                                                        <p><strong>Password:</strong> {{ $n->user->plain_password ?? 'Tidak tersedia' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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

<!-- Modal Tambah-->
{{-- <div class="modal fade" id="tambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Data Nasabah</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('nasabah.store') }}" method="post" enctype="multipart/form-data">
@csrf
<div class="card-body">
    <div class="modal-body">
        <div class="form-floating form-floating-custom mb-3">
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Nama Lengkap" value="{{ old('name') }}" />
            <label for="floatingInput">Nama Lengkap</label>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-floating form-floating-custom mb-3">
            <input type="number" class="form-control @error('Nik') is-invalid @enderror" id="Nik" name="Nik" placeholder="Nomor NIK" value="{{ old('Nik') }}" />
            <label for="floatingInput">Nomor NIK</label>
            @error('Nik')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-floating form-floating-custom mb-3">
            <select name="jenis_kelamin" id="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror">
                <option value="">-pilih-</option>
                <option value="Laki-laki">Laki-Laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
            <label for="floatingInput">Jenis Kelamin</label>
            @error('jenis_kelamin')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-floating form-floating-custom mb-3">
            <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" placeholder="Tanggal Lahir" value="{{ old('tanggal_lahir') }}" />
            <label for="floatingInput">Tanggal Lahir</label>
            @error('tanggal_lahir')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-floating form-floating-custom mb-3">
            <input type="number" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" id="No_telp" placeholder="No. Hp/Wa" value="{{ old('no_telp') }}" />
            <label for="floatingInput">No. Hp/Wa</label>
            @error('no_telp')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-floating form-floating-custom mb-3">
            <input type="date" name="tanggal_masuk" class="form-control @error('tanggal_masuk') is-invalid @enderror" id="tanggal_masuk" placeholder="No. Hp/Wa" value="{{ old('tanggal_masuk') }}" />
            <label for="floatingInput">Tanggal Masuk</label>
            @error('tanggal_masuk')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-floating form-floating-custom mb-3">
            <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" cols="30" rows="3">{{ old('alamat') }}</textarea>
            <label for="floatingInput">Alamat</label>
            @error('alamat')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-floating form-floating-custom mb-3">
            <input type="text" name="kelurahan" class="form-control @error('kelurahan') is-invalid @enderror" id="kelurahan" placeholder="Kelurahan" value="{{ old('kelurahan') }}" />
            <label for="floatingInput">Kelurahan</label>
            @error('kelurahan')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-floating form-floating-custom mb-3">
            <input type="text" name="pekerjaan" class="form-control @error('pekerjaan') is-invalid @enderror" id="pekerjaan" placeholder="Jenis Usaha" value="{{ old('pekerjaan') }}" />
            <label for="floatingInput">Pekerjaan</label>
            @error('pekerjaan')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <br>
        <hr>

        <div class="form-floating form-floating-custom mb-3">
            <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" id="foto" placeholder="Jenis Usaha" value="{{ old('foto') }}" />
            <label for="floatingInput">Foto Diri</label>
            <img id="fotoPreview" src="#" alt="Foto Preview" style="max-width: 200px; margin-top: 10px; display: none;" />
            @error('foto')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-floating form-floating-custom mb-3">
            <input type="file" name="ktp" class="form-control @error('ktp') is-invalid @enderror" id="ktp" placeholder="Jenis Usaha" value="{{ old('ktp') }}" />
            <label for="floatingInput">KTP</label>
            <img id="ktpPreview" src="#" alt="KTP Preview" style="max-width: 200px; margin-top: 10px; display: none;" />
            @error('ktp')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-floating form-floating-custom mb-3">
            <input type="file" name="kk" class="form-control @error('kk') is-invalid @enderror" id="kk" placeholder="Jenis Usaha" value="{{ old('kk') }}" />
            <label for="floatingInput">Kartu Keluarga</label>
            <img id="kkPreview" src="#" alt="KK Preview" style="max-width: 200px; margin-top: 10px; display: none;" />
            @error('kk')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-success">Save</button>
</div>
</form>
</div>
</div>
</div> --}}

{{-- Modal Edit  --}}
@foreach ($nasabah as $n)
<div class="modal fade" id="edit{{ $n->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Data Nasabah</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('nasabah.update', $n->id) }}" method="post" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="modal-body">
                    <div class="form-floating form-floating-custom mb-3">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Nama Lengkap" value="{{ $n->name }}" />
                        <label for="floatingInput">Nama Lengkap</label>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="number" class="form-control @error('Nik') is-invalid @enderror" id="Nik" name="Nik" placeholder="Nomor NIK" value="{{ $n->Nik }}" />
                        <label for="floatingInput">Nomor NIK</label>
                        @error('Nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror">
                            <option value="">-pilih-</option>
                            <option value="Laki-laki" {{ $n->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>
                                Laki-Laki</option>
                            <option value="Perempuan" {{ $n->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>
                                Perempuan</option>
                        </select>
                        <label for="floatingInput">Jenis Kelamin</label>
                        @error('jenis_kelamin')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" placeholder="Tanggal Lahir" value="{{ old('tanggal_lahir') }}" />
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        @error('tanggal_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="number" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" id="No_telp" placeholder="No. Hp/Wa" value="{{ $n->no_telp }}" />
                        <label for="floatingInput">No. Hp/Wa</label>
                        @error('no_telp')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="date" class="form-control @error('tanggal_masuk') is-invalid @enderror" id="tanggal_masuk" name="tanggal_masuk" placeholder="Tanggal Lahir" value="{{ old('tanggal_masuk') }}" />
                        <label for="tanggal_masuk">Tanggal Masuk</label>
                        @error('tanggal_masuk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" cols="30" rows="3">{{ $n->alamat }}</textarea>
                        <label for="floatingInput">Alamat</label>
                        @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="text" name="kelurahan" class="form-control @error('kelurahan') is-invalid @enderror" id="kelurahan" placeholder="Kelurahan" value="{{ $n->kelurahan }}" />
                        <label for="floatingInput">Kelurahan</label>
                        @error('kelurahan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="text" name="pekerjaan" class="form-control @error('pekerjaan') is-invalid @enderror" id="pekerjaan" placeholder="Jenis Usaha" value="{{ $n->pekerjaan }}" />
                        <label for="floatingInput">Pekerjaan</label>
                        @error('pekerjaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <br>
                    <hr>
                    <div class="form-floating form-floating-custom mb-3">
                        <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" id="foto" />
                        <label for="floatingInput">Foto Diri</label>
                        <img id="fotoPreview" src="{{ old('foto', isset($n->foto) ? asset('images/' . $n->foto) : '#') }}" alt="Foto Preview" style="max-width: 200px; margin-top: 10px; display: {{ isset($n->foto) ? 'block' : 'none' }};" />
                        @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="file" name="ktp" class="form-control @error('ktp') is-invalid @enderror" id="ktp" />
                        <label for="floatingInput">KTP</label>
                        <img id="ktpPreview" src="{{ old('ktp', isset($n->ktp) ? asset('images/' . $n->ktp) : '#') }}" alt="KTP Preview" style="max-width: 200px; margin-top: 10px; display: {{ isset($n->ktp) ? 'block' : 'none' }};" />
                        @error('ktp')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="file" name="kk" class="form-control @error('kk') is-invalid @enderror" id="kk" />
                        <label for="floatingInput">Kartu Keluarga</label>
                        <img id="kkPreview" src="{{ old('kk', isset($n->kk) ? asset('images/' . $n->kk) : '#') }}" alt="KK Preview" style="max-width: 200px; margin-top: 10px; display: {{ isset($n->kk) ? 'block' : 'none' }};" />
                        @error('kk')
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
