@extends('layouts.master')
@section('title', 'Profile')
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
                <a href="">Profile</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('angsuran.index') }}">@yield('title')</a>
            </li>
        </ul>
    </div>
@endsection
@section('content')
    <div class="row">
        @if (auth()->user()->role == 'Admin')
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">

                    </div>
                </div>
            </div>
            <div class="col-md-8">

            </div>
        @elseif(auth()->user()->role == 'User')
            <div class="col-md-12">
                <form action="{{ route('user.update', $user->id) }}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="card">
                         <div class="card-body">
                        <div class="form-floating form-floating-custom mb-3">
                            <input type="text" disabled class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" placeholder="Nama Lengkap" value="{{ $user->name }}" />
                            <label for="floatingInput">Nama Lengkap</label>
                            @error('name')
                                {{-- <div class="invalid-feedback">{{ $message }}</div> --}}
                            @enderror
                        </div>

                        <div class="form-floating form-floating-custom mb-3">
                            <input type="number" disabled class="form-control @error('Nik') is-invalid @enderror" id="Nik"
                                name="Nik" placeholder="Nomor NIK" value="{{ $user->Nik }}" />
                            <label for="floatingInput">Nomor NIK</label>
                            @error('Nik')
                                {{-- <div class="invalid-feedback">{{ $message }}</div> --}}
                            @enderror
                        </div>
                        <div class="form-floating form-floating-custom mb-3">
                            <select name="jenis_kelamin" id="jenis_kelamin"
                                disabled class="form-control @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">-pilih-</option>
                                <option value="Laki-laki" {{ $user->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-Laki</option>
                                <option value="Perempuan" {{ $user->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                            <label for="floatingInput">Jenis Kelamin</label>
                            @error('jenis_kelamin')
                                {{-- <div class="invalid-feedback">{{ $message }}</div> --}}
                            @enderror
                        </div>
       
                        <div class="form-floating form-floating-custom mb-3">
                            <input type="date" disabled
                                class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                                id="tanggal_lahir"
                                name="tanggal_lahir" 
                                placeholder="Tanggal Lahir" 
                                value="{{ old('tanggal_lahir', $user->tanggal_lahir ?? '') }}" />
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            @error('tanggal_lahir')
                                {{-- <div class="invalid-feedback">{{ $message }}</div> --}}
                            @enderror
                        </div>


                        <div class="form-floating form-floating-custom mb-3">
                            <input type="number" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror"
                                id="No_telp" placeholder="No. Hp/Wa" value="{{ $user->no_telp }}" />
                            <label for="floatingInput">No. Hp/Wa</label>
                            @error('no_telp')
                                {{-- <div class="invalid-feedback">{{ $message }}</div> --}}
                            @enderror
                        </div>

                        <div class="form-floating form-floating-custom mb-3">
                            <input type="text" disabled name="pekerjaan" class="form-control @error('pekerjaan') is-invalid @enderror"
                                id="pekerjaan" placeholder="Jenis Usaha" value="{{ $user->pekerjaan }}" />
                            <label for="floatingInput">Pekerjaan</label>
                            @error('pekerjaan')
                            @enderror
                        </div>


                        <div class="form-floating form-floating-custom mb-3">
                            @php
                                $selectedKecamatan = old('kecamatan', $data->kecamatan ?? '');
                                $selectedDesa = old('desa', $data->desa ?? '');
                            @endphp
                            <select name="kecamatan" id="kecamatan" class="form-control @error('kecamatan') is-invalid @enderror">
                                <option value="">--Pilih--</option>
                                <option value="Kecamatan Siborong Borong" {{ $selectedKecamatan == 'Kecamatan Siborong Borong' ? 'selected' : '' }}>Kecamatan Siborong Borong</option>
                                <option value="Kecamatan Paranginan" {{ $selectedKecamatan == 'Kecamatan Paranginan' ? 'selected' : '' }}>Kecamatan Paranginan</option>
                                <option value="Kecamatan Lintong Nihuta" {{ $selectedKecamatan == 'Kecamatan Lintong Nihuta' ? 'selected' : '' }}>Kecamatan Lintong Nihuta</option>
                            </select>
                            <label for="kecamatan">Kecamatan</label>
                            @error('kecamatan')
                            {{-- <div class="invalid-feedback">{{ $message }}</div> --}}
                            @enderror
                        </div>

                        <div class="form-floating form-floating-custom mb-3">
                            <select name="desa" id="desa" class="form-control @error('desa') is-invalid @enderror">
                                {{-- Ajax --}}
                            </select>
                            <label for="desa">Desa</label>
                            @error('desa')
                            {{-- <div class="invalid-feedback">{{ $message }}</div> --}}
                            @enderror
                        </div>


                        <div class="form-floating form-floating-custom mb-3">
                            <input type="text" name="kelurahan" class="form-control @error('kelurahan') is-invalid @enderror"
                                id="kelurahan" placeholder="Kelurahan" value="{{ $user->kelurahan }}" />
                            <label for="floatingInput">Kelurahan</label>
                            @error('kelurahan')
                                {{-- <div class="invalid-feedback">{{ $message }}</div> --}}
                            @enderror
                        </div>                    
                    </div>
                    <div class="card-footer">
                        {{-- <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button> --}}
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <script>
        const desaMap = {
            "Kecamatan Paranginan": [
                "Desa Paranginan Selatan", "Desa Siborutorop", "Desa Lumban Sialaman", "Desa Lumban Barat",
                "Desa Lobu Tolong", "Desa Sihonongan", "Desa Paranginan Utara", "Desa Pearung",
                "Desa Paerung Silali", "Desa Lumban Sianturi", "Desa Lobutolong Habinsaran"
            ],
            "Kecamatan Lintong Nihuta": [
                "Desa Nagasaribu I", "Desa Nagasaribu II", "Desa Nagasaribu III", "Desa Nagasaribu IV",
                "Desa Nagasaribu V", "Desa Sigompul", "Desa Pargaulan"
            ],
            "Kecamatan Siborong Borong": [
                "Desa Siborong Borong", "Desa Sitampurung", "Desa Sigalingging"
            ]
        };

        $(document).ready(function(){
            const kecamatanSelect = $('#kecamatan');
            const desaSelect = $('#desa');

            const selectedKecamatan = "{{ $selectedKecamatan }}";
            const selectedDesa = "{{ $selectedDesa }}";

            function populateDesa(kecamatan, selected = '') {
                desaSelect.empty().append('<option value="">-- Pilih Desa --</option>');
                if (desaMap[kecamatan]) {
                    desaMap[kecamatan].forEach(function(desa) {
                        const isSelected = desa === selected ? 'selected' : '';
                        desaSelect.append(`<option value="${desa}" ${isSelected}>${desa}</option>`);
                    });
                }
            }

            if (selectedKecamatan) {
                kecamatanSelect.val(selectedKecamatan);
                populateDesa(selectedKecamatan, selectedDesa);
            }

            kecamatanSelect.on('change', function () {
                const selected = $(this).val();
                populateDesa(selected);
            });
        });
    </script>
@endsection
