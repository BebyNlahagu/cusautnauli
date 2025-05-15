@extends('layouts.master')
@section('title', 'Halaman Petugas')
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
            <a href="">Profiel</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('petugas.index') }}">@yield('title')</a>
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
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <img src="{{ Route::url('images', $p->img) }}" alt="Profil" class="circle-rounded py-5" />
            </div>
        </div>
    </div>
    <div class="col-md-8">
       @foreach ($petugas as $p)
            <div class="card">
            <form action="{{ route('petugas.update', $p->id) }}" method="post" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="card-body">
                    <input type="hidden" name="user_id" id="user_id">

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror"
                            id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap"
                            value="{{ old('nama_lengkap') }}" />
                        <label for="floatingInput">Nama Lengkap</label>
                        @error('nama_lengkap')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <select class="form-control @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin"
                            name="jenis_kelamin" placeholder="Jenis kelamin" value="{{ old('jenis_kelamin') }}">
                            <option value="">--Pilih--</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                        <label for="floatingInput">Jenis Kelamin</label>
                        @error('jenis_kelamin')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="number" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp"
                            name="no_hp" placeholder="Jenis kelamin" value="{{ old('no_hp') }}">
                        <label for="floatingInput">No Hp/wa</label>
                        @error('no_hp')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating form-floating-custom mb-3">
                        <input type="file" class="form-control @error('img') is-invalid @enderror" id="img"
                            name="img" placeholder="Jenis kelamin" value="{{ old('img') }}">
                        <label for="floatingInput">No Hp/wa</label>
                        @error('img')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
       @endforeach
    </div>
</div>
@endsection