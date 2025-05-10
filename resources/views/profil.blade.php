@extends('layouts.master')
@section('title', 'Data Angsuran')
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
                <a href="{{ route('angsuran.index') }}">@yield('title')</a>
            </li>
        </ul>
    </div>
@endsection