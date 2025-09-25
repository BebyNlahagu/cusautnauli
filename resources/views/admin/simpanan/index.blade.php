@extends('layouts.master')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@section('title', 'Data Simpanan')
@section('bread')
    <div class="page-header">
        <h3 class="fw-bold mb-3">
            @if (Auth::check() && Auth::user()->role === 'User')
                {{ Auth::user()->nm_koperasi }}
            @endif
        </h3>
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
                <a href="">Simpanan</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('simpanan.index') }}">@yield('title')</a>
            </li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">@yield('title')</h4>
                    @if (auth()->user()->role == 'Admin')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah">
                            <span class="btn-label"><i class="fa fa-plus"></i></span>Add
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="basic-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    @if (auth()->user()->role === 'Admin')
                                        <th>Nama</th>
                                    @endif
                                    <th>Bulan Transaksi Terakhir</th>
                                    <th>Jumlah Simpanan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($simpananGrouped as $index => $s)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        @if (auth()->user()->role === 'Admin')
                                            <td>{{ $s['user']->name }}</td>
                                        @endif
                                        <td>{{ $s['tanggal_transaksi'] }}</td>
                                        <td>Rp {{ number_format($s['total_simpanan'], 0, ',', '.') }}</td>
                                        <td>
                                            <button type="button" class="btn btn-link btn-success view-simpanan-btn"
                                                title="Detail" data-user="{{ $s['user']->name }}"
                                                data-id="{{ $s['user']->id }}">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="{{ auth()->check() && auth()->user()->role === 'Admin' ? 3 : 2 }}"
                                        class="text-center">
                                        Jumlah Kapitalisasi
                                    </td>
                                    <td class="text-end">Rp {{ number_format($kapitalisasi, 0, ',', '.') }}</td>
                                </tr>
                                @if (auth()->user()->role == 'Admin')
                                    <tr>
                                        <td colspan="3" class="text-center fw-bolder">Total Simpanan</td>
                                        <td class="text-end fw-bold">Rp. {{ number_format($jumlah, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah data --}}
    <div class="modal fade" id="tambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Data Simpanan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('simpanan.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating form-floating-custom mb-3">
                            <select class="form-control select2 @error('user_id') is-invalid @enderror" id="user_id"
                                name="user_id" style="width: 100%;">
                                <option value="">--Pilih Nomor Anggota--</option>
                                @foreach ($nasabah->whereNotNull('nm_koperasi') as $n)
                                    <option value="{{ $n->id }}">{{ $n->nm_koperasi }} - {{ $n->name }}
                                    </option>
                                @endforeach
                            </select>
                            <!-- <label for="user_id">Pilih Nomor Anggota</label> -->
                        </div>
                        <div class="form-floating form-floating-custom mb-3">
                            <select class="form-control @error('jenis_simpanan') is-invalid @enderror"
                                onchange="setJumlahSimpanan()" id="jenis_simpanan" name="jenis_simpanan">
                                <option value="">Pilih Jenis Simpanan</option>
                                <option value="Simpanan Wajib">Simpanan Wajib</option>
                                <option value="Simpanan Dakesma">Simpanan Dakesma</option>
                            </select>
                            <label for="jenis_simpanan">Jenis Simpanan</label>
                        </div>
                        <div class="form-floating form-floating-custom mb-3">
                            <input type="text" id="jumlah_simpanan_display" oninput="formatUang(this)"
                                class="form-control @error('jumlah_simpanan') is-invalid @enderror" readonly
                                placeholder="Jumlah Simpanan"
                                value="{{ old('jumlah_simpanan') ? 'Rp ' . number_format(old('jumlah_simpanan'), 0, ',', '.') : '' }}" />
                            <input type="hidden" readonly name="jumlah_simpanan"
                                class="form-control @error('jumlah_simpanan') is-invalid @enderror" id="jumlah_simpanan"
                                value="{{ old('jumlah_simpanan') }}" />
                            <label for="floatingInput">Jumlah Simpanan</label>
                            @error('jumlah_simpanan')
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

    <!-- Modal Detail Simpanan -->
    <div class="modal fade" id="modalDetailSimpanan" tabindex="-1" aria-labelledby="modalDetailSimpananLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailSimpananLabel">Detail Simpanan <span
                            id="namaUserDetail"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Simpanan</th>
                                <th>Jumlah Simpanan</th>
                                <th>Bulan</th>
                                <th>Status</th>
                                @if (auth()->user()->role == 'Admin')
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="detailSimpananBody">
                            <!-- Data simpanan user akan diisi lewat JS -->
                        </tbody>
                    </table>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnPayAll">
                            <i class="fa fa-credit-card"></i> Bayar Semua
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <script>
            Swal.fire({
                title: "Berhasil!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "OK"
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: "Gagal!",
                text: "{{ session('error') }}",
                icon: "error",
                confirmButtonText: "Ok"
            });
        </script>
    @endif

    @if (session('delete'))
        <script>
            Swal.fire({
                title: "Dihapus!",
                text: "{{ session('delete') }}",
                icon: "warning",
                confirmButtonText: "OK"
            });
        </script>
    @endif

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.midtrans.client_key') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const currentUserRole = "{{ auth()->user()->role }}";

        $(document).ready(function() {
            $("#basic-datatables").DataTable();

            $('#user_id').select2({
                dropdownParent: $('#tambah')
            });

            // Event tombol detail
            $('.view-simpanan-btn').on('click', function() {
                var userId = $(this).data('id');
                var userName = $(this).data('user');
                $('#namaUserDetail').text(userName);

                $.ajax({
                    url: '/api/simpanan/' + userId,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var tbody = $('#detailSimpananBody');
                        tbody.empty();

                        if (data.simpanan.length === 0) {
                            tbody.append(
                                '<tr><td colspan="6" class="text-center">Tidak ada data simpanan.</td></tr>'
                            );
                            return;
                        }

                        $.each(data.simpanan, function(index, item) {
                            var tanggal = new Date(item.tanggal + 'T00:00:00')
                                .toLocaleDateString('id-ID', {
                                    month: 'long',
                                });

                            var statusBadge = item.status === 'Lunas' ?
                                '<span class="badge bg-success">Lunas</span>' :
                                '<span class="badge bg-warning text-dark">Belum Lunas</span>';

                            // Kolom aksi sesuai role
                            let actionColumn = '';

                            if (currentUserRole === 'User') {
                                actionColumn = `
                                <td>
                                    <button class="btn btn-primary btn-sm btn-pay"
                                            data-id="${item.id}"
                                            data-amount="${item.jumlah_simpanan}">
                                        Pay
                                    </button>
                                </td>
                            `;
                            } else if (currentUserRole === 'Admin') {
                                if (item.status === 'Belum Lunas') {
                                    actionColumn = `
                                        <td class="d-flex align-items-center gap-2">
                                            <button class="btn btn-success btn-sm btn-konfirmasi"
                                                    data-id="${item.id}"
                                                    data-amount="${item.jumlah_simpanan}">
                                                Confirm
                                            </button>
                                            <button class="btn btn-danger btn-sm btn-hapus"
                                                    data-id="${item.id}">
                                                Hapus
                                            </button>
                                        </td>
                                    `;
                                } else {
                                    actionColumn = `
                                        <td>
                                            <button class="btn btn-danger btn-sm btn-hapus" data-id="${item.id}">Hapus</button>
                                        </td>
                                    `;
                                }

                            } else {
                                actionColumn = `<td>-</td>`;
                            }

                            var row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.jenis_simpanan}</td>
                                <td>Rp ${Number(item.jumlah_simpanan).toLocaleString('id-ID')}</td>
                                <td>${tanggal}</td>
                                <td>${statusBadge}</td>
                                ${actionColumn}
                            </tr>
                        `;
                            tbody.append(row);
                        });

                        var modal = new bootstrap.Modal(document.getElementById(
                            'modalDetailSimpanan'));
                        modal.show();

                        // Event konfirmasi (Admin)
                        tbody.off('click', '.btn-konfirmasi').on('click', '.btn-konfirmasi',
                            function() {
                                var simpananId = $(this).data('id');

                                Swal.fire({
                                    title: 'Konfirmasi simpanan ini sebagai Lunas?',
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonText: 'Ya, konfirmasi',
                                    cancelButtonText: 'Batal'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.ajax({
                                            url: '/simpanan/konfirmasi/' +
                                                simpananId,
                                            type: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            success: function(resp) {
                                                if (resp.success) {
                                                    Swal.fire(
                                                        'Berhasil!',
                                                        'Simpanan dikonfirmasi.',
                                                        'success');
                                                    $('.view-simpanan-btn[data-id="' +
                                                            userId +
                                                            '"]')
                                                        .click();
                                                    location.reload();
                                                } else {
                                                    Swal.fire('Gagal!',
                                                        resp
                                                        .message ||
                                                        'Gagal mengkonfirmasi.',
                                                        'error');
                                                }
                                            },
                                            error: function() {
                                                Swal.fire('Error!',
                                                    'Terjadi kesalahan saat konfirmasi.',
                                                    'error');
                                            }
                                        });
                                    }
                                });
                            });

                        // Event hapus (Admin)
                        tbody.off('click', '.btn-hapus').on('click', '.btn-hapus', function() {
                            var simpananId = $(this).data('id');

                            Swal.fire({
                                title: 'Yakin ingin menghapus simpanan ini?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, hapus!',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: '/simpanan/' + simpananId,
                                        type: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        success: function(resp) {
                                            if (resp.success) {
                                                Swal.fire(
                                                    'Terhapus!',
                                                    'Data berhasil dihapus.',
                                                    'success');
                                                $('.view-simpanan-btn[data-id="' +
                                                        userId +
                                                        '"]')
                                                    .click();
                                                location.reload();
                                            } else {
                                                Swal.fire('Gagal!',
                                                    'Gagal menghapus data.',
                                                    'error');
                                            }
                                        },
                                        error: function() {
                                            Swal.fire('Error!',
                                                'Terjadi kesalahan.',
                                                'error');
                                        }
                                    });
                                }
                            });
                        });

                        tbody.off('click', '.btn-pay').on('click', '.btn-pay', function() {
                            var simpananId = $(this).data('id');

                            Swal.fire({
                                title: 'Lanjutkan pembayaran simpanan ini?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, bayar',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: "{{ url('/simpanan/paid') }}/" +
                                            simpananId,
                                        type: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        success: function(res) {
                                            if (res.snap_token) {
                                                window.snap.pay(res
                                                    .snap_token, {
                                                        onSuccess: function(
                                                            result
                                                        ) {
                                                            $.ajax({
                                                                url: '/simpanan/paid/' +
                                                                    simpananId,
                                                                type: 'POST',
                                                                headers: {
                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                                },
                                                                success: function(
                                                                    resp
                                                                ) {
                                                                    if (resp
                                                                        .success
                                                                    ) {
                                                                        Swal.fire(
                                                                            'Berhasil!',
                                                                            'Pembayaran sukses.',
                                                                            'success'
                                                                        );
                                                                        $('.view-simpanan-btn[data-id="' +
                                                                                userId +
                                                                                '"]'
                                                                            )
                                                                            .click();
                                                                        location
                                                                            .reload();
                                                                    } else {
                                                                        Swal.fire(
                                                                            'Gagal!',
                                                                            resp
                                                                            .message,
                                                                            'error'
                                                                        );
                                                                    }
                                                                }
                                                            });
                                                        },
                                                        onPending: function(
                                                            result
                                                        ) {
                                                            Swal.fire(
                                                                'Menunggu pembayaran.',
                                                                '',
                                                                'info'
                                                            );
                                                        },
                                                        onError: function(
                                                            result
                                                        ) {
                                                            Swal.fire(
                                                                'Error!',
                                                                'Pembayaran gagal.',
                                                                'error'
                                                            );
                                                        },
                                                        onClose: function() {
                                                            Swal.fire(
                                                                'Dibatalkan',
                                                                'Anda menutup popup pembayaran.',
                                                                'warning'
                                                            );
                                                        }
                                                    });
                                            } else {
                                                Swal.fire('Error!',
                                                    'Token pembayaran tidak valid.',
                                                    'error');
                                            }
                                        },
                                        error: function() {
                                            Swal.fire('Error!',
                                                'Gagal membuat transaksi.',
                                                'error');
                                        }
                                    });
                                }
                            });
                        });

                        // Event tombol "Bayar Semua"
                        $('#btnPayAll').off('click').on('click', function() {
                            let belumLunasIds = [];
                            data.simpanan.forEach(item => {
                                if (item.status === 'Belum Lunas') {
                                    belumLunasIds.push(item.id);
                                }
                            });

                            if (belumLunasIds.length === 0) {
                                Swal.fire('Info',
                                    'Tidak ada simpanan yang belum lunas.', 'info');
                                return;
                            }

                            Swal.fire({
                                title: 'Bayar semua simpanan Belum Lunas?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, bayar semua',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: "{{ route('simpanan.payAll') }}",
                                        type: 'POST',
                                        data: {
                                            ids: belumLunasIds
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        success: function(res) {
                                            if (res.snap_token) {
                                                window.snap.pay(res
                                                    .snap_token, {
                                                        onSuccess: function(
                                                            result
                                                            ) {
                                                            $.ajax({
                                                                url: "{{ url('/simpanan/pay-all/success') }}",
                                                                type: 'POST',
                                                                data: {
                                                                    ids: belumLunasIds
                                                                },
                                                                headers: {
                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                                },
                                                                success: function(
                                                                    resp
                                                                    ) {
                                                                    Swal.fire(
                                                                        'Berhasil!',
                                                                        'Semua simpanan berhasil dibayar.',
                                                                        'success'
                                                                        );
                                                                    $('.view-simpanan-btn[data-id="' +
                                                                            userId +
                                                                            '"]'
                                                                            )
                                                                        .click();
                                                                    location
                                                                        .reload();
                                                                },
                                                                error: function() {
                                                                    Swal.fire(
                                                                        'Error!',
                                                                        'Gagal update status simpanan.',
                                                                        'error'
                                                                        );
                                                                }
                                                            });
                                                        },
                                                        onPending: function(
                                                            result
                                                            ) {
                                                            Swal.fire(
                                                                'Menunggu pembayaran.',
                                                                '',
                                                                'info'
                                                                );
                                                        },
                                                        onError: function(
                                                            result
                                                            ) {
                                                            Swal.fire(
                                                                'Error!',
                                                                'Pembayaran gagal.',
                                                                'error'
                                                                );
                                                        },
                                                        onClose: function() {
                                                            Swal.fire(
                                                                'Dibatalkan',
                                                                'Anda menutup popup pembayaran.',
                                                                'warning'
                                                                );
                                                        }
                                                    });
                                            } else {
                                                Swal.fire('Error!',
                                                    'Token pembayaran tidak valid.',
                                                    'error');
                                            }
                                        },
                                        error: function() {
                                            Swal.fire('Error!',
                                                'Gagal membuat transaksi.',
                                                'error');
                                        }
                                    });
                                }
                            });
                        });


                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal mengambil data simpanan.', 'error');
                    }
                });
            });
        });

        // Fungsi format uang Rp
        function formatUang(input) {
            let value = input.value.replace(/\D+/g, '');
            if (value.length > 14) value = value.slice(0, 14);
            let formatted = 'Rp ' + value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            input.value = formatted;
            document.getElementById('jumlah_simpanan').value = value;
        }

        function setJumlahSimpanan() {
            const jenis = document.getElementById('jenis_simpanan').value;
            const jumlahInput = document.getElementById('jumlah_simpanan_display');

            const awal = 50000;
            const potongan = awal * 0.02;
            const jumlah = awal - potongan;

            if (jenis) {
                jumlahInput.value = jumlah;
                document.getElementById('jumlah_simpanan').value = jumlah;
                formatUang(jumlahInput);
            } else {
                jumlahInput.value = '';
                document.getElementById('jumlah_simpanan').value = '';
            }
        }
    </script>


@endsection
