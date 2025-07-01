@extends('layouts.auth')

@section('title', 'Halaman Login')
@section('content')
{{-- @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif --}}
<div class="container py-5 d-flex justify-content-center align-items-center min-vh-100">
    <div class="col-md-4">
        <div class="card">
            <div class="row mb-3 justify-content-center align-items-center">
                <img src="{{ asset('img/logo.png') }}" alt="logo" style="width: 120px;">
            </div>
            <div class="card-body">
                <h1 class="text-center mb-3">LOGIN</h1>
                <form id="loginForm" action="{{ route('login') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-2">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" autocomplete="off">
                        @error('username')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-2">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password" autocomplete="off">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span id="icon_click" class="fas fa-eye text-info btn-sm p-2" style="cursor: pointer;"></span>
                                </div>
                            </div>
                        </div>
                        @error('password')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary mt-2 me-2">Login</button>
                        {{-- <a href="{{ route('register') }}" class="btn btn-link mt-2">Register</a> --}}
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('swal'))
<script>
    Swal.fire({
        title: "{{ session('swal.title') }}",
        text: "{{ session('swal.text') }}",
        icon: "{{ session('swal.icon') }}",
        confirmButtonText: 'OK'
    });
</script>
@endif
<script>
    $(document).ready(function() {
        $('#icon_click').click(function() {
            $(this).toggleClass("fas fa-eye fas fa-eye-slash");
            var type = $(this).hasClass("fas fa-eye-slash") ? "text" : "password";
            $("#password").attr("type", type);
        });
    });

</script>
@endsection
