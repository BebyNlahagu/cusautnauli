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
                            <select name="alamat_id" id="alamat_id" class="form-control @error('alamat_id') is-invalid @enderror">
                                <option value="">-pilih-</option>
                                @foreach ($alamat as $a)
                                    <option value="{{ $a->id }}"
                                        {{ (old('alamat_id', $user->alamat_id ?? '') == $a->id) ? 'selected' : '' }}>
                                        {{ $a->alamat }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="floatingInput">Alamat</label>
                            @error('alamat_id')
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
            
                        <div class="form-floating form-floating-custom mb-3">
                            <input type="text" disabled name="pekerjaan" class="form-control @error('pekerjaan') is-invalid @enderror"
                                id="pekerjaan" placeholder="Jenis Usaha" value="{{ $user->pekerjaan }}" />
                            <label for="floatingInput">Pekerjaan</label>
                            @error('pekerjaan')
                                {{-- <div class="invalid-feedback">{{ $message }}</div> --}}
                            @enderror
                        </div>
                    
                        {{-- <div class="form-floating form-floating-custom mb-3">
                            <input type="file" name="foto" disabled class="form-control @error('foto') is-invalid @enderror"
                                id="foto" />
                            <label for="floatingInput">Foto Diri</label>
                            <img id="fotoPreview" src="{{ old('foto', isset($user->foto) ? asset('images/' . $user->foto) : '#') }}"
                                alt="Foto Preview"
                                style="max-width: 200px; margin-top: 10px; display: {{ isset($user->foto) ? 'block' : 'none' }};" />
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
            
                        <div class="form-floating form-floating-custom mb-3">
                            <input type="file" disabled name="ktp" class="form-control @error('ktp') is-invalid @enderror"
                                id="ktp" />
                            <label for="floatingInput">KTP</label>
                            <img id="ktpPreview" src="{{ old('ktp', isset($user->ktp) ? asset('images/' . $user->ktp) : '#') }}"
                                alt="KTP Preview"
                                style="max-width: 200px; margin-top: 10px; display: {{ isset($user->ktp) ? 'block' : 'none' }};" />
                            @error('ktp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
            
                        <div class="form-floating form-floating-custom mb-3">
                            <input type="file" disabled name="kk" class="form-control @error('kk') is-invalid @enderror"
                                id="kk" />
                            <label for="floatingInput">Kartu Keluarga</label>
                            <img id="kkPreview" src="{{ old('kk', isset($user->kk) ? asset('images/' . $user->kk) : '#') }}"
                                alt="KK Preview"
                                style="max-width: 200px; margin-top: 10px; display: {{ isset($user->kk) ? 'block' : 'none' }};" />
                            @error('kk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> --}}

                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                    </div>
                </form>
            </div>
        @endif
    </div>
@endsection
