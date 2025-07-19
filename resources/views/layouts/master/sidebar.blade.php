<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="{{ route('home')}}" class="logo">
                <img src="{{ asset('img/logo.png') }}" alt="navbar brand" class="navbar-brand center white" height="80" width="auto" style="filter: brightness(0) invert(1);" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                @if(Auth::user()->role == 'Admin')
                <li class="nav-item {{ \Route::is('home') ? 'active' : '' }}">
                    <a href="{{ route('home') }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                @elseif(Auth::user()->role == 'User')
                <li class="nav-item {{ \Route::is('user.edit') ? 'active' : '' }}">
                    <a href="{{ route('user.edit') }}">
                        <i class="fas fa-user"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                @endif

                @if (auth()->user()->role == "Admin")
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Components</h4>
                </li>
                <li class="nav-item {{ \Route::is('nasabah.index') ? 'active' : '' }}">
                    <a href="{{ route('nasabah.index') }}">
                        <i class="fas fa-users"></i>
                        <span>Anggota</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#base">
                        <i class="fas fa-layer-group"></i>
                        <p>Layanan</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="base">
                        <ul class="nav nav-collapse">
                            <li class="nav-item{{ \Route::is('simpanan.index') ? 'active' : '' }}">
                                <a href="{{ route('simpanan.index')}}">
                                    <span class="sub-item">Simpanan</span>
                                </a>
                            </li>
                            <li class="nav-item{{ \Route::is('pinjaman.index') ? 'active' : '' }}">
                                <a href="{{ route('pinjaman.index')}}">
                                    <span class="sub-item">Pinjaman</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('angsuran.index') }}">
                                    <span class="sub-item">Angsuran</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarLayouts">
                        <i class="fas fa-th-list"></i>
                        <p>Laporan</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="sidebarLayouts">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="{{ route('laporan.anggota') }}">
                                    <span class="sub-item">Laporan Anggota</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('laporan.simpanan') }}">
                                    <span class="sub-item">Laporan Simpanan</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('laporan.pinjaman') }}">
                                    <span class="sub-item">Laporan Pinjaman</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('laporan.angsuran') }}">
                                    <span class="sub-item">Laporan Angsuran</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @elseif (auth()->user()->role == 'Kepala')
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarLayouts">
                        <i class="fas fa-th-list"></i>
                        <p>Laporan</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="sidebarLayouts">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="{{ route('laporan.anggota') }}">
                                    <span class="sub-item">Laporan Anggota</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('laporan.simpanan') }}">
                                    <span class="sub-item">Laporan Simpanan</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('laporan.pinjaman') }}">
                                    <span class="sub-item">Laporan Pinjaman</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('laporan.angsuran') }}">
                                    <span class="sub-item">Laporan Angsuran</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @elseif (auth()->user()->role == 'User')
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#base">
                        <i class="fas fa-layer-group"></i>
                        <p>Layanan</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="base">
                        <ul class="nav nav-collapse">
                            <li class="nav-item{{ \Route::is('simpanan.index') ? 'active' : '' }}">
                                <a href="{{ route('simpanan.index')}}">
                                    <span class="sub-item">Simpanan</span>
                                </a>
                            </li>
                            <li class="nav-item{{ \Route::is('pinjaman.index') ? 'active' : '' }}">
                                <a href="{{ route('pinjaman.index')}}">
                                    <span class="sub-item">Pinjaman</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('angsuran.index') }}">
                                    <span class="sub-item">Angsuran</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
