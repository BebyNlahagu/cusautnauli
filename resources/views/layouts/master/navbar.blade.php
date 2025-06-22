<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
    <div class="container-fluid">
        <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
            <div class="input-group">
                <div class="input-group-prepend">
                    {{-- <button type="submit" class="btn btn-search pe-1">
                        <i class="fa fa-search search-icon"></i>
                    </button> --}}
                </div>

            </div>
        </nav>

        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
            <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" aria-haspopup="true">
                    <i class="fa fa-search"></i>
                </a>
                <ul class="dropdown-menu dropdown-search animated fadeIn">
                    <form class="navbar-left navbar-form nav-search">
                        <div class="input-group">
                            {{-- <input type="text" placeholder="Search ..." class="form-control" /> --}}
                        </div>
                    </form>
                </ul>
            </li>

            <li class="nav-item topbar-user dropdown hidden-caret">
                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                    <div class="avatar-sm">
                        {{-- @if ($petugas)
                        <img src="{{ asset('images/' . optional($petugas)->img) }}" alt=""
                        class="avatar-img rounded" />
                        @endif --}}
                    </div>
                    <span class="profile-username">
                        <span class="op-7">Hi,</span>
                        <span class="fw-bold">{{ Auth::user()->name }}</span>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <li>
                        <div class="dropdown-user-scroll scrollbar-outer">
                            <div class="user-box">
                                <div class="avatar-lg">
                                    {{-- @if ($petugas)
          <img src="{{ asset('storage/images/' . optional($petugas)->img) }}" alt="image profile"
                                    class="avatar-img rounded" />
                                    @endif --}}
                                </div>
                                <div class="u-text">
                                    <h4>{{ Auth::user()->name }}</h4>
                                    <p class="text-muted">{{ Auth::user()->email }}</p>
                                    <a href="{{ route('petugas.index') }}" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('petugas.index') }}">My Profile</a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" id="Logout" style="cursor:pointer;">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="post" style="display:none;">
                            @csrf
                        </form>
                    </li>
                </ul>

            </li>
        </ul>
    </div>
</nav>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $("#Logout").click(function(e) {
            e.preventDefault();
            Swal.fire({
                title: "Apa Kamu Yakin?"
                , text: "Kamu akan keluar dari akun ini!"
                , icon: "warning"
                , showCancelButton: true
                , cancelButtonText: "Batal"
                , confirmButtonText: "Ya, Logout!"
                , reverseButtons: true
                , customClass: {
                    confirmButton: "btn btn-success"
                    , cancelButton: "btn btn-danger"
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#logout-form").submit();
                }
            });
        });
    });

</script>
