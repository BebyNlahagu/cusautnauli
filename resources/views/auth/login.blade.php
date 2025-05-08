@extends('layouts.auth')

@section('title','Halaman Login')
@section('content')
    <div class="container py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-md-4">
            <div class="card">
                <div class="row mb-3 justify-content-center align-items-center">
                    <img src="{{ asset('img/logo.png')}}" alt="logo" style="width: 120px;">
                </div>
                <div class="card-body">
                    <h1 class="text-center mb-3">LOGIN</h1>
                    <form id="loginForm" action="{{ route('login') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-2">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>

                        <div class="form-group mb-2">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Enter Password">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span id="icon_click" class="fas fa-eye text-info btn-sm p-2"
                                            style="cursor: pointer;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary mt-2 me-2">Gaskan</button>
                            <a href="{{ route('register') }}" class="btn btn-link mt-2">Register</a>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
