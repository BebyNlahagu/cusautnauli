<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
    <div class="container-fluid">

        {{-- Left side (search) --}}
        <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
            <div class="input-group">
                <div class="input-group-prepend">
                    {{-- Search button (optional) --}}
                </div>
            </div>
        </nav>

        {{-- Right side --}}
        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

            {{-- Notifikasi khusus untuk User --}}
            @if(Auth::user()->role === 'User')
            @php
                $unreadNotifications = Auth::user()->unreadNotifications;
                $unreadCount = $unreadNotifications->count();
            @endphp

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle position-relative" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-bell"></i>
                    @if($unreadCount > 0)
                    <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                        {{ $unreadCount }}
                    </span>
                    @endif
                </a>

                <ul class="dropdown-menu dropdown-menu-end animated fadeIn" aria-labelledby="notifDropdown" style="width: 300px;">
                    <li class="dropdown-header">Notifikasi</li>

                    @forelse ($unreadNotifications as $notification)
                    <li class="dropdown-item notification-item" data-id="{{ $notification->id }}" style="cursor: pointer;">
                        <div class="text-box">
                            <small class="text-muted">{{ \Carbon\Carbon::parse($notification->data['time'])->diffForHumans() }}</small><br>
                        {{ $notification->data['message'] }}
                        </div>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    @empty
                    <li class="dropdown-item text-center text-muted">Tidak ada notifikasi baru</li>
                    @endforelse
                </ul>

            </li>
            @endif

            {{-- User dropdown --}}
            <li class="nav-item topbar-user dropdown hidden-caret">
                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                    <div class="avatar-sm">
                        {{-- Avatar user --}}
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
                                    {{-- Avatar --}}
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
                    @if (auth()->user()->role == "Admin")
                        <li><a class="dropdown-item" href="{{ route('petugas.index') }}">My Profile</a></li>
                    @elseif (auth()->user()->role == "User")
                        <li><a class="dropdown-item" href="{{ route('user.edit') }}">My Profile</a></li>
                    @endif
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('/assets/js/plugin/datatables/datatables.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
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

            $('.notification-item').on('click', function() {
                var $this = $(this);
                var notifId = $this.data('id');

                $.ajax({
                    url: '/notifications/' + notifId + '/mark-as-read'
                    , type: 'POST'
                    , headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                    , success: function(response) {
                        if (response.status === 'success') {
                            // Kurangi angka badge
                            var badge = $('#notifDropdown .badge');
                            var currentCount = parseInt(badge.text());

                            if (currentCount > 1) {
                                badge.text(currentCount - 1);
                            } else {
                                badge.remove();
                            }
                        }
                    }
                });
            });
    });
</script>
